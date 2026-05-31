# Emergence Portrait Prompt Preview Polish 109

## Task
Make premium prompt preview easier for players to read and copy.

## Actions
- Added polished prompt preview card.
- Added copy prompt button.
- Added sections for costume, personality, powers, ability showcase, and full-body standard.
- Verified prompt fixture smoke still passes.
- Verified no visible raw score leaks.

## Verification
```text
INPUTS=PASS
PORTRAIT_PROMPT_PREVIEW_POLISH_PATCH=PASS
STATIC_PROMPT_PREVIEW_SAFE=PASS
STATIC_PROMPT_COPY_UI=PASS
STATIC_PROMPT_SECTIONS=PASS
PLUGIN_TARBALL=PASS /data/data/com.termux/files/home/projects/websites/_reports/emergence-character-generator_109.tar.gz
SCP_UPLOAD=PASS
EXISTING_PLUGIN_BACKUP=PASS
No syntax errors detected in wp-content/plugins/emergence-character-generator/emergence-character-generator.php
REMOTE_PHP_LINT=PASS
    <script id="dreamos-portrait-prompt-preview-polish-inline">
          '<section class="ecg-prompt-preview-card" data-prompt-preview="polished">',
      .ecg-prompt-preview-card {
            setTimeout(function () { button.textContent = button.getAttribute('data-label') || 'Copy Prompt'; }, 1400);
        setTimeout(function () { button.textContent = button.getAttribute('data-label') || 'Copy Prompt'; }, 1400);
          '<button type="button" class="ecg-copy-prompt-button" data-label="Copy Prompt">Copy Prompt</button>',
REMOTE_PROMPT_PREVIEW_SOURCE=PASS
Success: Plugin already activated.
PLUGIN_ACTIVE=PASS
Success: The cache was flushed.
Success: Purged All!
LITESPEED_PURGE=PASS
REMOTE_DEPLOY=PASS
HTTP_CHARACTER=200
PUBLIC_PROMPT_PREVIEW_INLINE=PASS
PUBLIC_PROMPT_PREVIEW_SECTIONS=PASS
PUBLIC_PROMPT_COPY_BUTTON=PASS
PUBLIC_PROMPT_NO_RAW_SCORE_LEAK=PASS
EMERGENCE_PORTRAIT_PROMPT_PREVIEW_POLISH=PASS
== BUILD FIXTURE PROMPTS ==
FIXTURE_lean_armored_haunted_active=PASS
FIXTURE_powerful_cosmic_noble_dramatic=PASS
FIXTURE_compact_tactical_cocky_subtle=PASS
FIXTURE_tall_elegant_stoic_restrained=PASS
PROMPT_FIXTURE_MATRIX_WRITTEN=PASS
PROMPT_FIXTURE_MATRIX=/data/data/com.termux/files/home/projects/websites/_reports/emergence_portrait_prompt_quality_fixtures_108.json
PROMPT_FIXTURE_DISTINCTNESS=PASS
PROMPT_FIXTURE_FULL_BODY_STANDARD=PASS
PROMPT_FIXTURE_CUSTOM_COSTUME=PASS
PROMPT_FIXTURE_CUSTOM_PERSONALITY=PASS
PROMPT_FIXTURE_ABILITY_SHOWCASE=PASS
PROMPT_FIXTURE_NO_RAW_SCORE_LEAK=PASS
EMERGENCE_PORTRAIT_PROMPT_QUALITY_FIXTURES=PASS
PROMPT_FIXTURE_SMOKE=PASS
```

## Commit
Polish Emergence portrait prompt preview

## Status
PASS
