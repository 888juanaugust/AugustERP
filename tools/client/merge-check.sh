#!/usr/bin/env bash
# The client-update check: a client repository made from an earlier template
# commit, with the worked example installed and committed, merges the
# template's current commit without a conflict, and the example's tests pass.
#   tools/client/merge-check.sh [base-commit]   (default: HEAD~1)
# Needs vendor/ installed and the test database of phpunit.xml reachable.
set -euo pipefail
repo=$(git rev-parse --show-toplevel)
base=$(git -C "$repo" rev-parse "${1:-HEAD~1}")
head=$(git -C "$repo" rev-parse HEAD)
work=$(mktemp -d)
trap 'rm -rf "$work"' EXIT

git clone -q --no-local "$repo" "$work/client"
cd "$work/client"
git checkout -q -b client "$base"
git config user.email client@example.test
git config user.name "Client"
git config merge.ours.driver true

sh "$repo/examples/client/install.sh" "$work/client" >/dev/null
git add -A
git commit -q -m "Client: delivery routes"

if ! git merge -q --no-edit "$head"; then
    echo "The template update conflicts with the client's work:" >&2
    git diff --name-only --diff-filter=U >&2
    exit 1
fi
if git grep -n -E '^(<<<<<<<|>>>>>>>) ' -- . ':!vendor' >/dev/null 2>&1; then
    echo "Conflict markers left after the merge." >&2
    exit 1
fi

# The template's packages, copied without git metadata; the clone's own autoloader, so App\ is the clone's code.
tar -C "$repo" --exclude=.git -cf - vendor | tar -xf -
composer dump-autoload -q --no-interaction
[ -d "$repo/public/build" ] && ln -s "$repo/public/build" public/build
cp "$repo/.env" .env 2>/dev/null || cp .env.example .env
php artisan key:generate -q --force
php artisan test tests/Feature/Client
echo "The template update merged cleanly into the client and its tests pass."
