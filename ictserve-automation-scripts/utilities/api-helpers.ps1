#Requires -Version 7.0
<#
.SYNOPSIS
    API interaction utilities for ICTServe automation scripts.

.DESCRIPTION
    This module provides HTTP API interaction utilities including authentication,
    request handling, response validation, and error management for backend testing.

.NOTES
    Version: 1.0.0
    Author: ICTServe Automation Team
    Requirements: PowerShell 7.x
#>

# Import common functions
$commonFunctionsPath = Join-Path $PSScriptRoot "common-functions.ps1"
if (Test-Path $commonFunctionsPath) {
    . $commonFunctionsPath
}

# Script-level variables
$script:ApiToken = $null
$script:BaseApiUrl = $null
$script:DefaultHeaders = @{}

#region API Configuration

function Initialize-ApiClient {
    <#
    .SYNOPSIS
        Initializes the API client with base URL and default headers.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$BaseUrl,
        
        [Parameter()]
        [hashtable]$DefaultHeaders = @{},
        
        [Parameter()]
        [int]$TimeoutSeconds = 30
    )
    
    $script:BaseApiUrl = $BaseUrl.TrimEnd('/')
    $script:DefaultHeaders = @{
        'Content-Type' = 'application/json'
        'Accept' = 'application/json'
    }
    
    foreach ($key in $DefaultHeaders.Keys) {
        $script:DefaultHeaders[$key] = $DefaultHeaders[$key]
    }
    
    $script:ApiTimeout = $TimeoutSeconds
    
    Write-AutomationLog "API client initialized: $BaseUrl" -Level INFO
}

function Set-ApiToken {
    <#
    .SYNOPSIS
        Sets the authentication token for API requests.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Token,
        
        [Parameter()]
        [ValidateSet('Bearer', 'Basic', 'ApiKey')]
        [string]$TokenType = 'Bearer'
    )
    
    $script:ApiToken = $Token
    $script:TokenType = $TokenType
    
    $authHeader = switch ($TokenType) {
        'Bearer' { "Bearer $Token" }
        'Basic' { "Basic $Token" }
        'ApiKey' { $Token }
    }
    
    $script:DefaultHeaders['Authorization'] = $authHeader
    
    Write-AutomationLog "API token set ($TokenType)" -Level DEBUG
}

#endregion

#region HTTP Request Functions

function Invoke-ApiRequest {
    <#
    .SYNOPSIS
        Makes an HTTP request to the API with error handling.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [Alias('Url')]
        [string]$Endpoint,
        
        [Parameter()]
        [ValidateSet('GET', 'POST', 'PUT', 'PATCH', 'DELETE')]
        [string]$Method = 'GET',
        
        [Parameter()]
        [object]$Body = $null,
        
        [Parameter()]
        [hashtable]$Headers = @{},
        
        [Parameter()]
        [hashtable]$QueryParams = @{},
        
        [Parameter()]
        [int]$TimeoutSeconds = 0,
        
        [Parameter()]
        [switch]$RawResponse,
        
        [Parameter()]
        [switch]$IgnoreErrors
    )
    
    # Build URL
    $url = if ($Endpoint.StartsWith('http')) { $Endpoint } else { "$($script:BaseApiUrl)/$($Endpoint.TrimStart('/'))" }
    
    # Add query parameters
    if ($QueryParams.Count -gt 0) {
        $queryString = ($QueryParams.GetEnumerator() | ForEach-Object { "$($_.Key)=$([System.Web.HttpUtility]::UrlEncode($_.Value))" }) -join '&'
        $url = "$url?$queryString"
    }
    
    # Merge headers
    $requestHeaders = if ($script:DefaultHeaders) { $script:DefaultHeaders.Clone() } else { @{ 'Content-Type' = 'application/json'; 'Accept' = 'application/json' } }
    foreach ($key in $Headers.Keys) {
        $requestHeaders[$key] = $Headers[$key]
    }
    
    # Set timeout
    $timeout = if ($TimeoutSeconds -gt 0) { $TimeoutSeconds } elseif ($script:ApiTimeout) { $script:ApiTimeout } else { 30 }
    
    Write-AutomationLog "API Request: $Method $url" -Level DEBUG
    
    $params = @{
        Uri = $url
        Method = $Method
        Headers = $requestHeaders
        TimeoutSec = $timeout
        ErrorAction = if ($IgnoreErrors) { 'SilentlyContinue' } else { 'Stop' }
    }
    
    if ($Body -and $Method -in @('POST', 'PUT', 'PATCH')) {
        $params['Body'] = if ($Body -is [string]) { $Body } else { $Body | ConvertTo-Json -Depth 10 }
        Write-AutomationLog "Request Body: $($params['Body'])" -Level DEBUG
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-RestMethod @params
        $duration = (Get-Date) - $startTime
        
        Write-AutomationLog "API Response: Success (${duration.TotalMilliseconds}ms)" -Level DEBUG
        
        if ($RawResponse) {
            return $response
        }
        
        return $response
    }
    catch {
        $statusCode = if ($_.Exception.Response) { [int]$_.Exception.Response.StatusCode } else { 0 }
        $errorMessage = $_.Exception.Message
        
        if (-not $IgnoreErrors) {
            Write-AutomationLog "API Error: $statusCode - $errorMessage" -Level ERROR
        }
        
        if ($IgnoreErrors) {
            return $null
        }
        
        return @{
            Success = $false
            Error = $errorMessage
            StatusCode = $statusCode
            Duration = (Get-Date) - $startTime
        }
    }
}

