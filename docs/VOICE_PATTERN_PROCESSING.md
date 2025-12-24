# Voice Pattern Processing for Blog Posts

## Overview

All blog posts for Digital Dreamscape must be processed through the voice pattern processor to ensure they match Victor's authentic voice patterns before publishing.

## How It Works

### 1. Voice Template System

Voice patterns are defined in YAML templates:
- **Primary**: `D:/Agent_Cellphone_V2_Repository/config/writing_style_template.yaml`
- **Digital Dreamscape**: `D:/Agent_Cellphone_V2_Repository/temp_repos/Auto_Blogger/autoblogger/resources/voice_templates/digitaldreamscape_voice_template.yaml`

### 2. Voice Pattern Processor

The `voice_pattern_processor.py` module:
- Loads voice templates from YAML
- Builds voice instruction prompts
- Processes content through Mistral AI to apply patterns
- Returns content with Victor's authentic voice applied

### 3. Publishing Workflow

**Standard Publishing (with voice processing):**
```bash
python ops/deployment/publish_with_autoblogger.py \
  --site digitaldreamscape.site \
  --title "Your Post Title" \
  --file path/to/content.md \
  --status publish
```

**Skip voice processing (not recommended):**
```bash
python ops/deployment/publish_with_autoblogger.py \
  --site digitaldreamscape.site \
  --title "Your Post Title" \
  --file path/to/content.md \
  --skip-autoblogger
```

## Voice Patterns Applied

### Tone & Style
- Casual, direct, stream-of-consciousness
- Like DM'ing a close friend
- Honest, slightly self-deprecating, but confident

### Mechanics
- Lower-case: "i", "im", "id", "dont", "cant"
- Frequent "..." for pacing
- Loose punctuation, run-on sentences okay

### Shorthand
- "js" (just)
- "cs" (cause)
- "idk" (I don't know)
- "tbh" (to be honest)
- "rn" (right now)
- "tryna" (trying to)
- "kinda", "lowkey", "ngl"

### Phrasing Patterns
- Intros: "ok so", "so hear me out", "lowkey feel like"
- Thinking: "the question becomes...", "is this noise or signal"
- Meta: "ngl that kinda fire", "ok that makes sense"

### Structure
- Long, messy paragraphs are okay
- Line breaks when switching topics
- Contradictions can live in same block

## Requirements

### Environment Variables
- `MISTRAL_API_KEY` - Required for voice pattern processing

### Dependencies
- `mistralai` - Mistral AI client
- `pyyaml` - YAML parsing
- `python-dotenv` - Environment variable loading

## Troubleshooting

### Voice Processing Not Working

1. **Check Mistral API Key:**
   ```bash
   # Should be in D:/Agent_Cellphone_V2_Repository/.env
   MISTRAL_API_KEY=your_key_here
   ```

2. **Check Voice Template:**
   ```bash
   # Verify template exists
   ls D:/Agent_Cellphone_V2_Repository/config/writing_style_template.yaml
   ```

3. **Test Voice Processor:**
   ```bash
   python ops/deployment/voice_pattern_processor.py \
     --file test_content.md \
     --title "Test Post" \
     --output processed.md
   ```

### Fallback Behavior

If voice processing fails:
- Original content is returned (not processed)
- Warning message is displayed
- Post still publishes (but without voice patterns)

## Integration with Autoblogger

The voice pattern processor is designed to work alongside the autoblogger system:
- **Voice Processor**: Applies patterns to existing content
- **Autoblogger**: Generates new content from scratch with voice patterns

Both systems use the same voice template YAML files for consistency.

## Best Practices

1. **Always use voice processing** - Don't skip unless absolutely necessary
2. **Review processed content** - Check that voice patterns are applied correctly
3. **Update templates** - Keep voice templates current with Victor's evolving style
4. **Test before publishing** - Use `--status draft` first to review

## Example

**Before (polished):**
> "I have been asking myself a question that feels simple, but hits deep: Where are the people who move like me?"

**After (Victor's voice):**
> "lately ive been asking myself a question that feels simple, but hits deep: where are the people who move like me?"

The voice processor maintains meaning while applying authentic voice patterns.


