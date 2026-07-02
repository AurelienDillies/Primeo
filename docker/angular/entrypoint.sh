#!/bin/sh
set -e

cd /app

if [ ! -d "node_modules" ] || [ ! -f "node_modules/.bin/ng" ]; then
    echo "Installing npm dependencies..."
    npm install
fi

exec npm start -- --host 0.0.0.0 --poll=1000 --hmr