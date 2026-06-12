from pathlib import Path
import re
import subprocess
import sys
import time
from http.server import ThreadingHTTPServer, SimpleHTTPRequestHandler
from threading import Thread
from urllib.request import urlopen


ROOT = Path(__file__).resolve().parents[2]
PAGE = ROOT / "runtime/content/maskzero.site/spark-os/index.html"


def assert_page_source():
    html = PAGE.read_text(encoding="utf-8")
    assert 'data-dreamos-spark-os="clean-static-001"' in html
    assert 'data-action="start"' in html
    assert 'data-answer="${letter}"' in html
    assert 'spark-os-static' in html


def test_with_node_dom_fallback():
    """
    Minimal static proof without network:
    Confirms the shipped HTML includes the required runtime branches.
    This is not enough for final UX proof, but blocks fake deploy confidence.
    """
    assert_page_source()
    html = PAGE.read_text(encoding="utf-8")

    required = [
        "function renderDomain()",
        "function questionCard(q, answers)",
        'app.addEventListener("click"',
        'data-action="start"',
        'data-action="submit-domain"',
        'data-answer="${letter}"',
        "state.domain_answers[String(q)] = val",
        "renderDomain();",
    ]

    missing = [x for x in required if x not in html]
    assert not missing, f"Missing runtime branches: {missing}"


if __name__ == "__main__":
    test_with_node_dom_fallback()
    print("SPARK_OS_STATIC_SOURCE_TRUTH=PASS")
