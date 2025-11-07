# Final push to GitHub repository
Write-Host "=== Pushing to GitHub: Word-Reverser-test-task ===" -ForegroundColor Green

# Setup remote
Write-Host "`nSetting up remote..." -ForegroundColor Yellow
git remote remove origin 2>$null
git remote add origin https://github.com/valyaA11en/Word-Reverser-test-task.git
Write-Host "Remote configured:" -ForegroundColor Green
git remote -v

# Add all files
Write-Host "`nAdding all files..." -ForegroundColor Yellow
git add -A
$status = git status --short
if ($status) {
    Write-Host "Files to commit:" -ForegroundColor Green
    $status
} else {
    Write-Host "No changes to commit" -ForegroundColor Yellow
}

# Create commit
Write-Host "`nCreating commit..." -ForegroundColor Yellow
git commit -m "completing the Word Reverser test task"
if ($LASTEXITCODE -eq 0) {
    Write-Host "Commit created successfully" -ForegroundColor Green
} else {
    Write-Host "Commit failed or nothing to commit" -ForegroundColor Yellow
}

# Set branch to main
Write-Host "`nSetting branch to main..." -ForegroundColor Yellow
git branch -M main

# Push to GitHub
Write-Host "`nPushing to GitHub..." -ForegroundColor Yellow
Write-Host "This may require authentication (Personal Access Token)" -ForegroundColor Cyan
git push -u origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n=== SUCCESS! ===" -ForegroundColor Green
    Write-Host "Repository: https://github.com/valyaA11en/Word-Reverser-test-task" -ForegroundColor Cyan
} else {
    Write-Host "`n=== PUSH FAILED ===" -ForegroundColor Red
    Write-Host "You may need to:" -ForegroundColor Yellow
    Write-Host "1. Create Personal Access Token on GitHub" -ForegroundColor Yellow
    Write-Host "2. Use token as password when prompted" -ForegroundColor Yellow
    Write-Host "3. Or configure SSH keys" -ForegroundColor Yellow
}

