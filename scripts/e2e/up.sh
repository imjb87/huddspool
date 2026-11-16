#!/usr/bin/env bash

set -euo pipefail

project_root=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
cd "$project_root"

if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
    echo 'Docker Compose is required to start the isolated Huddspool E2E site.' >&2
    exit 1
fi

docker compose \
    --project-name huddspool-e2e \
    --file compose.e2e.yaml \
    up \
    --build \
    --detach \
    --force-recreate

echo 'Huddspool E2E site is starting at http://127.0.0.1:18000.'
