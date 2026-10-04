#!/usr/bin/env bash
# The last check before docs/accurate and docs/spec are committed: nothing that
# looks like one of the business's records may be in them. The sanitiser drops
# such text before it is written; this proves it, and it fails CI when it did not.
# Usage: tools/accurate-scan/privacy-check.sh [dir ...]   (default: docs/accurate docs/spec)
set -uo pipefail
cd "$(dirname "$0")/../.."
dirs=("$@"); [ ${#dirs[@]} -eq 0 ] && dirs=(docs/accurate docs/spec)
status=0
report() { echo "privacy-check: $1" >&2; status=1; }

# A business name: PT / CV / UD / TB / Toko / Bengkel followed by a word.
hits=$(grep -rniE '\b(pt|cv|ud|tb|toko|bengkel)\.? +[a-z]' "${dirs[@]}" 2>/dev/null | grep -viE 'cv javaindo 35|pt java indo|pt/cv|cv/' || true)
[ -n "$hits" ] && report "business names:"$'\n'"$hits"
# Money, thousands separators, long digit runs (phone, NPWP, document numbers), dates, emails.
hits=$(grep -rnE 'Rp\.? *[0-9]|[0-9]{1,3}([.,][0-9]{3})+|[0-9]{5,}|[0-9]{1,2}[/.-][0-9]{1,2}[/.-][0-9]{2,4}|@[a-z0-9-]+\.' "${dirs[@]}" 2>/dev/null \
  | grep -vE 'scannedAt|Dipindai|Dibangkitkan|20[0-9]{2}-[0-9]{2}-[0-9]{2}T[0-9:.]+Z|PMK 131/2024|11/12|2/10 n/30|1721|formulir-1721|UU PDP 27/2022' || true)
[ -n "$hits" ] && report "amounts, numbers, dates or emails:"$'\n'"$hits"
# The login itself.
if [ -n "${ACCURATE_EMAIL:-}" ] && grep -rqF -- "$ACCURATE_EMAIL" "${dirs[@]}" 2>/dev/null; then report "the ACCURATE login email appears in the output"; fi

[ $status -eq 0 ] && echo "privacy-check: clean (${dirs[*]})"
exit $status
