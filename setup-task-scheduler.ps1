# Laravel Scheduler Setup Script for Windows
# Auto-setup Windows Task Scheduler untuk Laravel Scheduler
# 
# Cara menggunakan:
# 1. Buka PowerShell sebagai Administrator
# 2. Jalankan: .\setup-task-scheduler.ps1

# Require Administrator
if (-NOT ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Warning "Script ini memerlukan akses Administrator!"
    Write-Host "Silakan klik kanan pada PowerShell dan pilih 'Run as Administrator'" -ForegroundColor Yellow
    Exit
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Laravel Scheduler Setup - TelkomLandmark" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get project directory
$projectPath = $PSScriptRoot
$batchFile = Join-Path $projectPath "run-scheduler.bat"

# Validate batch file exists
if (-Not (Test-Path $batchFile)) {
    Write-Host "ERROR: File run-scheduler.bat tidak ditemukan!" -ForegroundColor Red
    Write-Host "Path yang dicari: $batchFile" -ForegroundColor Red
    Exit
}

Write-Host "Project Path: $projectPath" -ForegroundColor Green
Write-Host "Batch File: $batchFile" -ForegroundColor Green
Write-Host ""

# Task Scheduler Configuration
$taskName = "Laravel Scheduler - TelkomLandmark"
$taskDescription = "Auto-approve izin dan auto-close absensi untuk sistem TelkomLandmark"

Write-Host "Membuat Windows Task Scheduler..." -ForegroundColor Yellow

# Check if task already exists
$existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue

if ($existingTask) {
    Write-Host "Task sudah ada. Menghapus task lama..." -ForegroundColor Yellow
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
}

# Create scheduled task action
$action = New-ScheduledTaskAction -Execute $batchFile -WorkingDirectory $projectPath

# Create trigger (every minute)
$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 1) -RepetitionDuration ([TimeSpan]::MaxValue)

# Create task principal (run whether user is logged on or not)
$principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

# Create settings
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -RunOnlyIfNetworkAvailable:$false

# Register the task
try {
    Register-ScheduledTask -TaskName $taskName -Description $taskDescription -Action $action -Trigger $trigger -Principal $principal -Settings $settings -Force | Out-Null
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "Task Scheduler berhasil dibuat!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Task Name: $taskName" -ForegroundColor Cyan
    Write-Host "Task akan berjalan setiap 1 menit" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Cara cek task:" -ForegroundColor Yellow
    Write-Host "1. Buka Task Scheduler (Win + R -> taskschd.msc)" -ForegroundColor White
    Write-Host "2. Cari task: $taskName" -ForegroundColor White
    Write-Host "3. Lihat di tab 'History' untuk melihat eksekusi" -ForegroundColor White
    Write-Host ""
    Write-Host "Cara test manual:" -ForegroundColor Yellow
    Write-Host "php artisan schedule:list" -ForegroundColor White
    Write-Host "php artisan izin:auto-approve" -ForegroundColor White
    Write-Host ""
    Write-Host "Log file:" -ForegroundColor Yellow
    Write-Host "storage/logs/auto-approve-izin.log" -ForegroundColor White
    Write-Host "storage/logs/auto-close-attendance.log" -ForegroundColor White
    Write-Host ""
    Write-Host "Setup selesai!" -ForegroundColor Green
    
} catch {
    Write-Host ""
    Write-Host "ERROR: Gagal membuat task scheduler" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Exit
}
