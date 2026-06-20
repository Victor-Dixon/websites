from pathlib import Path
import json
import re


ROOT = Path(__file__).resolve().parents[2]
CONFIG = ROOT / "config/site_configs.json"
DEPLOY_MODES = ROOT / "runtime/config/website_deploy_modes.yaml"
MIGRATION_TASK = ROOT / "runtime/tasks/maskzero_home_migration_001.yaml"
DADUDEKC = ROOT / "runtime/content/dadudekc.site"
MASKZERO = ROOT / "runtime/content/maskzero.site"


def test_dadudekc_site_is_standalone_static_site():
    sites = json.loads(CONFIG.read_text(encoding="utf-8"))
    dadudekc = sites["dadudekc.site"]

    assert dadudekc["path"] == "runtime/content/dadudekc.site"
    assert dadudekc["site_role"] == "standalone_static_site"
    assert "canonical_target" not in dadudekc
    assert "runtime/content/dadudekc.site/index.html" in dadudekc["deploy_files"]
    assert "runtime/content/dadudekc.site/spark-generator/index.html" in dadudekc["deploy_files"]
    assert sites["maskzero.site"]["path"] == "runtime/content/maskzero.site"


def test_dadudekc_no_longer_redirects_to_maskzero():
    htaccess = (DADUDEKC / ".htaccess").read_text(encoding="utf-8")
    index = (DADUDEKC / "index.html").read_text(encoding="utf-8")

    assert "maskzero.site" not in htaccess
    assert "https://maskzero.site/" not in index
    assert 'data-dadudekc-site="standalone"' in index
    assert "DadudeKC standalone site" in index
    assert "The Emergence" in index


def test_spark_and_emergence_pages_are_canonical_maskzero_content():
    required_pages = [
        MASKZERO / "spark-os/index.html",
        MASKZERO / "quiz/index.html",
        MASKZERO / "spark-generator/index.html",
        MASKZERO / "the-emergence.html",
        MASKZERO / "character-generator.html",
        MASKZERO / "battles/index.html",
        MASKZERO / "missions/index.html",
    ]

    missing = [str(page.relative_to(ROOT)) for page in required_pages if not page.exists()]
    assert not missing, f"Mask Zero canonical pages missing: {missing}"

    dadudekc_pages = [p for p in DADUDEKC.rglob("*") if p.is_file() and p.name not in {".htaccess", "index.html"}]
    assert dadudekc_pages, "dadudekc.site must keep its standalone site files"


def test_migration_registry_documents_current_redirect_decision():
    deploy_modes = DEPLOY_MODES.read_text(encoding="utf-8")
    task = MIGRATION_TASK.read_text(encoding="utf-8")

    assert "dadudekc.site:" in deploy_modes
    assert "deploy_policy: standalone_static_site" in deploy_modes
    assert "redirect_dadudekc_to_maskzero: false" in task
    assert "preserve_dadudekc_as_separate_site: true" in task


def test_maskzero_canonical_brand_contract_static_ssot():
    index = (MASKZERO / "index.html").read_text(encoding="utf-8")
    htaccess = (MASKZERO / ".htaccess").read_text(encoding="utf-8")

    assert "<title>MaskZero | Spark Protocol in Meridian City</title>" in index
    assert '<h1 id="home-title">MaskZero</h1>' in index
    assert "Create your Spark. Enter Meridian City. Answer the Dispatch." in index
    assert "MaskZero · Spark Protocol v8.6" in index
    assert "Created by WeAreSwarm · Powered by Dream.OS" in index
    assert r"RewriteCond %{HTTP_HOST} ^www\.maskzero\.site$ [NC]" in htaccess
    assert "RewriteRule ^(.*)$ https://maskzero.site/$1 [R=301,L,NE]" in htaccess


def test_maskzero_required_routes_have_static_sources():
    routes = [
        "index.html",
        "create-hero/index.html",
        "quiz/index.html",
        "how-it-works/index.html",
        "origin-rules/index.html",
        "roster-rules/index.html",
        "login/index.html",
        "spark-account/index.html",
        "meridian-map/index.html",
        "dispatch/index.html",
    ]
    missing = [route for route in routes if not (MASKZERO / route).exists()]
    assert not missing, f"Missing MaskZero route sources: {missing}"

    for route in routes:
        html = (MASKZERO / route).read_text(encoding="utf-8")
        assert "MaskZero" in html
        assert "Created by WeAreSwarm · Powered by Dream.OS" in html
        assert "Loading" not in html


