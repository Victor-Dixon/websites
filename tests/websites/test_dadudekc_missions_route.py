from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
CONTENT = ROOT / "runtime/content/dadudekc.site"
MISSIONS = CONTENT / "missions/index.html"


def test_missions_route_source_exists():
    html = MISSIONS.read_text(encoding="utf-8")

    assert 'data-dreamos-missions-route="static-001"' in html
    assert "Meridian Mission Board" in html
    assert "data-action=\"select-mission\"" in html
    assert "dreamos.currentMission.v1" in html


def test_key_pages_link_to_missions_route():
    pages = [
        CONTENT / "home.html",
        CONTENT / "the-emergence.html",
        CONTENT / "spark-os/index.html",
        CONTENT / "spark-generator/index.html",
        CONTENT / "battles.html",
        CONTENT / "battles/index.html",
        CONTENT / "protocol.html",
    ]

    missing = [
        str(page.relative_to(ROOT))
        for page in pages
        if 'href="/missions/"' not in page.read_text(encoding="utf-8")
    ]

    assert not missing, f"Pages missing /missions/ links: {missing}"
