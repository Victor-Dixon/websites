# Digital Dreamscape World-Building Protocol

**Version**: 1.0  
**Date**: 2025-12-21  
**Status**: Active Protocol

---

## Purpose

This protocol defines the systematic process for adding new domains to the Digital Dreamscape civilization. It ensures consistency, narrative coherence, and the closing of the **Life → System → Story → Better Life** loop.

---

## Foundation: The Three Authorities

Before adding domains, understand the foundation:

### The Three Authorities

Digital Dreamscape operates with **three distinct authorities** that do not overlap:

1. **Victor** = Decision Authority (vision holder, questline setter, final calls)
2. **The Swarm** = Execution Authority (8-personality being that executes, integrates, implements)
3. **Thea** = Narrative + Coherence Authority (canon state, narrative framing, continuity enforcement)

### The One-Line Rule

> **If Victor hasn't chosen it, it's not a command.  
> If the Swarm hasn't built it, it's not canon.  
> If Thea hasn't named it, it's not integrated.**

### Authority Separation

* **Victor** sets vision, priorities, final calls — can override anything, anytime
* **The Swarm** executes missions assigned by Victor — reports outcomes
* **Thea** synthesizes outcomes, declares canon, maintains continuity — never issues tasks

**No overlap. No conflict.**

See:
* **[VICTOR_AND_THE_SWARM.md](./VICTOR_AND_THE_SWARM.md)** - The full foundation narrative
* **[CANON_ROLE_CONTRACT.md](./CANON_ROLE_CONTRACT.md)** - The complete role contract

This authority structure is **canon law** and is the foundation upon which all domains are built.

---

## The Protocol

### Step 1: Define the Domain

**What**: Identify what aspect of life/work this domain represents.

**Questions to Answer**:
* What is this domain's core purpose?
* What are its fundamental rules?
* Who enters this domain? (player archetypes)
* What doesn't belong here? (domain boundaries)

**Output**: Clear understanding of the domain's identity and rules.

**Example**: FreeRideInvestor = Trading Domain with risk-first rules, discipline-based progression, freedom-focused players.

---

### Step 2: Write It Down (Make It Canon)

**What**: Create a `WHAT_IS_[DOMAIN].md` document in the domain's directory.

**Structure**:
* Core Purpose
* What It Actually Is (4-5 pillars)
* Who It's For
* What It's Not
* Digital Dreamscape Connection (how it fits as a questline)
* Current Status
* One-Line Definition

**Output**: `websites/[domain]/WHAT_IS_[DOMAIN].md`

**Why**: Makes it canon. The document itself becomes part of the world-state.

---

### Step 3: Update Digital Dreamscape Landscape

**What**: Add the domain to `WHAT_IS_DIGITAL_DREAMSCAPE.md` under "The Digital Dreamscape Landscape" section.

**Structure**:
* Domain name and description
* What it represents in the world
* As a Questline (mechanics mapping)
* The Philosophy (domain rules)
* Who enters / What it's not

**Output**: Updated `digitaldreamscape.site/WHAT_IS_DIGITAL_DREAMSCAPE.md`

**Why**: Integrates the domain into the world narrative.

---

### Step 4: Tell the Story (Blog Post)

**What**: Create a blog post that narratively adds the domain to the world.

**Location**: `digitaldreamscape.site/blog/[NNN]-[domain-name]-[descriptor].md`

**Structure**:
* **Title**: Descriptive, narrative-focused
* **Date**: When it was added
* **Category**: World-Building
* **Tags**: domain-creation, [domain-name], questline-design
* **Content**:
  - The question that led to it
  - The answer/realization
  - How it fits into the world
  - The mechanics (questline mapping)
  - Why it matters
  - What's next

**Output**: Blog post in `digitaldreamscape.site/blog/`

**Why**: Makes the addition part of the narrative. The act of adding it becomes canon.

---

### Step 5: Update SITE_INFO.md

**What**: Update the domain's `SITE_INFO.md` to reference the new WHAT_IS document.

**Add**:
```markdown
### What is [Domain Name]?

See **[WHAT_IS_[DOMAIN].md](./WHAT_IS_[DOMAIN].md)** for the full description.

**TL;DR**: [One-line definition]
```

**Output**: Updated `websites/[domain]/SITE_INFO.md`

**Why**: Creates discoverability and connection.

---

### Step 6: Close the Loop

**What**: Ensure the domain feeds back into execution.

**Check**:
* Does the domain have clear progression mechanics?
* Are actions tracked and become canon?
* Does the narrative improve execution?
* Does execution improve the narrative?

**Output**: Verified narrative ↔ execution loop

**Why**: This is the core of Digital Dreamscape. Without the loop, it's just documentation.

---

## Example: FreeRideInvestor Domain

### Step 1: Defined
- Trading Domain
- Risk-first rules
- Discipline-based progression
- Freedom-focused players

### Step 2: Written
- Created `WHAT_IS_FREERIDEINVESTOR.md`
- Defined 4 pillars + technical platform
- Established philosophy and boundaries

### Step 3: Integrated
- Added to Digital Dreamscape landscape
- Mapped as Trading Questline
- Defined mechanics (trades = missions, etc.)

### Step 4: Narrated
- Blog post: "002-the-trading-domain-emerges.md"
- Told the story of adding it
- Made it part of the canon

### Step 5: Referenced
- Updated `SITE_INFO.md` with link and TL;DR

### Step 6: Loop Closed
- Trades become missions (input stream)
- Journal entries become canon (world-state)
- Lessons learned improve execution (story → better life)

### Step 7: Canon Extraction
- Automated extraction finds domain creation
- Generates canon candidate
- Structures for Thea processing

### Step 8: Victor Acknowledgment
- Victor reviews candidate
- Acknowledges as canon-worthy
- Reframes narrative if needed

### Step 9: Thea Declaration
- Thea declares canon
- Updates CANON_EVENTS.md
- Maintains continuity

---

## Protocol Checklist

When adding a new domain:

- [ ] Step 1: Domain defined (purpose, rules, players, boundaries)
- [ ] Step 2: WHAT_IS document created
- [ ] Step 3: Added to Digital Dreamscape landscape
- [ ] Step 4: Blog post written (narrative addition)
- [ ] Step 5: SITE_INFO.md updated
- [ ] Step 6: Narrative ↔ execution loop verified
- [ ] Step 7: Canon extraction run (automated or manual)
- [ ] Step 8: Victor acknowledges canon-worthy events
- [ ] Step 9: Thea declares canon and updates timeline

---

## Next Domains to Add

Based on existing websites:

1. **The Consulting Domain** (DaDudeKC)
2. **The Event Services Domain** (Crosby Ultimate Events)
3. **The Mobile Bartending Domain** (Houston Sip Queen)
4. **The Automation Tools Domain** (Trading Robot Plug)
5. **The Music & Entertainment Domain** (Southwest Secret)
6. **The Creative Domain** (AriaJet)
7. **The Documentation Domain** (We Are Swarm Online)
8. **The Demo Domain** (We Are Swarm Site)
9. **The Personal Domain** (Prism Blossom)

Each follows the same protocol.

---

## Principles

1. **Everything is Canon**: The act of creating documentation is itself part of the story
2. **Narrative First**: Story drives structure, not the other way around
3. **Close the Loop**: Always ensure story feeds back into execution
4. **Systematic**: Follow the protocol for consistency
5. **Living World**: The world evolves with each addition

---

## Version History

- **v1.0** (2025-12-21): Initial protocol established after adding Trading Domain

---

*Part of the Digital Dreamscape world-building system*

