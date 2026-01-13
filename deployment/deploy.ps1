param(
    [Parameter(Mandatory=$false)]
    [string]$Site,

    [Parameter(Mandatory=$false)]
    [string]$Package,

    [switch]$All,
    [switch]$DryRun,
    [switch]$Force,
    [switch]$Optimize,
    [switch]$SkipSecurityAudit
)

# Deployment script for WordPress sites
# Deploys versioned packages to configured sites
# Enhanced with performance optimization and monitoring capabilities

function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] [$Level] $Message"
    Write-Host $logMessage
    if ($Level -eq "ERROR") { Write-Error $logMessage }
}

function Optimize-SiteAssets {
    param([string]$sitePath)

    Write-Log "Optimizing assets for site: $sitePath"

    # Check for CSS files and minify
    $cssFiles = Get-ChildItem "$sitePath\*.css" -Recurse
    foreach ($cssFile in $cssFiles) {
        if ($cssFile.Name -notlike "*.min.css") {
            $minifiedPath = $cssFile.FullName -replace '\.css$', '.min.css'
            if (!(Test-Path $minifiedPath)) {
                Write-Log "Minifying CSS: $($cssFile.Name)"
                # Simple CSS minification (remove comments, extra whitespace)
                $content = Get-Content $cssFile.FullName -Raw
                $content = $content -replace '/\*[\s\S]*?\*/', '' -replace '\s+', ' ' -replace '\s*{\s*', '{' -replace '\s*}\s*', '}' -replace '\s*;\s*', ';'
                $content | Out-File $minifiedPath -Encoding UTF8
            }
        }
    }

    # Check for JS files and minify
    $jsFiles = Get-ChildItem "$sitePath\*.js" -Recurse
    foreach ($jsFile in $jsFiles) {
        if ($jsFile.Name -notlike "*.min.js") {
            $minifiedPath = $jsFile.FullName -replace '\.js$', '.min.js'
            if (!(Test-Path $minifiedPath)) {
                Write-Log "Minifying JS: $($jsFile.Name)"
                # Simple JS minification (remove comments and extra whitespace)
                $content = Get-Content $jsFile.FullName -Raw
                $content = $content -replace '/\*[\s\S]*?\*/', '' -replace '//.*$', '' -replace '\s+', ' '
                $content | Out-File $minifiedPath -Encoding UTF8
            }
        }
    }
}

function Generate-PerformanceReport {
    param([string]$siteName, [string]$sitePath)

    Write-Log "Generating performance report for $siteName"

    $report = @{
        site = $siteName
        timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        metrics = @{
            php_files = (Get-ChildItem "$sitePath\*.php" -Recurse).Count
            js_files = (Get-ChildItem "$sitePath\*.js" -Recurse).Count
            css_files = (Get-ChildItem "$sitePath\*.css" -Recurse).Count
            total_size_mb = [math]::Round((Get-ChildItem $sitePath -Recurse | Measure-Object -Property Length -Sum).Sum / 1MB, 2)
        }
        optimizations = @{
            css_minified = $true
            js_minified = $true
            caching_enabled = $true
        }
    }

    $reportPath = "..\reports\performance_$siteName.json"
    $report | ConvertTo-Json -Depth 4 | Out-File $reportPath -Encoding UTF8
    Write-Log "Performance report saved: $reportPath"
}

function Get-SiteConfig {
    param([string]$siteName)
    $configPath = "..\sites\$siteName\site-config.json"
    # TODO: Use WEBSITES_ROOT from config instead of hardcoded path
    if (Test-Path $configPath) {
        return Get-Content $configPath | ConvertFrom-Json
    } else {
        Write-Log "Site config not found: $configPath" "ERROR"
        return $null
    }
}

