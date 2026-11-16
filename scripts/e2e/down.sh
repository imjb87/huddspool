#!/usr/bin/env bash

set -euo pipefail

project_root=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
cd "$project_root"

docker compose \
    --project-name huddspool-e2e \
    --file compose.e2e.yaml \
    down \
    --remove-orphans
