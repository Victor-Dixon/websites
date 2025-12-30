## Product Requirements Document (PRD): Flowr Timer

### Overview
Flowr is a lightweight timer that answers:

- **What time did we start Flowr?**
- **What time did we end Flowr?**
- **How long did Flowr last?**

Flowr also supports an optional behavior: **auto-stop after the user says a word** (voice-triggered stop).

### Problem statement
People want a fast, low-friction way to time a short “Flowr” session without doing manual math or remembering timestamps. For certain use cases, Flowr should stop automatically once the user speaks (or speaks a specific keyword), so the interaction is hands-free.

### Goals
- **Accurate timestamps**: capture start and end clock times reliably.
- **Accurate duration**: compute and display elapsed time clearly.
- **Fast interaction**: start/stop in one tap each; minimal UI.
- **Optional hands-free stop**: stop automatically after a recognized word/keyword.
- **Clear session record**: show the most recent session details; optionally show a small history list.

### Non-goals (for MVP)
- Background timing across device reboots / OS-level background execution guarantees.
- Multi-user accounts, syncing across devices, or cloud storage.
- Advanced audio processing (VAD, diarization, wake word models) beyond built-in browser speech recognition.
- Editing historical sessions (MVP: view-only).

### Target users
- **Primary**: a single user timing short “Flowr” sessions on a phone or laptop.
- **Secondary**: creators/operators who need a “speak once to stop” timer for quick capture workflows.

### Core user stories
- As a user, I can tap **Start Flowr** and immediately see the timer running.
- As a user, I can tap **Stop** and see end time + total duration.
- As a user, I can **Reset** to clear the current session display.
- As a user, I can enable **voice auto-stop** so the timer stops when I speak.
- As a user, I can set a **keyword** so the timer stops only when that word/phrase is heard.
- As a user, I can see a small **session history** (recent sessions) to confirm what happened.

### UX / UI requirements
- **Primary screen (single screen MVP)**:
  - Large **elapsed timer** display.
  - **Start time** (HH:MM:SS).
  - **End time** (HH:MM:SS).
  - **Duration** (MM:SS.mmm or MM:SS).
  - Controls: **Start**, **Stop**, **Reset**.
  - Status indicator: **Idle / Running / Stopped**.
  - Optional block: **Auto-stop (voice)** settings.
- **State behavior**:
  - Idle:
    - Start enabled; Stop disabled.
    - Start/end/duration shown as placeholder (e.g., “—”).
  - Running:
    - Start disabled; Stop enabled.
    - Elapsed updates smoothly.
  - Stopped:
    - Start enabled; Stop disabled.
    - End time and duration fixed.

### Functional requirements (MVP)
- **Timer**
  - Start records `startedAt` (local device time) and begins elapsed updates.
  - Stop records `endedAt` and freezes elapsed.
  - Duration computed as `endedAt - startedAt`.
  - Reset clears the displayed session (does not need to delete history unless explicitly chosen).
- **Voice auto-stop (optional)**
  - Toggle to enable voice auto-stop.
  - Mode:
    - **Any word**: stop on first recognized non-empty transcript.
    - **Keyword**: stop only when transcript matches keyword (token or substring match).
  - If speech recognition is unsupported/unavailable, UI should:
    - Disable the toggle (or show it disabled).
    - Provide a **fallback** action for demo/testing (e.g., “Simulate word heard”).
- **Session history (optional but recommended for MVP)**
  - Store and display the most recent N sessions (recommend N=5–10).
  - Each row shows: start time, end time, duration, stop reason.

### Stop reasons (enumeration)
- `manual`: user pressed Stop.
- `voice_any`: stopped after any recognized word.
- `voice_keyword`: stopped after matching keyword.
- `error`: stopped due to a fatal error (if applicable).

### Data model (suggested)
- `FlowrSession`
  - `id` (string/uuid)
  - `startedAt` (timestamp)
  - `endedAt` (timestamp | null while running)
  - `durationMs` (number | null while running)
  - `stopReason` (enum)
  - `voiceEnabled` (boolean)
  - `voiceMode` (`any` | `keyword` | null)
  - `keyword` (string | null)
  - `transcriptSnippet` (string | null) — store only if explicitly desired; see Privacy.

### Edge cases & error handling
- **Double start**: ignore or prevent; UI should not create multiple intervals.
- **Stop without start**: ignore; keep UI stable.
- **Speech permission denied**:
  - Timer still functions manually.
  - Show a small non-blocking message: “Mic permission denied; voice auto-stop off.”
- **Speech recognition ends unexpectedly** while running:
  - Attempt to restart recognition if voice auto-stop is enabled.
  - Do not stop the timer unless a stop condition occurs.
- **Device time changes mid-session**:
  - Acceptable for MVP (duration may be affected). Later: monotonic timing source if platform supports.

### Accessibility requirements
- Buttons must be reachable and readable at mobile sizes.
- Minimum contrast for text over background.
- Status messaging uses `aria-live` to announce changes like “Running” / “Stopped”.
- Keyboard operability (Start/Stop/Reset) for web.

### Privacy & security
- Voice mode should be **opt-in**.
- MVP default: **do not store raw audio**.
- Transcript storage:
  - Recommended MVP behavior: show the last transcript in UI **without persisting it**.
  - If persisted, store only a short snippet and document it clearly.

### Analytics (MVP)
Track simple events to validate usage:
- `flowr_start` (properties: voiceEnabled, voiceMode)
- `flowr_stop` (properties: stopReason, durationMs)
- `flowr_reset`
- `voice_permission_denied`
- `voice_unsupported`

### Success metrics
- **Activation**: % of users who successfully complete 1 session (start+stop).
- **Voice adoption**: % of sessions with voice auto-stop enabled.
- **Completion**: % of running sessions that end (manual or voice) within a reasonable time window.

### MVP acceptance criteria
- Starting creates a session with visible start time and running elapsed time.
- Stopping freezes the timer and shows end time + computed duration.
- Reset clears the current session display and returns UI to Idle.
- When voice auto-stop is enabled and speech recognition is supported:
  - In “Any word” mode, the first recognized word stops the timer.
  - In “Keyword” mode, speaking the keyword stops the timer.
- When speech recognition is not supported:
  - UI communicates it clearly and still allows manual timer use.

### Future scope (post-MVP)
- Persist sessions to local storage and/or backend.
- Session notes/tags.
- Export history (CSV/JSON).
- More robust “word detection” via VAD or keyword spotter.
- Mobile-native implementation (iOS/Android) with background support.

### Implementation reference
- UI mock prototype: `side-projects/web/flowr-timer/index.html`

### Open questions
- What does “stop after saying a word” mean exactly?
  - First detected speech at all (any sound), first recognized transcript token, or a specific keyword?
- Should we store transcripts at all, and if yes, for how long?
- Should duration display be `MM:SS.mmm` or `MM:SS` for MVP?

