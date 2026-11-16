#!/usr/bin/env bash

set -euo pipefail

project_root=$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)
cd "$project_root"

bash scripts/e2e/up.sh

for attempt in $(seq 1 60); do
    if curl --silent --show-error --fail http://127.0.0.1:18000/login >/dev/null; then
        break
    fi

    if [ "$attempt" -eq 60 ]; then
        echo 'The isolated Huddspool E2E site did not become ready.' >&2
        docker compose --project-name huddspool-e2e --file compose.e2e.yaml logs --no-color e2e-bootstrap e2e-app e2e-reverb >&2 || true
        exit 1
    fi

    sleep 1
done

npm run e2e
