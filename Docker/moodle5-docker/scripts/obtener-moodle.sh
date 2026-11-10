#!/usr/bin/env bash
# =============================================================================
#  Descarga el código de Moodle en ./moodle (Linux, macOS, WSL o Git Bash)
#
#    bash scripts/obtener-moodle.sh                     # rama del .env (5.3 LTS)
#    bash scripts/obtener-moodle.sh MOODLE_502_STABLE   # otra rama
# =============================================================================
set -euo pipefail
cd "$(dirname "$0")/.."

RAMA="${1:-$(grep -E '^MOODLE_BRANCH=' .env 2>/dev/null | cut -d= -f2 || true)}"
RAMA="${RAMA:-MOODLE_503_STABLE}"

if [ -d moodle/.git ]; then
    echo "Ya existe ./moodle: actualizando la rama $RAMA"
    git -C moodle pull --ff-only
    exit 0
fi

echo "Clonando Moodle ($RAMA) en ./moodle ..."
# core.longpaths solo importa en Windows (Git Bash); en Linux no hace nada
git clone -c core.longpaths=true -c core.autocrlf=false --depth 1 --branch "$RAMA" \
    https://github.com/moodle/moodle.git moodle
