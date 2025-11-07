# Script to push project to GitHub
# Run: .\push-to-github.ps1

Write-Host "=== Preparing project for GitHub ===" -ForegroundColor Green

# Check git
if (-not (Test-Path .git)) {
    Write-Host "Initializing git repository..." -ForegroundColor Yellow
    git init
}

# Add all files
Write-Host "`nAdding files..." -ForegroundColor Yellow
git add .

# Check status
Write-Host "`nRepository status:" -ForegroundColor Yellow
git status

# Create commit
Write-Host "`nCreating commit..." -ForegroundColor Yellow
git commit -m "Initial commit: Word Reverser - PHP library for reversing letters in words"

# Rename branch to main
Write-Host "`nSetting up main branch..." -ForegroundColor Yellow
git branch -M main

# Setup remote
Write-Host "`nSetting up remote repository..." -ForegroundColor Yellow
git remote remove origin 2>$null
git remote add origin https://github.com/valyaA11en/word-reverser.git

Write-Host "`nRemote configured:" -ForegroundColor Yellow
git remote -v

Write-Host "`n=== Ready to push! ===" -ForegroundColor Green
Write-Host "`nIMPORTANT: First create repository on GitHub:" -ForegroundColor Cyan
Write-Host "1. Go to https://github.com/valyaA11en" -ForegroundColor Cyan
Write-Host "2. Click 'New repository'" -ForegroundColor Cyan
Write-Host "3. Name: word-reverser" -ForegroundColor Cyan
Write-Host "4. Create repository (without README is OK)" -ForegroundColor Cyan
Write-Host "`nThen run:" -ForegroundColor Yellow
Write-Host "git push -u origin main" -ForegroundColor White
Write-Host "`nIf authentication required, use Personal Access Token" -ForegroundColor Cyan
