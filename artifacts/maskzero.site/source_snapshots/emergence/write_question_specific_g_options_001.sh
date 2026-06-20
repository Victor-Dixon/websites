#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== WRITE QUESTION-SPECIFIC G OPTIONS =="

python - << 'PY'
import json
from pathlib import Path

quiz_path = Path("data/quiz/questions.json")
data = json.loads(quiz_path.read_text())

g_map = {
  1: "G. I immediately start seeing how all the pieces could connect and what this changes for everyone involved.",
  2: "G. I zoom out and map the insult into the larger pattern between us before I respond.",
  3: "G. That I can connect separate problems into one system and find the leverage point.",
  4: "G. I become the quiet center people orbit around, linking conversations and energy without forcing it.",
  5: "G. How well I can coordinate different strengths, roles, and possibilities into one working response.",
  6: "G. Integrative -- I pull every available thread into alignment until the goal has a path.",
  7: "G. Seeing how people, timing, and resources fit together better than anyone expected.",
  8: "G. My pattern sense -- I notice how everything is connected before others do.",
  9: "G. A systems view -- I start reading wind, distance, people, exits, and consequences all at once.",
  10: "G. To hold the whole field in mind and shift every piece at exactly the right moment.",
  11: "G. By finding the hidden connection point between what they need, what I can offer, and what the moment allows.",
  12: "G. A living network around me, every path and person connected without anything feeling forced.",
  13: "G. Identify how the whole matchup works, then shift the structure so my win becomes inevitable.",
  14: "G. I reconnect scattered parts of myself and rebuild from the whole pattern, not one wound.",
  15: "G. I connect the right supports, sequence the work, and make the responsibility sustainable.",
  16: "G. Being the unseen axis that lets everyone and everything move together.",
  17: "G. Build momentum by connecting the right pieces until the goal starts pulling itself forward.",
  18: "G. My systems sense; I track relationships between moving parts and stabilize the whole field.",
  19: "G. Keeping people, purpose, and structure connected long enough for the outcome to survive.",
  20: "G. Let them miss that I understand the entire room, not just my own position in it.",
  21: "G. Keeping every piece connected to the larger purpose when pressure tries to scatter me.",
  22: "G. Linking the entire field so distance, people, and timing all answer to one intent.",
  23: "G. I redistribute pressure through everything connected to me until no single part has to break.",
  24: "G. A need to make separate things click into one larger pattern that finally makes sense.",
  25: "G. It shows in how I connect people, openings, and consequences into something stronger than force.",
  26: "G. I make the whole situation work against them while making every part support me.",
  27: "G. Connection -- the invisible structure between people, places, timing, and intent.",
  28: "G. Making every separate piece of the situation point toward the same unavoidable result.",
  29: "G. I keep the web between us alive, making sure no one feels disconnected or stranded.",
  30: "G. Convergence -- many separate movements becoming one clear direction.",
  31: "G. I connect foundations, people, and timing so the thing can keep growing without me forcing it.",
  32: "G. Link every route, ally, and resource around them so danger has no clean path in.",
  33: "G. Networked -- I wait by watching how every piece is moving toward the right moment.",
  34: "G. Becoming the organizing center that helps everyone else find their place.",
  35: "G. I reconnect with my people, my purpose, and the larger pattern until I feel aligned again.",
  36: "G. That I brought separate people, moments, and forces together into something that lasted.",
  37: "G. I look for who and what can share the weight so the task becomes coordinated instead of isolated.",
  38: "G. Coordinating -- I make separate efforts reinforce each other under pressure.",
  39: "G. That I can turn hardship into a shared structure where every part supports another.",
  40: "G. The one who made everyone stronger by knowing how they all fit together.",
  41: "G. Reframe it as part of a larger system and find the connection that makes it movable.",
  42: "G. The center point that gathers strain from every direction and redirects it into motion.",
  43: "G. That I can read the whole field fast enough to coordinate the right move before the moment closes.",
  44: "G. I use it to connect timing, routes, and people so I can move at the exact right second.",
  45: "G. Synchronizing many moving pieces quickly enough that the opening appears all at once.",
  46: "G. I coordinate route, timing, and support so the jam unravels instead of needing brute speed.",
  47: "G. That I can connect movement, timing, and opportunity into one clean escape or advance.",
  48: "G. I understand how every move links together, then shift the chain before they notice.",
  49: "G. I contain it long enough to understand the whole pattern, then redirect it where it matters.",
  50: "G. By connecting the feeling to the bigger truth behind it instead of just venting the heat.",
  51: "G. Like a charged field -- everyone can feel how the whole room is being pulled into alignment.",
  52: "G. I tie every point together until the conclusion feels impossible to separate from the facts.",
  53: "G. A field of pressure that organizes everything nearby around one unmistakable intent.",
  54: "G. A magnetic field -- invisible, connective, and impossible to ignore once you feel it.",
  55: "G. I read the social structure, find the safest connection, and move through it without rupture.",
  56: "G. I route it through the parts of my life that can carry it without exposing the vulnerable parts.",
  57: "G. I redirect attention into the group dynamic so no single spotlight stays fixed on me.",
  58: "G. The quiet between connected things, where I can move without breaking the larger pattern.",
  59: "G. Connected but inaccessible -- present in the web, unreachable at the exposed point.",
  60: "G. Having enough connected paths that no one pressure can ever fully trap me.",
  61: "G. By finding the central relationship between all the parts and adjusting that first.",
  62: "G. The integrator -- I make separate strengths work as one system.",
  63: "G. Changing how all the pieces relate so the situation begins solving itself.",
  64: "G. Connecting every moving part until the whole situation responds like one body.",
  65: "G. The coordination that lets everyone and everything act together instead of alone.",
  66: "G. A hidden network where every separate piece carries the same intent.",
  67: "G. I notice the relationship map forming and adjust myself to strengthen the whole room.",
  68: "G. By helping people find the point where their needs, moods, and roles connect.",
  69: "G. Becoming the bridge between what exists now and what all the separate pieces could become.",
  70: "G. My ability to connect with the living pattern around me until it shows me how to survive.",
  71: "G. Drawing separate instincts, moods, and people into one shared direction.",
  72: "G. Change is convergence -- separate forces meeting until something new has to emerge."
}

