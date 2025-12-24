# Canon Automation Protocol

**Version**: 1.0  
**Date**: 2025-12-21  
**Status**: Active Protocol

---

## Purpose

This protocol defines how canon events are automatically extracted from agent work cycles and structured for Thea's narrative processing, while maintaining authority separation.

---

## The Authority Flow

```
Agent Work (Execution)
  ↓
Canon Extraction (Automated)
  ↓
Canon Candidates (Structured)
  ↓
Victor Acknowledgment (Decision)
  ↓
Thea Declaration (Narrative)
  ↓
Canon Event (Official)
  ↓
Feeds back into system
```

**No authority bleeds between layers.**

---

## How It Works

### Step 1: Agent Work Cycle

Agents work and update their `status.json` files with:
* `completed_tasks` - Tasks that are done
* `achievements` - Significant milestones
* `contract_status` - Contract completions with deliverables
* `current_tasks` - Current work (marked complete when done)

**This is execution. This is the Swarm doing their work.**

---

### Step 2: Canon Extraction (Automated)

The `canon_automation.py` tool:

1. **Scans all agent status.json files**
2. **Extracts canon-worthy events:**
   * Completed contracts (with deliverables and results)
   * Significant achievements
   * Major task completions
   * Coordination activities
3. **Structures events for Thea**
4. **Generates canon candidates**

**This is automated. This happens on a schedule or trigger.**

---

### Step 3: Canon Candidates (Structured)

Candidates are structured as:

```json
{
  "canon_candidate": true,
  "event": {
    "type": "contract_completion",
    "agent": "Agent-8",
    "contract": "Fix Consolidated Imports",
    "deliverables": [...],
    "results": {...}
  },
  "suggested_narrative": "...",
  "requires_victor_acknowledgment": true
}
```

**These are suggestions. They are NOT canon yet.**

---

### Step 4: Victor Acknowledgment (Decision)

Victor reviews candidates and:

* **Acknowledges** - "Yes, this is canon-worthy"
* **Reframes** - "This is canon, but the narrative should be..."
* **Defers** - "Not canon yet, wait for more context"
* **Rejects** - "Not canon-worthy"

**Victor has decision authority. Nothing becomes canon without Victor's acknowledgment.**

---

### Step 5: Thea Declaration (Narrative)

Once Victor acknowledges, Thea:

* **Declares it canon** - Makes it official
* **Frames the narrative** - What does this mean?
* **Identifies the arc** - What phase are we in?
* **Maintains continuity** - How does this fit the story?
* **Reflects** - Why did this matter? What changed?

**Thea has narrative authority. She declares canon and maintains continuity.**

---

### Step 6: Canon Event (Official)

Once Thea declares it:

* Added to `CANON_EVENTS.md` timeline
* Blog post created (if significant)
* Feeds back into system state
* Becomes part of the permanent story

**This is canon. This is irreversible in the narrative.**

---

## What Gets Extracted

### Contract Completions

When an agent completes a contract:
* Contract name
* Deliverables created
* Results/outcomes
* Timestamp

**Example**: Agent-8 completes "Fix Consolidated Imports" → Canon candidate

### Achievements

When an agent achieves a milestone:
* Achievement description
* Significance
* Timestamp

**Example**: Agent-8 "SSOT domain mapping complete - 32 domains defined" → Canon candidate

### Major Task Completions

When an agent completes significant tasks:
* Task name
* Details/results
* Timestamp

**Example**: Agent-8 "Fixed 12 import conflicts across 6 files" → Potential canon

### Coordination Activities

When agents coordinate:
* Coordination type
* Participants
* Outcome
* Timestamp

**Example**: Agent-8 "A2A coordination accepted" → Potential canon

---

## Automation Triggers

Canon extraction can be triggered by:

1. **Scheduled** - Daily/weekly extraction
2. **On-demand** - Manual trigger when needed
3. **Event-driven** - When contract status changes to "✅ COMPLETE"
4. **Integration** - Hooked into agent status update process

---

## The One-Line Rule (Still Applies)

> **If Victor hasn't chosen it, it's not a command.  
> If the Swarm hasn't built it, it's not canon.  
> If Thea hasn't named it, it's not integrated.**

**Automation extracts. Victor acknowledges. Thea declares.**

---

## Example Flow

1. **Agent-8 completes contract** → Updates status.json
2. **Canon extraction runs** → Finds completed contract
3. **Candidate generated** → "Agent-8 completed Fix Consolidated Imports"
4. **Victor reviews** → "Yes, this is canon-worthy. The narrative should be: 'The Import Domain was stabilized'"
5. **Thea declares** → "Canon Event: Import Domain Stabilization (2025-12-21)"
6. **Added to timeline** → Part of permanent story
7. **Feeds back** → Future import work references this canon

---

## Integration Points

### With Agent Status System

* Agents update status.json → Triggers extraction
* Extraction finds completions → Generates candidates
* Candidates reviewed → Victor acknowledges
* Thea declares → Canon event created

### With Thea System

* Thea receives structured events
* Thea processes narrative framing
* Thea maintains continuity
* Thea declares canon

### With Blog System

* Significant canon events → Blog posts
* Narrative integration → Story continues
* Public-facing → Build-in-public

---

## Benefits

**For Victor:**
* Don't have to manually track every completion
* System suggests what might be canon
* Still maintains decision authority
* Can reframe narrative as needed

**For The Swarm:**
* Their work is automatically considered for canon
* Don't have to manually report everything
* Work gets narrative weight automatically
* Understand why their work matters

**For Thea:**
* Receives structured events to process
* Can maintain continuity automatically
* Can identify arcs and patterns
* Can reflect on system health

**For Digital Dreamscape:**
* Canon events accumulate automatically
* Narrative builds from real work
* Story feeds back into execution
* Loop closes continuously

---

## Implementation

### The Tool

See `tools/canon_automation.py` for the extraction tool.

**Run with:**
```bash
cd D:\Agent_Cellphone_V2_Repository
python tools/canon_automation.py --workspaces agent_workspaces --output reports/canon_extraction.json
```

### First Extraction Results

**Initial run (2025-12-22):**
* **32 potential canon events** extracted
* **19 canon candidates** generated
* **7 agents** scanned
* **Event types**: Contract completions, achievements, task completions, coordination

### Automation Triggers

Canon extraction can be triggered by:

1. **Scheduled** - Daily/weekly extraction (cron job or task scheduler)
2. **On-demand** - Manual trigger when needed
3. **Event-driven** - When contract status changes to "✅ COMPLETE"
4. **Integration** - Hooked into agent status update process

### Integration Points

**With Agent Status System:**
* Agents update status.json → Triggers extraction (optional)
* Extraction finds completions → Generates candidates
* Candidates reviewed → Victor acknowledges
* Thea declares → Canon event created

**With Thea System:**
* Thea receives structured events from `canon_extraction.json`
* Thea processes narrative framing
* Thea maintains continuity
* Thea declares canon

**With Blog System:**
* Significant canon events → Blog posts
* Narrative integration → Story continues
* Public-facing → Build-in-public

---

*Part of the Digital Dreamscape world-building system*  
*Automates canon extraction while maintaining authority separation*

