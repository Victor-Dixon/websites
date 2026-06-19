import hashlib
import json
import re
from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]

EXPECTED_PROTOCOL_VERSION = "Spark Protocol v8.6"
EXPECTED_PROTOCOL_QUESTION_HASH = "24bc3d7f45ef02866048ded58fe7715d638d966b13a74349b2565cde46f3fc00"
EXPECTED_FLAVOR_BLOCKS = {
    **{q: "Titan" for q in range(29, 34)},
    **{q: "Velocity" for q in range(34, 39)},
    **{q: "Energy" for q in range(39, 44)},
    **{q: "Specter" for q in range(44, 49)},
    **{q: "Duality" for q in range(49, 54)},
    **{q: "Omni" for q in range(54, 59)},
    **{q: "Primal" for q in range(59, 64)},
    **{q: "Mind" for q in range(64, 69)},
}

QUIZ_BANK_PAGES = [
    ROOT / "runtime/content/maskzero.site/quiz/index.html",
    ROOT / "runtime/content/maskzero.site/spark-generator/index.html",
    ROOT / "runtime/content/maskzero.site/spark-os/index.html",
    ROOT / "runtime/content/dadudekc.site/spark-generator/index.html",
    ROOT / "runtime/content/dadudekc.site/spark-os/index.html",
]


def _bank_from_page(path: Path) -> dict:
    html = path.read_text(encoding="utf-8")
    match = re.search(
        r'<script id="(?:bank|spark-question-bank)" type="application/json">(.*?)</script>',
        html,
        re.S,
    )
    assert match, f"Missing embedded Spark question bank: {path}"
    return json.loads(match.group(1))


def _question_hash(bank: dict) -> str:
    questions = [
        {"q": q["q"], "question": q["question"], "options": q["options"]}
        for q in bank["domain_questions"] + bank["flavor_questions"]
    ]
    payload = json.dumps(questions, ensure_ascii=False, sort_keys=True, separators=(",", ":"))
    return hashlib.sha256(payload.encode("utf-8")).hexdigest()


def test_quiz_banks_match_protocol_v8_6_questionnaire_exactly():
    for path in QUIZ_BANK_PAGES:
        bank = _bank_from_page(path)

        assert bank["source"] == "Protocol_v8_6_f2fc.md"
        assert bank["protocol_version"] == EXPECTED_PROTOCOL_VERSION
        assert bank["questionnaire"] == "28 Domain + 5-per-block Adaptive Flavor"
        assert len(bank["domain_questions"]) == 28
        assert len(bank["flavor_questions"]) == 40
        assert _question_hash(bank) == EXPECTED_PROTOCOL_QUESTION_HASH


def test_adaptive_flavor_questions_are_locked_to_protocol_blocks():
    for path in QUIZ_BANK_PAGES:
        bank = _bank_from_page(path)
        flavor_blocks = {q["q"]: q["domain"] for q in bank["flavor_questions"]}

        assert flavor_blocks == EXPECTED_FLAVOR_BLOCKS
        assert all(q["block"] == f"{q['domain']} Block" for q in bank["flavor_questions"])
