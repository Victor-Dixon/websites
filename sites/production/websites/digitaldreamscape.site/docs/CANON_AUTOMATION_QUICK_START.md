# Canon Automation: Quick Start

**Version**: 1.0  
**Date**: 2025-12-22

---

## What It Does

Automatically extracts canon-worthy events from agent work cycles and structures them for Thea's narrative processing.

---

## How to Use

### Run Extraction

```bash
cd D:\Agent_Cellphone_V2_Repository
python tools/canon_automation.py
```

Or use the wrapper:

```bash
python scripts/run_canon_extraction.py
```

### Review Results

Results are saved to: `reports/canon_extraction.json`

The file contains:
* **structured_events** - All events extracted from agent work
* **canon_candidates** - Events suggested for canon (require Victor acknowledgment)

### Process Candidates

1. **Victor reviews** `canon_candidates` in the JSON
2. **Acknowledges** canon-worthy events
3. **Thea processes** acknowledged events
4. **Canon declared** and added to timeline

---

## What Gets Extracted

* **Contract Completions** - When agents complete contracts
* **Achievements** - Significant milestones
* **Major Task Completions** - Important work done
* **Coordination Activities** - Agent collaboration

---

## Authority Flow

```
Agent Work → Extraction → Candidates → Victor → Thea → Canon
```

**Automation extracts. Victor acknowledges. Thea declares.**

---

## First Run Results

**Initial extraction (2025-12-22):**
* 32 events found
* 19 candidates generated
* 7 agents scanned

**Ready for Victor's review.**

---

*Part of the Digital Dreamscape canon automation system*