def test_maskzero_login_hands_off_to_spark_account_flow():
    sites = json.loads(CONFIG.read_text(encoding="utf-8"))
    htaccess = (MASKZERO / ".htaccess").read_text(encoding="utf-8")
    login = (MASKZERO / "login/index.html").read_text(encoding="utf-8")

    assert "RewriteRule ^login/?$ /spark-login/?redirect_to=%2Fspark-dashboard%2F [R=302,L,NE]" in htaccess
    assert "RewriteRule ^wp-login\\.php$ /spark-login/?redirect_to=%2Fspark-dashboard%2F [R=302,L,NE]" in htaccess
    assert "RewriteRule ^\\.spark-auth(/.*)?$ - [F,L]" in htaccess
    assert "RewriteRule ^the-emergence/?$ /the-emergence.html [L]" in htaccess
    assert "RewriteRule ^meridian-dispatch/?$ /dispatch/ [R=302,L,NE]" in htaccess
    assert "RewriteRule ^spark-battle/?$ /battles/ [R=302,L,NE]" in htaccess
    assert "runtime/content/maskzero.site/api/spark-auth.php" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/spark-login/index.html" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/spark-logout/index.html" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/spark-signup/index.html" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/spark-account/index.html" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/spark-dashboard/index.html" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/assets/css/maskzero-comic-theme.css" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/assets/css/spark-site-shell.css" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/assets/js/spark-account-runtime.js" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/assets/js/spark-dashboard.js" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/assets/js/spark-site-shell.js" in sites["maskzero.site"]["deploy_files"]
    assert '<link rel="canonical" href="https://maskzero.site/spark-login/">' in login
    assert "url=/spark-login/?redirect_to=%2Fspark-dashboard%2F" in login
    spark_login = (MASKZERO / "spark-login/index.html").read_text(encoding="utf-8")
    assert "Log In" in spark_login
    assert 'target="sparkLoginFrame"' in spark_login
    assert 'name="sparkLoginFrame"' in spark_login
    assert "event.preventDefault()" in spark_login
    assert 'action="/api/spark-auth.php?action=login"' in spark_login
    assert 'fetch("/api/spark-auth.php?action=login"' in spark_login
    assert 'fetch("/wp-login.php"' not in spark_login
    assert 'href="/spark-login/?help=lost-password"' in spark_login
    assert 'href="/wp-login.php?action=lostpassword"' not in spark_login
    assert 'window.location.href = target' in spark_login
    assert "Login did not complete" in spark_login
    assert 'id="sparkLoginDebugLog"' in spark_login
    assert "Login debug started. Passwords are never logged." in spark_login
    assert "MaskZero auth response" in spark_login
    assert "detectAuthClues" in spark_login
    assert "password_supplied" in spark_login
    assert 'document.getElementById("user_pass").value' in spark_login
    assert "password:" not in spark_login.lower()
    signup = (MASKZERO / "spark-signup/index.html").read_text(encoding="utf-8")
    runtime = (MASKZERO / "assets/js/spark-account-runtime.js").read_text(encoding="utf-8")
    auth_api = (MASKZERO / "api/spark-auth.php").read_text(encoding="utf-8")
    logout = (MASKZERO / "spark-logout/index.html").read_text(encoding="utf-8")
    assert "Create Account" in signup
    assert 'fetch("/api/spark-auth.php?action=register"' in signup
    assert '"/api/spark-auth.php?action=session"' in runtime
    assert "maskzero_spark_session" in auth_api
    assert "password_hash" in auth_api
    assert "password_verify" in auth_api
    assert "/.spark-auth" in auth_api
    assert 'fetch("/api/spark-auth.php?action=logout"' in logout
    assert "Spark Account" in (MASKZERO / "spark-account/index.html").read_text(encoding="utf-8")
    assert "Command Post" in (MASKZERO / "spark-dashboard/index.html").read_text(encoding="utf-8")


def test_maskzero_migrated_pages_use_comic_book_skin():
    pages = [
        "index.html",
        "create-hero/index.html",
        "how-it-works/index.html",
        "dispatch/index.html",
        "meridian-map/index.html",
        "origin-rules/index.html",
        "roster-rules/index.html",
        "spark-account/index.html",
        "quiz/index.html",
        "spark-generator/index.html",
        "spark-os/index.html",
        "missions/index.html",
        "battles/index.html",
    ]

    missing = []
    for route in pages:
        html = (MASKZERO / route).read_text(encoding="utf-8")
        class_tokens = re.findall(r'class=["\']([^"\']+)["\']', html)
        has_comic_skin = any("maskzero-comic-skin" in tokens.split() for tokens in class_tokens)
        if not has_comic_skin or "/assets/css/maskzero-comic-theme.css" not in html:
            missing.append(route)

    assert not missing, f"MaskZero pages missing comic skin: {missing}"


def test_maskzero_quiz_restores_migrated_spark_flow_as_primary_route():
    sites = json.loads(CONFIG.read_text(encoding="utf-8"))
    quiz = MASKZERO / "quiz/index.html"
    spark_alias = MASKZERO / "spark-generator/index.html"
    quiz_html = quiz.read_text(encoding="utf-8")
    spark_html = spark_alias.read_text(encoding="utf-8")

    assert "runtime/content/maskzero.site/quiz/index.html" in sites["maskzero.site"]["deploy_files"]
    assert "runtime/content/maskzero.site/spark-generator/index.html" in sites["maskzero.site"]["deploy_files"]
    assert '<link rel="canonical" href="https://maskzero.site/quiz/">' in quiz_html
    assert '<link rel="canonical" href="https://maskzero.site/quiz/">' in spark_html
    assert 'data-maskzero-quiz="dadudekc-migration"' in quiz_html
    assert '"protocol_version":"Spark Protocol v8.6"' in quiz_html
    assert "Start Spark Quiz" in quiz_html
    assert "Spark.submitDomain()" in quiz_html
    assert "Spark.submitFlavor()" in quiz_html
    assert "MaskZero quiz ready" in quiz_html
    assert quiz_html == spark_html


def test_maskzero_public_sources_do_not_reference_old_domain():
    public_files = list(MASKZERO.rglob("*.html")) + list((MASKZERO / "assets").rglob("*.js"))
    offenders = [
        str(path.relative_to(ROOT))
        for path in public_files
        if "dadudekc.site" in path.read_text(encoding="utf-8", errors="ignore").lower()
    ]
    assert not offenders, f"MaskZero public files reference dadudekc.site: {offenders}"
