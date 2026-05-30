#!/usr/bin/env bash
set -euo pipefail

PLAN_ID="${1:-}"
CONFIG_FILE="${2:-}"

if [[ -z "$PLAN_ID" || -z "$CONFIG_FILE" ]]; then
  echo "Usage: $0 <plan_id> <config.json>"
  exit 1
fi

if [[ ! -f "$CONFIG_FILE" ]]; then
  echo "ERROR: Config file not found: $CONFIG_FILE" >&2
  exit 1
fi

ROOT="$(python3 - "$CONFIG_FILE" << 'PY'
import json, pathlib, sys
print(pathlib.Path(json.load(open(sys.argv[1]))["root"]).expanduser().resolve())
PY
)"
DIST_DIR="$ROOT/$(python3 - "$CONFIG_FILE" << 'PY'
import json, sys
print(json.load(open(sys.argv[1]))["dist_dir"])
PY
)"
PLAN_DIR="$ROOT/$(python3 - "$CONFIG_FILE" << 'PY'
import json, sys
print(json.load(open(sys.argv[1]))["plan_dir"])
PY
)"
REPORT_DIR="$ROOT/$(python3 - "$CONFIG_FILE" << 'PY'
import json, sys
print(json.load(open(sys.argv[1]))["report_dir"])
PY
)"
OUT_MD="$REPORT_DIR/hostinger_site_install_plan_${PLAN_ID}.md"
OUT_JSON="$REPORT_DIR/hostinger_site_install_plan_${PLAN_ID}.json"

mkdir -p "$REPORT_DIR"

python3 - "$PLAN_ID" "$CONFIG_FILE" << 'PYEOF'
import json
import sys
from pathlib import Path

plan_id = sys.argv[1]
config_file = sys.argv[2]

cfg = json.loads(Path(config_file).read_text())

root = Path(cfg["root"]).expanduser().resolve()
dist_dir = root / cfg["dist_dir"]
plan_dir = root / cfg["plan_dir"]
report_dir = root / cfg["report_dir"]
out_md = report_dir / f"hostinger_site_install_plan_{plan_id}.md"
out_json = report_dir / f"hostinger_site_install_plan_{plan_id}.json"

for domain in cfg["domains"]:
    (plan_dir / domain).mkdir(parents=True, exist_ok=True)

for domain, data in cfg["domains"].items():
    checklist_path = plan_dir / domain / "install_checklist.md"

    title = domain.replace("_", " ").replace("-", " ").title()
    lines = [
        f"# {title} Hostinger Install Checklist",
        "",
        "## Domain Model",
        "",
        data.get("model", "TBD"),
        "",
    ]

    plugins = data.get("plugins", [])
    if plugins:
        lines += ["## Upload Plugins", ""]
        step = 1
        for plugin in plugins:
            lines.append(f"{step}. Upload `{plugin}`")
            step += 1
            lines.append(f"{step}. Activate plugin.")
            step += 1
        if data.get("cpts"):
            lines.append(f"{step}. Confirm admin has CPTs:")
            for cpt in data["cpts"]:
                lines.append(f"   - `{cpt}`")
            step += 1
        lines.append(f"{step}. Confirm no fatal error.")
        lines.append("")

    if data.get("shortcodes"):
        lines += [
            "## Test Page",
            "",
            "Create page: `Plugin Smoke Test`",
            "",
            "Add:",
            "",
            "```text",
        ]
        for sc in data["shortcodes"]:
            lines.append(f"[{sc}]")
        lines += ["```", ""]

    lines += [
        "## Theme Policy",
        "",
        data.get("theme_policy", "rebuild_from_scratch").replace("_", " ").title()
        + ". Do not preserve old theme as canonical.",
        "",
        "## Pages To Rebuild",
        "",
    ]
    for page in data.get("pages", []):
        lines.append(f"- {page}")
    lines.append("")

    if data.get("backend_rule"):
        lines += ["## Backend Rule", "", data["backend_rule"], ""]

    if data.get("hold_reason"):
        lines += ["## Hold Reason", "", data["hold_reason"], ""]

    if data.get("proof_items"):
        lines += ["## Proof Needed", ""]
        for item in data["proof_items"]:
            lines.append(f"- {item}")
        lines.append("")

    checklist_path.write_text("\n".join(lines) + "\n")
    print(f"CHECKLIST_CREATED={checklist_path}")

payload = {
    "plan_id": plan_id,
    "root": str(root),
    "dist_dir": str(dist_dir),
    "plan_dir": str(plan_dir),
    "domains": {},
}

