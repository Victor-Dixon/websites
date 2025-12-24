# Voice Profile System

Unified voice profiles for team members to ensure content doesn't look AI-generated.

## Profiles Available

- **victor_v1** - Victor (Builder & Systems Thinker)
  - Chat: Casual, loose grammar, shorthand (js, cs, idk)
  - Blog: Direct, confident, builder energy, Problem→Fix→Steps→CTA

- **kiki_v1** - Kiki (Clear, Warm, Decisive)
  - Chat: Clear, warm, decisive, thoughtful
  - Blog: Clear structure with warm tone

- **corey_v1** - Corey (Direct, Specific, No Fluff)
  - Chat: Direct, efficient, gets to the point
  - Blog: Short lines, concrete examples, clear CTA

- **carmyn_v1** - Carmyn (Authentic Voice)
  - Chat: Authentic, natural communication
  - Blog: Genuine expression with clear structure

- **aria_v1** - Aria (Authentic Voice)
  - Chat: Authentic, natural communication
  - Blog: Genuine expression with clear structure

## Usage

### Voice Detection

```python
from config.voice_profiles.voice_detector import VoiceDetector

detector = VoiceDetector()
profile_id, confidence, analysis = detector.detect_voice(content)
print(f"Detected: {profile_id} ({confidence:.2%} confidence)")
```

### Content Authenticity Analysis

```python
result = detector.analyze_content_authenticity(content)
print(f"Authenticity Score: {result['authenticity_score']:.2%}")
print(f"AI Indicators: {result['ai_indicators']}")
```

### Suggest Voice Profile

```python
suggested = detector.suggest_voice(content, context="blog")
print(f"Suggested profile: {suggested}")
```

## CLI Usage

```bash
# Detect voice from content file
python config/voice_profiles/voice_detector.py content.txt
```

## Profile Structure

Each profile includes:
- **chat_style**: How the person types in chat/conversation
- **blog_style**: How the person writes for published content
- **unique_voice_markers**: Signature phrases and patterns
- **opening_styles**: How they typically start content
- **closing_styles**: How they typically end content
- **do/dont**: Guidelines for matching their voice

## Integration

The VoicePatternProcessor can use these profiles:

```python
from ops.deployment.voice_pattern_processor import VoicePatternProcessor

# Use specific profile
processor = VoicePatternProcessor(
    voice_template_path=Path("config/voice_profiles/victor_voice_profile.yaml")
)
rewritten = processor.apply_voice_patterns(content)
```

## Goals

1. **Prevent AI-generated appearance**: Match content to authentic human voices
2. **Consistency**: Maintain consistent voice across all content
3. **Detection**: Automatically identify which voice profile matches content
4. **Improvement**: Get recommendations to make content more authentic


