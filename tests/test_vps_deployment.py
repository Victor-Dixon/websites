"""
VPS deployment package checks for Dream.OS websites runtime.

Run with: pytest tests/test_vps_deployment.py -v
"""

from __future__ import annotations

import json
import re
import subprocess
from pathlib import Path

import pytest

REPO_ROOT = Path(__file__).resolve().parents[1]
VPS_ROOT = REPO_ROOT / "deploy" / "vps" / "websites"
VPS_SCRIPTS = VPS_ROOT / "scripts"

REQUIRED_ENV_KEYS = [
    "DREAMOS_ROOT",
    "WEBSITES_REPO",
    "DREAMVAULT_REPO",
    "DREAM_DATA_VAULT_REPO",
    "DREAMOS_BRAIN_REPO",
    "DASHBOARD_RUNTIME_DIR",
    "SITE_PREVIEW_PORT",
    "PUBLIC_SITE_ROOT",
]

REQUIRED_SCRIPTS = [
    "install.sh",
    "healthcheck.sh",
    "preview.sh",
    "export_dashboard_inputs.sh",
    "lib.sh",
]

LOCAL_PATH_PATTERNS = [
    re.compile(r"D:\\", re.I),
    re.compile(r"D:/", re.I),
    re.compile(r"/data/data/com\.termux", re.I),
]


class TestVpsPackageLayout:
    def test_vps_scripts_exist(self):
        for name in REQUIRED_SCRIPTS:
            path = VPS_SCRIPTS / name
            assert path.is_file(), f"missing VPS script: {path}"

    def test_env_example_has_required_vars(self):
        env_example = (VPS_ROOT / ".env.example").read_text(encoding="utf-8")
        for key in REQUIRED_ENV_KEYS:
            assert f"{key}=" in env_example, f"missing env key in .env.example: {key}"

    def test_nginx_example_exists(self):
        nginx = VPS_ROOT / "nginx" / "dreamos-sites.conf.example"
        assert nginx.is_file()
        text = nginx.read_text(encoding="utf-8")
        assert "/var/www/dreamos-sites" in text
        assert "location /data/" in text

    def test_vps_readme_documents_hostinger_coexistence(self):
        readme = (VPS_ROOT / "README.md").read_text(encoding="utf-8")
        assert "Hostinger" in readme
        assert "GitHub Actions" in readme


class TestVpsScriptsPathNeutral:
    @pytest.mark.parametrize("script_name", REQUIRED_SCRIPTS)
    def test_no_windows_or_termux_hardcodes(self, script_name: str):
        text = (VPS_SCRIPTS / script_name).read_text(encoding="utf-8")
        for pattern in LOCAL_PATH_PATTERNS:
            assert not pattern.search(text), (
                f"{script_name} contains hardcoded local path: {pattern.pattern}"
            )


class TestJsonValidation:
    def test_public_json_files_are_valid(self):
        public_dir = REPO_ROOT / "public"
        if not public_dir.is_dir():
            pytest.skip("public/ not present")
        json_files = list(public_dir.glob("*.json"))
        if not json_files:
            pytest.skip("no public JSON files")
        for path in json_files:
            json.loads(path.read_text(encoding="utf-8"))

    def test_env_example_has_no_real_secrets(self):
        text = (VPS_ROOT / ".env.example").read_text(encoding="utf-8")
        assert "sk-" not in text
        for line in text.splitlines():
            if "=" not in line or line.strip().startswith("#"):
                continue
            _, _, value = line.partition("=")
            assert not value.strip().startswith("ghp_"), f"suspected token in .env.example: {line}"


class TestDashboardExportLogic:
    def test_sanitize_removes_secret_keys(self, tmp_path: Path):
        src = tmp_path / "health.json"
        dest = tmp_path / "out.json"
        payload = {
            "status": "ok",
            "api_key": "should-be-removed",
            "nested": {"webhook_url": "https://example.com/hook", "count": 3},
        }
        src.write_text(json.dumps(payload), encoding="utf-8")

        py = f"""
import json
from pathlib import Path
SECRET_HINTS = ("password", "secret", "token", "api_key", "apikey", "private_key", "webhook", "credential", "authorization")
def is_secret_key(key):
    lower = key.lower()
    return any(h in lower for h in SECRET_HINTS)
def scrub(value):
    if isinstance(value, dict):
        return {{k: scrub(v) for k, v in value.items() if not is_secret_key(str(k))}}
    if isinstance(value, list):
        return [scrub(i) for i in value]
    return value
src = Path({str(src)!r})
dest = Path({str(dest)!r})
data = json.loads(src.read_text(encoding="utf-8"))
dest.write_text(json.dumps(scrub(data), indent=2) + "\\n", encoding="utf-8")
"""
        subprocess.run(["python", "-c", py], check=True)
        cleaned = json.loads(dest.read_text(encoding="utf-8"))
        assert cleaned["status"] == "ok"
        assert "api_key" not in cleaned
        assert "webhook_url" not in cleaned["nested"]
        assert cleaned["nested"]["count"] == 3


class TestPreviewPathResolution:
    def test_preview_defaults_use_env_not_windows_paths(self):
        preview = (VPS_SCRIPTS / "preview.sh").read_text(encoding="utf-8")
        assert "SITE_PREVIEW_ROOT" in preview
        assert "PUBLIC_SITE_ROOT" in preview
        assert "127.0.0.1" in preview
        assert '${WEBSITES_REPO}/public' in preview

    def test_healthcheck_checks_sites_yml(self):
        health = (VPS_SCRIPTS / "healthcheck.sh").read_text(encoding="utf-8")
        assert "sites.yml" in health
        assert "DASHBOARD_RUNTIME_DIR" in health
