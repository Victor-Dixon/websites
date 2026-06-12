from pathlib import Path
import json


ROOT = Path(__file__).resolve().parents[2]
CONFIG = ROOT / "config/site_configs.json"
DEPLOY_MODES = ROOT / "runtime/config/website_deploy_modes.yaml"
MIGRATION_TASK = ROOT / "runtime/tasks/maskzero_home_migration_001.yaml"
DADUDEKC = ROOT / "runtime/content/dadudekc.site"
MASKZERO = ROOT / "runtime/content/maskzero.site"


def test_dadudekc_site_is_redirect_shell_to_maskzero_ssot():
    sites = json.loads(CONFIG.read_text(encoding="utf-8"))
    dadudekc = sites["dadudekc.site"]

    assert dadudekc["path"] == "runtime/content/dadudekc.site"
    assert dadudekc["canonical_target"] == "https://maskzero.site"
    assert dadudekc["deploy_files"] == [
        "runtime/content/dadudekc.site/.htaccess",
        "runtime/content/dadudekc.site/index.html",
    ]
    assert sites["maskzero.site"]["path"] == "runtime/content/maskzero.site"


def test_dadudekc_redirect_preserves_stale_project_paths():
    htaccess = (DADUDEKC / ".htaccess").read_text(encoding="utf-8")
    index = (DADUDEKC / "index.html").read_text(encoding="utf-8")

    assert "RewriteRule ^(.*)$ https://maskzero.site/$1 [R=301,L,NE]" in htaccess
    assert "data-canonical-project-redirect" in index
    assert "Spark, Emergence, and Mask Zero are the same project" in index
    assert "https://maskzero.site/" in index


def test_spark_and_emergence_pages_are_canonical_maskzero_content():
    required_pages = [
        MASKZERO / "spark-os/index.html",
        MASKZERO / "spark-generator/index.html",
        MASKZERO / "the-emergence.html",
        MASKZERO / "character-generator.html",
        MASKZERO / "battles/index.html",
        MASKZERO / "missions/index.html",
    ]

    missing = [str(page.relative_to(ROOT)) for page in required_pages if not page.exists()]
    assert not missing, f"Mask Zero canonical pages missing: {missing}"

    dadudekc_pages = [p for p in DADUDEKC.rglob("*") if p.is_file() and p.name not in {".htaccess", "index.html"}]
    assert not dadudekc_pages, f"dadudekc.site must only contain redirect shell files: {dadudekc_pages}"


def test_migration_registry_documents_current_redirect_decision():
    deploy_modes = DEPLOY_MODES.read_text(encoding="utf-8")
    task = MIGRATION_TASK.read_text(encoding="utf-8")

    assert "dadudekc.site:" in deploy_modes
    assert "deploy_policy: domain_redirect_only" in deploy_modes
    assert "redirect_dadudekc_to_maskzero: true" in task
    assert "preserve_dadudekc_as_separate_site: false" in task
