# Voice Pattern Workflow - How It Works

## Overview

The voice pattern system has **two modes**:

1. **Content Generation** - Create new blog posts from scratch with your voice
2. **Content Transformation** - Apply your voice to existing content

## Current Implementation

### Mode 1: Content Transformation (Currently Active)

**What it does:**
- Takes existing blog post content (polished, formal, etc.)
- Applies Victor's authentic voice patterns
- Transforms it to match your writing style

**Example:**

**Input (polished):**
> "I have been asking myself a question that feels simple, but hits deep: Where are the people who move like me? Not better people. Not perfect people. Just people with that same frequency."

**Output (Victor's voice):**
> "lately ive been asking myself a question that feels simple, but hits deep: where are the people who move like me? not 'better' people. not 'perfect' people. js people with that same frequency."

**Workflow:**
```
You write draft → Voice processor applies patterns → WordPress template formats it → Published
```

### Mode 2: Content Generation (Available via Autoblogger)

**What it does:**
- Generates new blog posts from scratch
- Uses your voice patterns from the start
- Creates content that matches your style

**Workflow:**
```
Topic/idea → Autoblogger generates → Already in your voice → WordPress template → Published
```

## Blog Template Integration

### WordPress Theme Templates

The blog templates (`single.php`, `archive.php`, etc.) handle:
- ✅ **Layout** - How the post looks
- ✅ **Styling** - CSS and design
- ✅ **Structure** - Header, content, footer, sidebar
- ✅ **Navigation** - Previous/next posts
- ✅ **Comments** - Comment section
- ✅ **Metadata** - Author, date, categories, tags

### Voice Pattern Processor

The voice processor handles:
- ✅ **Content style** - Your writing voice
- ✅ **Tone** - Casual, direct, authentic
- ✅ **Language** - Shorthand, contractions, phrasing
- ✅ **Structure** - Paragraph flow, sentence style

**They work together:**
```
Voice Processor → Content in your voice
     ↓
WordPress Theme → Formats it beautifully
     ↓
Published Post → Your voice + Beautiful design
```

## Full Workflow Examples

### Example 1: Transform Existing Content

**Step 1:** You write a polished draft
```markdown
# Where Are My People?

I have been asking myself a question that feels simple, but hits deep...
```

**Step 2:** Voice processor applies patterns
```bash
python ops/deployment/publish_with_autoblogger.py \
  --site digitaldreamscape.site \
  --title "Where Are My People?" \
  --file draft.md
```

**Step 3:** WordPress theme formats it
- Uses `single.php` template
- Applies CSS styling
- Adds header, footer, navigation
- Displays in your theme design

**Result:** Blog post in your voice, beautifully formatted

### Example 2: Generate New Content

**Step 1:** Give autoblogger a topic
```bash
python -m autoblogger.cli.generate_blog \
  --topic "building in public struggles" \
  --style "victor_authentic"
```

**Step 2:** Autoblogger generates content
- Uses voice template from start
- Creates content in your voice
- Already matches your style

**Step 3:** Publish to WordPress
- Content already in your voice
- Theme formats it
- Published

## What Gets Applied

### Voice Patterns (From YAML Template)

**Tone:**
- Casual, direct, stream-of-consciousness
- Like DM'ing a close friend

**Mechanics:**
- Lower-case: "i", "im", "dont", "cant"
- Shorthand: "js", "cs", "idk", "tbh", "rn"
- Punctuation: "..." for pacing, loose commas

**Phrasing:**
- "ok so", "lowkey feel like", "so hear me out"
- "the question becomes...", "is this noise or signal"
- "ngl that kinda fire", "ok that makes sense"

**Structure:**
- Long, messy paragraphs okay
- Line breaks when switching topics
- Contradictions can live together

### Blog Template (WordPress Theme)

**Layout:**
- Header with navigation
- Main content area
- Sidebar (if enabled)
- Footer with links

**Styling:**
- Typography (fonts, sizes)
- Colors and spacing
- Responsive design
- Animations and effects

**Features:**
- Author bio box
- Social share buttons
- Related posts
- Comments section
- Reading time
- Category tags

## Current Capabilities

### ✅ What Works Now

1. **Transform existing content** - Apply voice to polished drafts
2. **Use local LLM** - No API costs, fully private
3. **WordPress integration** - Publishes directly
4. **Template formatting** - Theme handles layout

### 🔄 What Can Be Enhanced

1. **Generate from scratch** - Full autoblogger integration
2. **Template customization** - More voice-specific styling
3. **Batch processing** - Multiple posts at once
4. **Preview mode** - See before publishing

## Usage Scenarios

### Scenario 1: You Write, We Transform

**You:** Write polished draft in any style
**System:** Applies your voice patterns
**Result:** Content in your authentic voice

### Scenario 2: Topic → Full Post

**You:** Give topic/idea
**System:** Generates full post in your voice
**Result:** Complete blog post ready to publish

### Scenario 3: Quick Thoughts → Blog Post

**You:** Stream of consciousness notes
**System:** Transforms to blog post format with your voice
**Result:** Polished post maintaining your authentic style

## Summary

**Voice Pattern Processor:**
- ✅ Mimics your writing style
- ✅ Applies to existing content
- ✅ Can generate new content (via autoblogger)

**Blog Template:**
- ✅ Formats content beautifully
- ✅ Handles layout and design
- ✅ Adds WordPress features

**Together:**
- ✅ Your voice + Beautiful design = Published blog post

The system ensures your authentic voice is preserved while the WordPress theme makes it look professional and polished.