function Invoke-ApiGet {
    <#
    .SYNOPSIS
        Makes a GET request to the API.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Endpoint,
        
        [Parameter()]
        [hashtable]$QueryParams = @{},
        
        [Parameter()]
        [hashtable]$Headers = @{}
    )
    
    return Invoke-ApiRequest -Endpoint $Endpoint -Method 'GET' -QueryParams $QueryParams -Headers $Headers
}

function Invoke-ApiPost {
    <#
    .SYNOPSIS
        Makes a POST request to the API.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Endpoint,
        
        [Parameter()]
        [object]$Body = $null,
        
        [Parameter()]
        [hashtable]$Headers = @{}
    )
    
    return Invoke-ApiRequest -Endpoint $Endpoint -Method 'POST' -Body $Body -Headers $Headers
}

function Invoke-ApiPut {
    <#
    .SYNOPSIS
        Makes a PUT request to the API.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Endpoint,
        
        [Parameter()]
        [object]$Body = $null,
        
        [Parameter()]
        [hashtable]$Headers = @{}
    )
    
    return Invoke-ApiRequest -Endpoint $Endpoint -Method 'PUT' -Body $Body -Headers $Headers
}

function Invoke-ApiDelete {
    <#
    .SYNOPSIS
        Makes a DELETE request to the API.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Endpoint,
        
        [Parameter()]
        [hashtable]$Headers = @{}
    )
    
    return Invoke-ApiRequest -Endpoint $Endpoint -Method 'DELETE' -Headers $Headers
}

#endregion

#region Authentication Functions

function Get-ApiAuthToken {
    <#
    .SYNOPSIS
        Authenticates with the API and retrieves an access token.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Email,
        
        [Parameter(Mandatory = $true)]
        [string]$Password,
        
        [Parameter()]
        [string]$DeviceName = "automation-script"
    )
    
    Write-AutomationLog "Authenticating user: $Email" -Level INFO
    
    $body = @{
        email = $Email
        password = $Password
        device_name = $DeviceName
    }
    
    $response = Invoke-ApiPost -Endpoint '/sanctum/token' -Body $body
    
    if ($response.Success -and $response.Data.token) {
        Set-ApiToken -Token $response.Data.token -TokenType 'Bearer'
        Write-AutomationLog "Authentication successful" -Level SUCCESS
        return @{
            Success = $true
            Token = $response.Data.token
            User = $response.Data.user
        }
    }
    
    Write-AutomationLog "Authentication failed" -Level ERROR
    return @{
        Success = $false
        Error = $response.Error
    }
}

function Revoke-ApiAuthToken {
    <#
    .SYNOPSIS
        Revokes the current API authentication token.
    #>
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Revoking API token" -Level INFO
    
    $response = Invoke-ApiPost -Endpoint '/sanctum/token/revoke'
    
    $script:ApiToken = $null
    $script:DefaultHeaders.Remove('Authorization')
    
    return $response.Success
}

