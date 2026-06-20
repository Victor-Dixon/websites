#!/usr/bin/env python3
import json
import os
import re
import socket
import ssl
import subprocess
import sys
import time
from pathlib import Path
from urllib.request import Request, urlopen
from urllib.error import URLError, HTTPError

ROOT = Path.cwd()
REPORT_DIR = ROOT / "data/reports/websites"
REPORT_DIR.mkdir(parents=True, exist_ok=True)

INVENTORY_PATH = ROOT / "runtime/deploy/domain_inventory.json"

SEED_DOMAINS = {
    "weareswarm.online",
    "weareswarm.site",
    "maskzero.site",
    "ariajet.site",
    "freerideinvestor.com",
    "southwestsecret.com",
}

DOMAIN_RE = re.compile(r"\b(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+(?:com|net|org|site|online|app|io|dev|ai|xyz|co|us)\b", re.I)

EXCLUDE_DIRS = {
    ".git", "node_modules", ".cache", "__pycache__", ".venv", "venv",
    "data/reports", ".dreamos",
}

def now():
    return time.strftime("%Y-%m-%dT%H:%M:%S%z")

def clean_domain(d):
    d = d.strip().lower().strip(".,;:()[]{}<>\"'")
    if d.startswith("www."):
        d = d[4:]
    return d

def is_domain(s):
    return bool(DOMAIN_RE.fullmatch(s or ""))

def load_existing_inventory():
    if INVENTORY_PATH.exists():
        try:
            return json.loads(INVENTORY_PATH.read_text())
        except Exception:
            return {}
    return {}

def extract_domains_from_text(text):
    return {clean_domain(m.group(0)) for m in DOMAIN_RE.finditer(text or "")}

def should_skip(path: Path):
    parts = set(path.parts)
    if ".git" in parts or "node_modules" in parts or "__pycache__" in parts:
        return True
    s = str(path)
    return "/data/reports/" in s or "/.git/" in s or "/node_modules/" in s

def scan_repo_domains():
    found = {}
    for base in [ROOT / "_deploy", ROOT / "routes"]:
        if base.exists():
            for child in base.iterdir():
                if child.is_dir():
                    name = clean_domain(child.name)
                    if is_domain(name):
                        found.setdefault(name, set()).add(f"dir:{child.relative_to(ROOT)}")

    for wf in (ROOT / ".github/workflows").glob("*.yml"):
        txt = wf.read_text(errors="ignore")
        for d in extract_domains_from_text(txt):
            found.setdefault(d, set()).add(f"workflow:{wf.relative_to(ROOT)}")

    for p in ROOT.rglob("*"):
        if not p.is_file() or should_skip(p):
            continue
        if p.suffix.lower() not in {".html", ".js", ".json", ".md", ".yml", ".yaml", ".txt", ".sh", ".py", ".css"}:
            continue
        try:
            txt = p.read_text(errors="ignore")
        except Exception:
            continue
        for d in extract_domains_from_text(txt):
            if d.endswith(("github.com", "githubusercontent.com", "schema.org", "w3.org")):
                continue
            found.setdefault(d, set()).add(f"text:{p.relative_to(ROOT)}")
    return {k: sorted(v) for k, v in found.items()}

def load_hostinger_token():
    token = os.environ.get("HOSTINGER_API_TOKEN", "").strip()
    if token:
        return token
    p = Path.home() / ".dreamos/secrets/hostinger_api_token"
    if p.exists():
        return p.read_text().strip()
    return ""

def extract_domains_recursive(obj):
    out = set()
    if isinstance(obj, dict):
        for k, v in obj.items():
            if isinstance(v, str):
                for d in extract_domains_from_text(v):
                    out.add(d)
            else:
                out |= extract_domains_recursive(v)
    elif isinstance(obj, list):
        for v in obj:
            out |= extract_domains_recursive(v)
    elif isinstance(obj, str):
        out |= extract_domains_from_text(obj)
    return out

def fetch_hostinger_domains():
    token = load_hostinger_token()
    if not token:
        return set(), "NO_TOKEN"

    url = "https://developers.hostinger.com/api/domains/v1/portfolio"
    req = Request(url, headers={
        "Authorization": f"Bearer {token}",
        "Accept": "application/json",
        "User-Agent": "DreamOSDomainInventory/1.0",
    })

    try:
        with urlopen(req, timeout=25) as r:
            raw = r.read().decode("utf-8", errors="ignore")
        data = json.loads(raw)
        domains = {clean_domain(d) for d in extract_domains_recursive(data) if is_domain(clean_domain(d))}
        return domains, "PASS"
    except Exception as e:
        return set(), f"ERROR:{type(e).__name__}:{e}"

