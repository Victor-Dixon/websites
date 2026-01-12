param(
    [Parameter(Mandatory=$false)]
    [string]$Site,

    [Parameter(Mandatory=$false)]
    [string]$Package,

    [switch]$All,
    [switch]$DryRun,
    [switch]$Force
)

# Deployment script for WordPress sites
# Deploys versioned packages to configured sites

function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] [$Level] $Message"
    Write-Host $logMessage
    if ($Level -eq "ERROR") { Write-Error $logMessage }
}

function Get-SiteConfig {
    param([string]$siteName)
    $configPath = "..\sites\$siteName\site-config.json"
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
    param([string]$siteName, [switch]$DryRun)

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

    return $success
}

# Main execution
Write-Log "Starting deployment process"

if ($All) {
    Write-Log "Deploying to all sites"
    $sites = Get-ChildItem "..\sites" -Directory | Select-Object -ExpandProperty Name

    foreach ($site in $sites) {
        $result = Deploy-Site -siteName $site -DryRun:$DryRun
        if (!$result -and !$DryRun) {
            Write-Log "Deployment failed for site: $site" "ERROR"
            exit 1
        }
    }
}
elseif ($Site) {
    Write-Log "Deploying to specific site: $Site"
    $result = Deploy-Site -siteName $Site -DryRun:$DryRun
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
    Write-Log "Usage: .\deploy.ps1 -All | -Site <sitename> | -Package <packagename> [-DryRun]" "ERROR"
    exit 1
}

Write-Log "Deployment process completed"