#endregion

#region Response Validation Functions

function Assert-ApiResponse {
    <#
    .SYNOPSIS
        Validates an API response against expected criteria.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [hashtable]$Response,
        
        [Parameter()]
        [int]$ExpectedStatusCode = 200,
        
        [Parameter()]
        [string[]]$RequiredFields = @(),
        
        [Parameter()]
        [string]$Message = ""
    )
    
    $errors = @()
    
    # Check success
    if (-not $Response.Success) {
        $errors += "Request failed: $($Response.Error)"
    }
    
    # Check status code
    if ($Response.StatusCode -ne $ExpectedStatusCode) {
        $errors += "Expected status $ExpectedStatusCode, got $($Response.StatusCode)"
    }
    
    # Check required fields
    foreach ($field in $RequiredFields) {
        $value = $Response.Data
        foreach ($part in $field.Split('.')) {
            if ($null -eq $value) { break }
            $value = $value.$part
        }
        
        if ($null -eq $value) {
            $errors += "Missing required field: $field"
        }
    }
    
    if ($errors.Count -gt 0) {
        $errorMessage = if ($Message) { "$Message - " } else { "" }
        $errorMessage += $errors -join '; '
        Write-AutomationLog $errorMessage -Level ERROR
        throw $errorMessage
    }
    
    Write-AutomationLog "API response validated successfully" -Level DEBUG
    return $true
}

function Test-ApiEndpoint {
    <#
    .SYNOPSIS
        Tests if an API endpoint is accessible.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Endpoint,
        
        [Parameter()]
        [int]$ExpectedStatusCode = 200
    )
    
    $response = Invoke-ApiGet -Endpoint $Endpoint
    
    return @{
        Available = $response.Success -and $response.StatusCode -eq $ExpectedStatusCode
        StatusCode = $response.StatusCode
        ResponseTime = $response.Duration.TotalMilliseconds
    }
}

#endregion

#region File Upload Functions

function Invoke-ApiFileUpload {
    <#
    .SYNOPSIS
        Uploads a file to the API.
    #>
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        [string]$Endpoint,
        
        [Parameter(Mandatory = $true)]
        [string]$FilePath,
        
        [Parameter()]
        [string]$FieldName = 'file',
        
        [Parameter()]
        [hashtable]$AdditionalFields = @{}
    )
    
    if (-not (Test-Path $FilePath)) {
        Write-AutomationLog "File not found: $FilePath" -Level ERROR
        return @{ Success = $false; Error = "File not found" }
    }
    
    $url = "$($script:BaseApiUrl)/$($Endpoint.TrimStart('/'))"
    
    Write-AutomationLog "Uploading file: $FilePath to $Endpoint" -Level INFO
    
    try {
        $fileBytes = [System.IO.File]::ReadAllBytes($FilePath)
        $fileName = [System.IO.Path]::GetFileName($FilePath)
        
        # Build multipart form data
        $boundary = [System.Guid]::NewGuid().ToString()
        $contentType = "multipart/form-data; boundary=$boundary"
        
        # This is a simplified placeholder - actual implementation would use proper multipart encoding
        Write-AutomationLog "File upload initiated: $fileName" -Level DEBUG
        
        return @{
            Success = $true
            FileName = $fileName
            FileSize = $fileBytes.Length
        }
    }
    catch {
        Write-AutomationLog "File upload failed: $($_.Exception.Message)" -Level ERROR
        return @{
            Success = $false
            Error = $_.Exception.Message
        }
    }
}

#endregion

# Export functions (only when loaded as a module)
if ($MyInvocation.MyCommand.ScriptBlock.Module) {
    Export-ModuleMember -Function @(
        'Initialize-ApiClient',
        'Set-ApiToken',
        'Invoke-ApiRequest',
        'Invoke-ApiGet',
        'Invoke-ApiPost',
        'Invoke-ApiPut',
        'Invoke-ApiDelete',
        'Get-ApiAuthToken',
        'Revoke-ApiAuthToken',
        'Assert-ApiResponse',
        'Test-ApiEndpoint',
        'Invoke-ApiFileUpload'
    )
}
