# API Testing Script for Windows PowerShell
# Usage: .\api.ps1 -Method GET -Route "/api/categories"
# Example: .\api.ps1 -Method POST -Route "/api/categories" -Data '{"name":"Test","description":"Desc"}'

param(
    [Parameter(Mandatory=$false)]
    [string]$Method,

    [Parameter(Mandatory=$false)]
    [string]$Route,

    [Parameter(Mandatory=$false)]
    [string]$Data = "",

    [Parameter(Mandatory=$false)]
    [string]$BaseUrl = $env:API_BASE_URL,

    [Parameter(Mandatory=$false)]
    [string]$Token = $env:API_TOKEN,

    [Parameter(Mandatory=$false)]
    [string]$ContentType = "application/json",

    [Parameter(Mandatory=$false)]
    [string]$Accept = "application/json",

    [Parameter(Mandatory=$false)]
    [switch]$Verbose,

    [Parameter(Mandatory=$false)]
    [string]$SaveFile = "",

    [Parameter(Mandatory=$false)]
    [switch]$Help
)

# Function to display usage
function Show-Usage {
    Write-Host "`nUsage:" -ForegroundColor Blue
    Write-Host "  .\api.ps1 -Method METHOD -Route ROUTE [-Data DATA] [-Options]`n"

    Write-Host "Examples:" -ForegroundColor Blue
    Write-Host '  .\api.ps1 -Method GET -Route "/api/categories"'
    Write-Host '  .\api.ps1 -Method POST -Route "/api/categories" -Data ''{"name":"Test","description":"Desc"}'''
    Write-Host '  .\api.ps1 -Method PUT -Route "/api/categories/1" -Data ''{"name":"Updated"}'''
    Write-Host '  .\api.ps1 -Method DELETE -Route "/api/categories/1"'
    Write-Host ""

    Write-Host "Options:" -ForegroundColor Blue
    Write-Host "  -Help                   Show this help message"
    Write-Host "  -BaseUrl URL            Set base URL (default: http://localhost)"
    Write-Host "  -Token TOKEN            Add Bearer token for authentication"
    Write-Host "  -ContentType TYPE       Set Content-Type header (default: application/json)"
    Write-Host "  -Accept TYPE            Set Accept header (default: application/json)"
    Write-Host "  -Verbose                Show request details"
    Write-Host "  -SaveFile FILE          Save response to file"
    Write-Host ""

    Write-Host "Environment Variables:" -ForegroundColor Blue
    Write-Host "  API_BASE_URL            Default base URL"
    Write-Host "  API_TOKEN               Default bearer token"
    Write-Host ""
    exit 0
}

# Show help if requested or missing required parameters
if ($Help -or [string]::IsNullOrEmpty($Method) -or [string]::IsNullOrEmpty($Route)) {
    Show-Usage
}

# Set default base URL if not provided
if ([string]::IsNullOrEmpty($BaseUrl)) {
    $BaseUrl = "http://localhost"
}

# Build URL
$Url = "$BaseUrl$Route"

# Show request details if verbose
if ($Verbose) {
    Write-Host "`n=== REQUEST DETAILS ===" -ForegroundColor Yellow
    Write-Host "Method: " -ForegroundColor Blue -NoNewline
    Write-Host $Method
    Write-Host "URL: " -ForegroundColor Blue -NoNewline
    Write-Host $Url
    Write-Host "Content-Type: " -ForegroundColor Blue -NoNewline
    Write-Host $ContentType
    Write-Host "Accept: " -ForegroundColor Blue -NoNewline
    Write-Host $Accept

    if (-not [string]::IsNullOrEmpty($Token)) {
        $MaskedToken = "***" + $Token.Substring([Math]::Max(0, $Token.Length - 8))
        Write-Host "Authorization: " -ForegroundColor Blue -NoNewline
        Write-Host "Bearer $MaskedToken"
    }

    if (-not [string]::IsNullOrEmpty($Data)) {
        Write-Host "Data: " -ForegroundColor Blue -NoNewline
        Write-Host $Data
    }
    Write-Host ""
}

# Build headers
$Headers = @{
    "Content-Type" = $ContentType
    "Accept" = $Accept
}

if (-not [string]::IsNullOrEmpty($Token)) {
    $Headers["Authorization"] = "Bearer $Token"
}

# Execute request
Write-Host "`n=== RESPONSE ===" -ForegroundColor Yellow

try {
    $Response = $null
    $StatusCode = 0

    if ([string]::IsNullOrEmpty($Data)) {
        $Response = Invoke-RestMethod -Uri $Url -Method $Method -Headers $Headers -ErrorAction Stop -StatusCodeVariable StatusCode
    } else {
        $Response = Invoke-RestMethod -Uri $Url -Method $Method -Headers $Headers -Body $Data -ErrorAction Stop -StatusCodeVariable StatusCode
    }

    # Pretty print JSON response
    $JsonResponse = $Response | ConvertTo-Json -Depth 10
    Write-Host $JsonResponse

    Write-Host "`nHTTP Status: $StatusCode" -ForegroundColor Green

    # Save to file if requested
    if (-not [string]::IsNullOrEmpty($SaveFile)) {
        $JsonResponse | Out-File -FilePath $SaveFile -Encoding UTF8
        Write-Host "`nResponse saved to: $SaveFile" -ForegroundColor Green
    }

    # Check if response contains error
    if ($JsonResponse -match '"error"') {
        Write-Host "`n⚠ Response contains errors" -ForegroundColor Red
        exit 1
    } else {
        Write-Host "`n✓ Request completed" -ForegroundColor Green
    }

} catch {
    $StatusCode = $_.Exception.Response.StatusCode.value__
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "`nHTTP Status: $StatusCode" -ForegroundColor Red

    # Try to get error response body
    try {
        $ErrorStream = $_.Exception.Response.GetResponseStream()
        $Reader = New-Object System.IO.StreamReader($ErrorStream)
        $ErrorBody = $Reader.ReadToEnd()
        if ($ErrorBody) {
            Write-Host "`nError Response:" -ForegroundColor Red
            Write-Host $ErrorBody
        }
    } catch {
        # Ignore if we can't read error body
    }

    Write-Host "`n⚠ Request failed" -ForegroundColor Red
    exit 1
}