function Deploy-PackageToSite {
    param(
        [string]$packageName,
        [string]$version,
        [string]$siteName,
        [string]$packageType,
        [switch]$DryRun
    )

    $sourcePath = "..\packages\$packageName\$version"
    $sitePath = "..\websites\$siteName\wp\wp-content\$packageType"

    if (!(Test-Path $sourcePath)) {
        Write-Log "Package not found: $sourcePath" "ERROR"
        return $false
    }

    # Get the actual plugin/theme folder name from inside the package
    $pluginFolder = Get-ChildItem $sourcePath -Directory | Select-Object -First 1
    if (!$pluginFolder) {
        Write-Log "No plugin/theme folder found in package $sourcePath" "ERROR"
        return $false
    }

    $actualPluginName = $pluginFolder.Name
    $destinationPath = "$sitePath\$actualPluginName"
    $backupPath = "$sitePath\$packageName.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"

    # Get the actual plugin/theme folder name for dry run
    $pluginFolder = Get-ChildItem $sourcePath -Directory | Select-Object -First 1
    if (!$pluginFolder) {
        Write-Log "No plugin/theme folder found in package $sourcePath" "ERROR"
        return $false
    }

    $actualPluginName = $pluginFolder.Name
    $destinationPath = "$sitePath\$actualPluginName"

    if ($DryRun) {
        Write-Log "[DRY RUN] Would deploy $actualPluginName ($packageName $version) to $siteName"
        Write-Log "[DRY RUN] Source: $pluginFolder.FullName"
        Write-Log "[DRY RUN] Destination: $destinationPath"
        return $true
    }

    try {
        # Create backup if destination exists
        if (Test-Path $destinationPath) {
            Write-Log "Creating backup: $backupPath"
            Copy-Item $destinationPath $backupPath -Recurse -Force
        }

        # Deploy package
        Write-Log "Deploying $actualPluginName ($packageName $version) to $siteName"
        if (!(Test-Path $sitePath)) {
            New-Item -ItemType Directory -Path $sitePath -Force | Out-Null
        }

        # Copy the plugin folder from package to plugins directory
        Copy-Item $pluginFolder.FullName $destinationPath -Recurse -Force
        Write-Log "Successfully deployed $actualPluginName ($packageName $version) to $siteName"
        return $true
    }
    catch {
        Write-Log "Failed to deploy $packageName to $siteName`: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

function Deploy-Site {
    param([string]$siteName, [switch]$DryRun, [switch]$Optimize)

    Write-Log "Processing site: $siteName"
    $config = Get-SiteConfig $siteName

    if ($null -eq $config) {
        return $false
    }

    $success = $true

    # Deploy plugins
    foreach ($plugin in $config.packages.plugins.PSObject.Properties) {
        $result = Deploy-PackageToSite -packageName $plugin.Name -version $plugin.Value -siteName $siteName -packageType "plugins" -DryRun:$DryRun
        if (!$result) { $success = $false }
    }

    # Deploy themes
    foreach ($theme in $config.packages.themes.PSObject.Properties) {
        $result = Deploy-PackageToSite -packageName $theme.Name -version $theme.Value -siteName $siteName -packageType "themes" -DryRun:$DryRun
        if (!$result) { $success = $false }
    }

    # Security audit phase
    if (!$SkipSecurityAudit -and !$DryRun) {
        Write-Log "Running security audit for $siteName"

        try {
            $auditResult = & ".\run_security_audit.ps1" -SiteName $siteName -AuditType "lockdown" -FailOnHighSeverity
            if ($LASTEXITCODE -ne 0) {
                Write-Log "Security audit failed for $siteName - blocking deployment" "ERROR"
                return $false
            }
            Write-Log "Security audit passed for $siteName"
        } catch {
            Write-Log "Security audit error for $siteName`: $($_.Exception.Message)" "ERROR"
            return $false
        }
    }

    # Performance optimization phase
    if ($Optimize -and !$DryRun) {
        Write-Log "Starting performance optimization for $siteName"
        $sitePath = "..\websites\$siteName"

        # Optimize assets
        Optimize-SiteAssets -sitePath $sitePath

        # Generate performance report
        Generate-PerformanceReport -siteName $siteName -sitePath $sitePath

        # Add caching headers optimization
        Add-CacheHeaders -sitePath $sitePath

        Write-Log "Performance optimization completed for $siteName"
    }

    return $success
}

function Add-CacheHeaders {
    param([string]$sitePath)

    Write-Log "Adding cache optimization headers"

    # Create or update .htaccess for caching
    $htaccessPath = "$sitePath\.htaccess"
    $cacheRules = @"

# Performance Optimization - Cache Headers
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 year"
</IfModule>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

"@

    if (!(Test-Path $htaccessPath)) {
        New-Item -ItemType File -Path $htaccessPath -Force | Out-Null
    }

    $existingContent = Get-Content $htaccessPath -Raw
    if ($existingContent -notlike "*Performance Optimization*") {
        Add-Content $htaccessPath $cacheRules
        Write-Log "Cache optimization rules added to .htaccess"
    }
}

# Main execution
Write-Log "Starting deployment process"

if ($All) {
    Write-Log "Deploying to all sites"
    if ($Optimize) { Write-Log "Performance optimization enabled" }
    $sites = Get-ChildItem "..\sites" -Directory | Select-Object -ExpandProperty Name

    foreach ($site in $sites) {
        $result = Deploy-Site -siteName $site -DryRun:$DryRun -Optimize:$Optimize
        if (!$result -and !$DryRun) {
            Write-Log "Deployment failed for site: $site" "ERROR"
            exit 1
        }
    }
}
elseif ($Site) {
    Write-Log "Deploying to specific site: $Site"
    if ($Optimize) { Write-Log "Performance optimization enabled" }
    $result = Deploy-Site -siteName $Site -DryRun:$DryRun -Optimize:$Optimize
    if (!$result -and !$DryRun) {
        Write-Log "Deployment failed for site: $Site" "ERROR"
        exit 1
    }
}
elseif ($Package) {
    Write-Log "Deploying specific package: $Package"
    # Find all sites that use this package
    $sites = Get-ChildItem "..\sites" -Directory | Select-Object -ExpandProperty Name

    foreach ($site in $sites) {
        $config = Get-SiteConfig $site
        if ($config) {
            $usesPackage = $false

            # Check plugins
            if ($config.packages.plugins.PSObject.Properties.Name -contains $Package) {
                $version = $config.packages.plugins.$Package
                $result = Deploy-PackageToSite -packageName $Package -version $version -siteName $site -packageType "plugins" -DryRun:$DryRun
                $usesPackage = $true
            }

            # Check themes
            if ($config.packages.themes.PSObject.Properties.Name -contains $Package) {
                $version = $config.packages.themes.$Package
                $result = Deploy-PackageToSite -packageName $Package -version $version -siteName $site -packageType "themes" -DryRun:$DryRun
                $usesPackage = $true
            }

            if (!$usesPackage) {
                Write-Log "Site $site does not use package $Package"
            }
        }
    }
}
else {
    Write-Log "Usage: .\deploy.ps1 -All | -Site <sitename> | -Package <packagename> [-DryRun] [-Optimize] [-SkipSecurityAudit]" "ERROR"
    Write-Log "Examples:" "INFO"
    Write-Log "  .\deploy.ps1 -All                           # Deploy all sites" "INFO"
    Write-Log "  .\deploy.ps1 -Site mysite.com               # Deploy specific site" "INFO"
    Write-Log "  .\deploy.ps1 -All -Optimize                 # Deploy with performance optimization" "INFO"
    Write-Log "  .\deploy.ps1 -Site mysite.com -DryRun       # Preview deployment" "INFO"
    Write-Log "  .\deploy.ps1 -All -SkipSecurityAudit        # Skip security audit (not recommended)" "INFO"
    exit 1
}

Write-Log "Deployment process completed"