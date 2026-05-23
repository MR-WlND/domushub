$ErrorActionPreference = 'Stop'

$php = (Get-Command php -ErrorAction SilentlyContinue).Source
$npm = (Get-Command npm -ErrorAction SilentlyContinue).Source

if (-not $php) {
    throw 'Không tìm thấy lệnh php trong PATH.'
}

if (-not $npm) {
    throw 'Không tìm thấy lệnh npm trong PATH.'
}

$mailpitCandidates = @(
    'C:\laragon\bin\mailpit\1.22.3\mailpit.exe',
    'C:\Program Files\mailpit\mailpit.exe',
    "$env:USERPROFILE\AppData\Local\Programs\mailpit\mailpit.exe"
)

$mailpitPath = $null
foreach ($candidate in $mailpitCandidates) {
    if (Test-Path $candidate) {
        $mailpitPath = $candidate
        break
    }
}

if (-not $mailpitPath) {
    throw 'Không tìm thấy Mailpit. Hãy cài Mailpit hoặc cập nhật scripts/start-dev.ps1.'
}

function Ensure-Process {
    param(
        [string]$CommandPattern,
        [string]$FilePath,
        [string]$Arguments
    )

    $alreadyRunning = Get-CimInstance Win32_Process | Where-Object {
        $_.CommandLine -and $_.CommandLine -match [regex]::Escape($CommandPattern)
    }

    if ($alreadyRunning) {
        return
    }

    Start-Process -FilePath $FilePath -ArgumentList $Arguments -WindowStyle Hidden | Out-Null
}

$alreadyMailpit = Get-CimInstance Win32_Process | Where-Object {
    $_.Name -eq 'mailpit.exe'
}

if (-not $alreadyMailpit) {
    Start-Process -FilePath $mailpitPath -ArgumentList '--smtp 127.0.0.1:1025 --ui 127.0.0.1:8025 --quiet' -WindowStyle Hidden | Out-Null
    Start-Sleep -Seconds 2
}

Ensure-Process 'artisan serve' $php 'artisan serve'
Ensure-Process 'queue:listen' $php 'artisan queue:listen --tries=1'
Ensure-Process 'npm run dev' $npm 'run dev'

Write-Host 'Mailpit, Laravel server, queue va Vite da duoc khoi dong.'
Start-Sleep -Seconds 1