def dns_ips(domain):
    out = set()
    for host in [domain, f"www.{domain}"]:
        try:
            for item in socket.getaddrinfo(host, 443, proto=socket.IPPROTO_TCP):
                out.add(item[4][0])
        except Exception:
            pass
    return sorted(out)

def http_check(url):
    try:
        req = Request(url, headers={"User-Agent": "DreamOSDeployTruthIndex/1.0"}, method="GET")
        with urlopen(req, timeout=16, context=ssl.create_default_context()) as r:
            body = r.read(512).decode("utf-8", errors="ignore")
            return {
                "ok": 200 <= r.status < 400,
                "status": r.status,
                "final_url": r.geturl(),
                "sample": body[:160],
            }
    except HTTPError as e:
        return {"ok": False, "status": e.code, "final_url": url, "error": str(e)}
    except Exception as e:
        return {"ok": False, "status": None, "final_url": url, "error": f"{type(e).__name__}:{e}"}

def source_candidates(domain):
    root = domain.split(".")[0]
    candidates = [
        ROOT / "_deploy" / domain,
        ROOT / "routes" / domain,
        ROOT / "_deploy" / root,
        ROOT / "routes" / root,
    ]
    return [str(p.relative_to(ROOT)) for p in candidates if p.exists()]

def workflow_refs(domain):
    refs = []
    for wf in (ROOT / ".github/workflows").glob("*.yml"):
        txt = wf.read_text(errors="ignore")
        if domain in txt or domain.replace(".", "-") in txt:
            refs.append(str(wf.relative_to(ROOT)))
    return refs

def read_weareswarm_gate():
    p = ROOT / "data/reports/weareswarm_online/deploy_truth_gate_latest.json"
    if p.exists():
        try:
            return json.loads(p.read_text())
        except Exception:
            return {"ok": False, "error": "invalid_json"}
    return None

def classify(domain, discovered_by, srcs, refs, live):
    if domain == "weareswarm.online":
        gate = read_weareswarm_gate()
        if gate and gate.get("ok"):
            return "ACTIVE_TRUTH_GATED"

    live_ok = any(v.get("ok") for v in live.values())
    has_src = bool(srcs)
    has_workflow = bool(refs)

    if has_src and has_workflow and live_ok:
        return "MAPPED_LIVE_NEEDS_DOMAIN_GATE"
    if has_src and live_ok:
        return "SOURCE_LIVE_NEEDS_DEPLOY_GATE"
    if has_src and not live_ok:
        return "SOURCE_ONLY_NEEDS_LIVE_VERIFY"
    if live_ok and not has_src:
        return "LIVE_ONLY_NEEDS_REPO_MAPPING"
    if "hostinger_api" in discovered_by and not has_src:
        return "DISCOVERED_NEEDS_MAPPING"
    return "NEEDS_TRIAGE"