missing = []
for q in data["questions"]:
    qid = q["id"]
    if qid not in g_map:
        missing.append(qid)
        continue

    opts = [str(o) for o in q.get("options", []) if not str(o).startswith("G.")]
    opts.append(g_map[qid])
    q["options"] = opts

data["scoring"]["answer_choices"] = ["A", "B", "C", "D", "E", "F", "G"]
data["scoring"]["g_domain"] = "omni"
data["scoring"]["g_content_policy"] = "question_specific"

quiz_path.write_text(json.dumps(data, indent=2, ensure_ascii=False) + "\n")

report = {
    "status": "PASS" if not missing else "BLOCKED",
    "patched_count": 72 - len(missing),
    "missing": missing
}

Path("data/reports/quiz").mkdir(parents=True, exist_ok=True)
Path("data/reports/quiz/question_specific_g_options_001.json").write_text(json.dumps(report, indent=2) + "\n")

print("QUESTION_SPECIFIC_G_PATCH=" + report["status"])
print("PATCHED_COUNT=" + str(report["patched_count"]))
PY

python - << 'PY'
import json
from pathlib import Path

data = json.loads(Path("data/quiz/questions.json").read_text())

generic_markers = [
    "coordinate the whole situation from above",
    "central force everything else has to organize around",
    "coordinating routes, timing, and openings at once",
    "controlled field that changes how everyone else can move",
    "disappear into the structure of the moment",
    "become the hub, linking separate pieces into one working system",
    "shift the entire emotional climate so people naturally move with me"
]

flagged = []

for q in data["questions"]:
    g_opts = [str(o) for o in q.get("options", []) if str(o).startswith("G.")]
    if len(g_opts) != 1:
        flagged.append({"id": q["id"], "reason": "bad_g_count", "count": len(g_opts)})
        continue

    if any(marker in g_opts[0] for marker in generic_markers):
        flagged.append({"id": q["id"], "reason": "generic_g", "g": g_opts[0]})

report = {
    "status": "PASS" if not flagged else "BLOCKED",
    "blocked_count": len(flagged),
    "blocked": flagged
}

Path("data/reports/quiz/g_option_content_audit.json").write_text(json.dumps(report, indent=2, ensure_ascii=False) + "\n")

print("G_OPTION_CONTENT_AUDIT=" + report["status"])
print("BLOCKED_COUNT=" + str(len(flagged)))
PY

npm run test:contract
npm run test:answers
npm run test:scoring

echo "WRITE_QUESTION_SPECIFIC_G_OPTIONS=PASS"
