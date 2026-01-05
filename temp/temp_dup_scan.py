import hashlib, json
from pathlib import Path

ROOTS = [Path("websites"), Path("sites"), Path("Swarm_website")]
ROOTS = [p for p in ROOTS if p.exists()]
SKIP = {".git", "node_modules", ".venv", "__pycache__", ".pytest_cache", "dist", "build"}
HASHES = {}

def sha(p: Path):
    h = hashlib.sha256()
    with p.open("rb") as f:
        for chunk in iter(lambda: f.read(1024*1024), b""):
            h.update(chunk)
    return h.hexdigest()

for root in ROOTS:
    for p in root.rglob("*"):
        if p.is_dir():
            continue
        if any(part in SKIP for part in p.parts):
            continue
        if p.suffix.lower() in {".png",".jpg",".jpeg",".gif",".zip",".pdf",".woff",".woff2"}:
            continue
        try:
            h = sha(p)
        except Exception:
            continue
        HASHES.setdefault(h, []).append(str(p))

dups = [{"hash": h, "count": len(v), "paths": v} for h, v in HASHES.items() if len(v) > 1]
dups.sort(key=lambda x: x["count"], reverse=True)

Path("reports").mkdir(exist_ok=True)
Path("reports/websites_dup_scan.json").write_text(json.dumps(dups, indent=2))
print("Duplicate groups:", len(dups))
for g in dups[:10]:
    print("\n", g["count"], "files")
    for p in g["paths"][:6]:
        print("  -", p)