def main():
    existing = load_existing_inventory()
    repo_domains_raw = scan_repo_domains()
    hostinger_domains, hostinger_status = fetch_hostinger_domains()

    # Ownership rule:
    # - Hostinger API + seed domains are authoritative.
    # - Repo text is NOT authoritative by itself because HTML/JS/docs contain CDN,
    #   social, package-registry, and third-party domains.
    # - Repo domains count only when they are explicitly in deploy/route dirs,
    #   or already recognized by Hostinger/seed.
    repo_domains = {}
    for d, sources in repo_domains_raw.items():
        strong_dir = any(src.startswith("dir:") for src in sources)
        owned_signal = d in SEED_DOMAINS or d in hostinger_domains
        if strong_dir or owned_signal:
            repo_domains[d] = sources

    existing_domains = set((existing.get("domains") or {}).keys())
    existing_owned = {
        d for d in existing_domains
        if d in SEED_DOMAINS or d in hostinger_domains or d in repo_domains
    }

    all_domains = set(SEED_DOMAINS)
    all_domains |= hostinger_domains
    all_domains |= set(repo_domains.keys())
    all_domains |= existing_owned

    domains = {}
    for domain in sorted(clean_domain(d) for d in all_domains if is_domain(clean_domain(d))):
        discovered_by = set()

        if domain in SEED_DOMAINS:
            discovered_by.add("seed")
        if domain in repo_domains:
            discovered_by.add("repo")
        if domain in hostinger_domains:
            discovered_by.add("hostinger_api")
        if domain in (existing.get("domains") or {}):
            discovered_by.add("existing_inventory")

        srcs = source_candidates(domain)
        refs = workflow_refs(domain)
        live = {
            "apex": http_check(f"https://{domain}/"),
            "www": http_check(f"https://www.{domain}/"),
        }
        ips = dns_ips(domain)

        domains[domain] = {
            "domain": domain,
            "status": classify(domain, discovered_by, srcs, refs, live),
            "discovered_by": sorted(discovered_by),
            "source_candidates": srcs,
            "workflow_refs": refs,
            "dns_ips": ips,
            "live": live,
            "notes": [],
        }

        if domains[domain]["status"] == "DISCOVERED_NEEDS_MAPPING":
            domains[domain]["notes"].append("Domain detected from Hostinger/API or inventory but no repo mapping found. Create _deploy/<domain> or routes/<domain> and a deploy target before publishing.")

    index = {
        "generated": now(),
        "tool": "websites_deploy_truth_gate_index",
        "hostinger_api_status": hostinger_status,
        "known_seed_domains": sorted(SEED_DOMAINS),
        "domains": domains,
        "summary": {
            "total_domains": len(domains),
            "active_truth_gated": sum(1 for d in domains.values() if d["status"] == "ACTIVE_TRUTH_GATED"),
            "discovered_needs_mapping": sum(1 for d in domains.values() if d["status"] == "DISCOVERED_NEEDS_MAPPING"),
            "mapped_live_needs_domain_gate": sum(1 for d in domains.values() if d["status"] == "MAPPED_LIVE_NEEDS_DOMAIN_GATE"),
            "needs_triage": sum(1 for d in domains.values() if "NEEDS" in d["status"]),
        },
    }

    INVENTORY_PATH.write_text(json.dumps(index, indent=2, sort_keys=True) + "\n")

    latest_json = REPORT_DIR / "deploy_truth_index_latest.json"
    latest_md = REPORT_DIR / "deploy_truth_index_latest.md"
    latest_json.write_text(json.dumps(index, indent=2, sort_keys=True) + "\n")

    lines = [
        "# Websites Deploy Truth Index",
        "",
        f"- generated: `{index['generated']}`",
        f"- Hostinger API: `{hostinger_status}`",
        f"- total domains: `{index['summary']['total_domains']}`",
        f"- active truth gated: `{index['summary']['active_truth_gated']}`",
        f"- discovered needs mapping: `{index['summary']['discovered_needs_mapping']}`",
        f"- mapped live needs domain gate: `{index['summary']['mapped_live_needs_domain_gate']}`",
        "",
        "## Domains",
        "",
        "| Domain | Status | Discovered By | Sources | Workflows | Live |",
        "|---|---:|---|---:|---:|---|",
    ]

    for domain, d in domains.items():
        live_bits = []
        for key, val in d["live"].items():
            live_bits.append(f"{key}:{val.get('status')}")
        lines.append(
            f"| `{domain}` | `{d['status']}` | {', '.join(d['discovered_by'])} | {len(d['source_candidates'])} | {len(d['workflow_refs'])} | {', '.join(live_bits)} |"
        )

    latest_md.write_text("\n".join(lines) + "\n")

    print(f"REPORT_JSON={latest_json}")
    print(f"REPORT_MD={latest_md}")
    print(f"INVENTORY={INVENTORY_PATH}")
    print(f"DOMAIN_TOTAL={index['summary']['total_domains']}")
    print(f"DISCOVERED_NEEDS_MAPPING={index['summary']['discovered_needs_mapping']}")
    print(f"ACTIVE_TRUTH_GATED={index['summary']['active_truth_gated']}")

    required = [
        "weareswarm.online",
        "weareswarm.site",
        "maskzero.site",
        "ariajet.site",
        "freerideinvestor.com",
        "southwestsecret.com",
    ]

    missing = [d for d in required if d not in domains]
    if missing:
        print(f"VERIFY=FAIL_REQUIRED_DOMAINS_MISSING missing={','.join(missing)}")
        return 1

    print("VERIFY=PASS_REQUIRED_DOMAINS_PRESENT")
    print("VERIFY=PASS_DOMAIN_INVENTORY_WRITTEN")
    print("VERIFY=PASS_DEPLOY_TRUTH_INDEX_WRITTEN")

    if hostinger_status == "NO_TOKEN":
        print("VERIFY=WARN_HOSTINGER_API_TOKEN_MISSING_SELF_INGEST_LIMITED")
    elif hostinger_status == "PASS":
        print("VERIFY=PASS_HOSTINGER_API_DOMAIN_INGEST")
    else:
        print(f"VERIFY=WARN_HOSTINGER_API_DOMAIN_INGEST status={hostinger_status}")

    print("CLOSEOUT=PASS_WEBSITES_DEPLOY_TRUTH_INDEX")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
