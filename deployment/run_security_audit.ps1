param(
    [Parameter(Mandatory=$true)]
    [string]$SiteName,

    [Parameter(Mandatory=$false)]
    [string]$AuditType = "lockdown",

    [switch]$FailOnHighSeverity,

    [switch]$GenerateReport
)

# Security Audit CI/CD Integration Script
# Runs automated security audits during deployment pipeline

Write-Host "🔒 SECURITY AUDIT CI/CD INTEGRATION" -ForegroundColor Cyan
Write-Host "===================================" -ForegroundColor Cyan

$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$auditDir = "..\reports\security_audits"
$reportFile = "$auditDir\ci_$SiteName`_$timestamp.json"

# Ensure reports directory exists
if (!(Test-Path $auditDir)) {
    New-Item -ItemType Directory -Path $auditDir -Force | Out-Null
}

# Check if audit runner exists
$auditRunner = "..\..\..\Agent_Cellphone_V2_Repository\tools\security_audit_runner.py"
if (!(Test-Path $auditRunner)) {
    Write-Error "Security audit runner not found: $auditRunner"
    exit 1
}

# Run the security audit
Write-Host "Running $AuditType audit for $SiteName..." -ForegroundColor Yellow

try {
    $auditOutput = & python $auditRunner --audit-type $AuditType --code-path "..\websites\$SiteName" 2>&1
    $exitCode = $LASTEXITCODE

    Write-Host "Audit completed with exit code: $exitCode" -ForegroundColor Gray

    # Parse audit results (simplified - in real implementation, parse JSON output)
    $auditResults = @{
        site = $SiteName
        audit_type = $AuditType
        timestamp = Get-Date -Format "yyyy-MM-ddTHH:mm:ssZ"
        exit_code = $exitCode
        output = $auditOutput
        ci_pipeline = $true
    }

    # Check for critical issues
    $criticalCount = ($auditOutput | Select-String -Pattern "ship_blockers.*CRITICAL" | Measure-Object).Count
    $highCount = ($auditOutput | Select-String -Pattern "severity.*HIGH" | Measure-Object).Count

    $auditResults.critical_issues = $criticalCount
    $auditResults.high_issues = $highCount

    # Export results
    $auditResults | ConvertTo-Json -Depth 4 | Out-File $reportFile -Encoding UTF8

    Write-Host "Audit results saved to: $reportFile" -ForegroundColor Green

    # CI/CD Logic
    if ($FailOnHighSeverity -and ($criticalCount -gt 0 -or $highCount -gt 0)) {
        Write-Host "❌ SECURITY AUDIT FAILED: Found $criticalCount critical and $highCount high severity issues" -ForegroundColor Red
        Write-Host "📋 Review audit report: $reportFile" -ForegroundColor Yellow
        exit 1
    }

    if ($criticalCount -gt 0) {
        Write-Host "🚨 CRITICAL SECURITY ISSUES FOUND: $criticalCount" -ForegroundColor Red
        Write-Host "⚠️  Deployment allowed but security review required" -ForegroundColor Yellow
    } elseif ($highCount -gt 0) {
        Write-Host "⚠️  HIGH SEVERITY ISSUES FOUND: $highCount" -ForegroundColor Yellow
        Write-Host "✅ Deployment allowed with security monitoring" -ForegroundColor Green
    } else {
        Write-Host "✅ SECURITY AUDIT PASSED: No critical or high severity issues" -ForegroundColor Green
    }

    # Generate summary report if requested
    if ($GenerateReport) {
        $summaryFile = "$auditDir\ci_summary_$timestamp.md"
        $summary = @"
# Security Audit CI/CD Report
**Site:** $SiteName
**Audit Type:** $AuditType
**Timestamp:** $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")
**Exit Code:** $exitCode

## Issues Found
- Critical: $criticalCount
- High: $highCount

## CI/CD Status
$(
    if ($FailOnHighSeverity -and ($criticalCount -gt 0 -or $highCount -gt 0)) {
        "❌ FAILED - Deployment blocked due to security issues"
    } elseif ($criticalCount -gt 0) {
        "⚠️  PASSED WITH WARNINGS - Critical issues require review"
    } elseif ($highCount -gt 0) {
        "✅ PASSED - High severity issues logged for monitoring"
    } else {
        "✅ PASSED - No security blocking issues"
    }
)

## Full Report
See: $reportFile
"@

        $summary | Out-File $summaryFile -Encoding UTF8
        Write-Host "Summary report generated: $summaryFile" -ForegroundColor Green
    }

} catch {
    Write-Error "Security audit failed: $($_.Exception.Message)"
    exit 1
}

Write-Host "🔒 Security audit CI/CD integration completed" -ForegroundColor Cyan