#!/usr/bin/env sh
# Installs the worked client example (Delivery Routes) into a client repository:
# copies examples/client/files over the repository's root. Run from anywhere:
#   sh examples/client/install.sh [target-repository]   (default: this repository)
set -eu
here=$(cd "$(dirname "$0")" && pwd)
target=${1:-$(cd "$here/../.." && pwd)}
cp -R "$here/files/." "$target/"
echo "Delivery Routes installed into $target: app/Client, config/client.php, tests/Feature/Client."
echo "Then: php artisan migrate && php artisan db:seed --class='App\\Client\\Seeders\\DeliveryRouteSeeder'"