for domain, data in cfg["domains"].items():
    payload["domains"][domain] = {
        "checklist": str(plan_dir / domain / "install_checklist.md"),
        "plugins": [str(dist_dir / p) for p in data.get("plugins", [])],
        "theme_policy": data.get("theme_policy", "rebuild_from_scratch"),
        "status": data.get("status", "draft"),
        "model": data.get("model", ""),
        "cpts": data.get("cpts", []),
        "shortcodes": data.get("shortcodes", []),
        "pages": data.get("pages", []),
        "proof_items": data.get("proof_items", []),
        "hold_reason": data.get("hold_reason", ""),
        "backend_rule": data.get("backend_rule", ""),
    }

out_json.write_text(json.dumps(payload, indent=2, sort_keys=True))
print(f"JSON_CREATED={out_json}")

lines = [
    f"# Hostinger Site Install Plan {plan_id}",
    "",
    f"- **root:** `{root}`",
    f"- **plan_dir:** `{plan_dir}`",
    f"- **dist_dir:** `{dist_dir}`",
    "",
    "## Domains",
    "",
]

for domain, data in payload["domains"].items():
    lines += [
        f"### {domain}",
        "",
        f"- **checklist:** `{data['checklist']}`",
        f"- **theme_policy:** `{data['theme_policy']}`",
        f"- **status:** `{data['status']}`",
        f"- **model:** {data.get('model', 'N/A')}",
        "- **plugins:**",
    ]
    if data["plugins"]:
        for plugin in data["plugins"]:
            lines.append(f"  - `{plugin}`")
    else:
        lines.append("  - none")

    if data.get("cpts"):
        lines.append("- **cpts:**")
        for cpt in data["cpts"]:
            lines.append(f"  - `{cpt}`")

    if data.get("shortcodes"):
        lines.append("- **shortcodes:**")
        for shortcode in data["shortcodes"]:
            lines.append(f"  - `[{shortcode}]`")

    if data.get("pages"):
        lines.append("- **pages to rebuild:**")
        for page in data["pages"]:
            lines.append(f"  - {page}")

    if data.get("hold_reason"):
        lines.append(f"- **hold_reason:** {data['hold_reason']}")

    lines.append("")

lines += ["## Global Rules", ""]
for rule in cfg.get("global_rules", []):
    lines.append(f"- {rule}")
lines.append("")

out_md.write_text("\n".join(lines) + "\n")
print(f"REPORT_CREATED={out_md}")
PYEOF

echo ""
echo "== VERIFY =="

FAIL=0

while IFS= read -r domain; do
  if [[ ! -f "$PLAN_DIR/$domain/install_checklist.md" ]]; then
    echo "FAIL: $PLAN_DIR/$domain/install_checklist.md missing"
    FAIL=1
  else
    echo "${domain}_CHECKLIST=PASS"
  fi
done < <(python3 - "$CONFIG_FILE" << 'PY'
import json, sys
for domain in json.load(open(sys.argv[1]))["domains"]:
    print(domain)
PY
)

while IFS= read -r plugin; do
  [[ -z "$plugin" ]] && continue
  if [[ ! -f "$DIST_DIR/$plugin" ]]; then
    echo "FAIL: $DIST_DIR/$plugin missing"
    FAIL=1
  else
    echo "PLUGIN_ZIP_PASS=$plugin"
  fi
done < <(python3 - "$CONFIG_FILE" << 'PY'
import json, sys
cfg = json.load(open(sys.argv[1]))
plugins = sorted({p for d in cfg["domains"].values() for p in d.get("plugins", [])})
for plugin in plugins:
    print(plugin)
PY
)

if python3 -m json.tool "$OUT_JSON" >/dev/null 2>&1; then
  echo "JSON_VALID=PASS"
else
  echo "JSON_VALID=FAIL"
  FAIL=1
fi

if [[ "$FAIL" -eq 1 ]]; then
  echo "HOSTINGER_SITE_INSTALL_PLAN=FAIL"
  exit 1
fi

echo ""
echo "== PREVIEW =="
head -n 100 "$OUT_MD"

echo ""
echo "== CLOSEOUT =="
echo "HOSTINGER_SITE_INSTALL_PLAN=PASS"
echo "PLAN_ID=$PLAN_ID"
echo "REPORT_MD=$OUT_MD"
echo "REPORT_JSON=$OUT_JSON"
echo "PLAN_DIR=$PLAN_DIR"
echo "DIST_DIR=$DIST_DIR"
