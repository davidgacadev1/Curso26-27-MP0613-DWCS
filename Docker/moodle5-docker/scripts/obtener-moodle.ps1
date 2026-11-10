# =============================================================================
#  Descarga el código de Moodle en la carpeta .\moodle (PowerShell, en Windows)
#
#    .\scripts\obtener-moodle.ps1                       # rama del .env (5.3 LTS)
#    .\scripts\obtener-moodle.ps1 MOODLE_502_STABLE     # otra rama
#
#  Si PowerShell no deja ejecutar scripts:
#    powershell -ExecutionPolicy Bypass -File .\scripts\obtener-moodle.ps1
# =============================================================================
param([string]$Rama = "")

Set-Location (Split-Path $PSScriptRoot -Parent)

if (-not $Rama) {
    $linea = Select-String -Path ".env" -Pattern "^MOODLE_BRANCH=(.+)$" -ErrorAction SilentlyContinue
    $Rama = if ($linea) { $linea.Matches[0].Groups[1].Value.Trim() } else { "MOODLE_503_STABLE" }
}

if (Test-Path "moodle\.git") {
    Write-Host "Ya existe .\moodle: actualizando la rama $Rama"
    git -C moodle pull --ff-only
    exit $LASTEXITCODE
}

Write-Host "Clonando Moodle ($Rama) en .\moodle ..."
# core.longpaths: Moodle tiene rutas de más de 260 caracteres y Windows las rechaza sin esta opción.
# --depth 1: solo la última versión (unos 700 MB en lugar de varios GB con todo el historial).
git clone -c core.longpaths=true -c core.autocrlf=false --depth 1 --branch $Rama `
    https://github.com/moodle/moodle.git moodle
