#!/usr/bin/env bash
# Gets a Claude Code cloud session to the point where the app and the reference
# study tool both run: the local PostgreSQL 16 cluster with the role and databases
# phpunit.xml expects, Redis, Composer and npm dependencies, and the browser's
# trust in the session proxy. Idempotent; every step that depends on the
# network fails soft, because the environment's egress policy decides what is
# reachable, and a session that cannot install locally still has CI.
set -uo pipefail

if [ "${CLAUDE_CODE_REMOTE:-}" != "true" ]; then
  exit 0
fi

cd "${CLAUDE_PROJECT_DIR:-$(dirname "$0")/../..}"

say() { echo "[session-start] $*" >&2; }

# --- PostgreSQL 16 -----------------------------------------------------------
if [ -d /etc/postgresql/16/main ]; then
  pg_ctlcluster 16 main start >/dev/null 2>&1 || true
  for _ in $(seq 1 20); do
    pg_isready -q -h 127.0.0.1 -p 5432 && break
    sleep 0.5
  done
  if pg_isready -q -h 127.0.0.1 -p 5432; then
    su postgres -c "psql -tAc \"SELECT 1 FROM pg_roles WHERE rolname='augusterp'\"" | grep -q 1 \
      || su postgres -c "psql -qc \"CREATE ROLE augusterp LOGIN SUPERUSER PASSWORD 'secret'\""
    for db in augusterp augusterp_test; do
      su postgres -c "psql -tAc \"SELECT 1 FROM pg_database WHERE datname='$db'\"" | grep -q 1 \
        || su postgres -c "createdb -O augusterp $db"
    done
  else
    say "PostgreSQL did not start"
  fi
fi

# --- Redis -------------------------------------------------------------------
if command -v redis-server >/dev/null 2>&1 && ! redis-cli ping >/dev/null 2>&1; then
  redis-server --daemonize yes >/dev/null 2>&1 || say "redis-server did not start"
fi

# --- Composer ----------------------------------------------------------------
# The session proxy refuses Composer's zip downloads from api.github.com but
# serves git clones of public repositories, hence --prefer-source.
if [ -f composer.json ]; then
  composer install --no-interaction --no-progress --prefer-source -q 2>/dev/null \
    || composer install --no-interaction --no-progress -q \
    || say "composer install failed; tests run in CI"
  [ -f .env ] || { cp .env.example .env && php artisan key:generate -q; }
fi

# --- The reference study tool ------------------------------------------------
if [ -f tools/referensi-scan/package.json ]; then
  (cd tools/referensi-scan && npm ci --no-audit --no-fund -q 2>/dev/null) || say "npm ci for the study tool failed"
fi

# The pre-installed Chromium trusts certificates through NSS, and the
# session's TLS-intercepting proxy is not in that store, so every HTTPS page
# fails with ERR_CERT_AUTHORITY_INVALID until its CA is imported. The CA is
# the one certificate in the bundle issued by Anthropic's agent proxy.
if [ -n "${SSL_CERT_FILE:-}" ] && [ -f "$SSL_CERT_FILE" ]; then
  if ! command -v certutil >/dev/null 2>&1; then
    export DEBIAN_FRONTEND=noninteractive
    (apt-get update -qq >/dev/null 2>&1 && apt-get install -y -qq libnss3-tools >/dev/null 2>&1) \
      || say "libnss3-tools not installed; the study tool's browser will not trust the proxy"
  fi
  if command -v certutil >/dev/null 2>&1; then
    mkdir -p "$HOME/.pki/nssdb"
    [ -f "$HOME/.pki/nssdb/cert9.db" ] || certutil -d "sql:$HOME/.pki/nssdb" -N --empty-password >/dev/null 2>&1
    tmp=$(mktemp -d)
    awk -v dir="$tmp" 'BEGIN{n=0} /BEGIN CERTIFICATE/{n++; f=sprintf("%s/cert-%03d.pem",dir,n)} {print > f}' "$SSL_CERT_FILE"
    for pem in "$tmp"/cert-*.pem; do
      subject=$(openssl x509 -in "$pem" -noout -subject 2>/dev/null)
      case "$subject" in
        *"agent-proxy interception CA"*)
          nick=$(printf '%s' "$subject" | sed -E 's/.*CN *= *([^,]*).*/\1/')
          certutil -d "sql:$HOME/.pki/nssdb" -L -n "$nick" >/dev/null 2>&1 \
            || certutil -d "sql:$HOME/.pki/nssdb" -A -t "C,," -n "$nick" -i "$pem" >/dev/null 2>&1 \
            || say "could not import the proxy CA into NSS"
          ;;
      esac
    done
    rm -rf "$tmp"
  fi
fi

exit 0
