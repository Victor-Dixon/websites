# Recover versioned Spark route patch without perl

Generated: 2026-06-05T02:37:50-05:00

## Remote patch

```text
WP_ROOT=/home/u996867598/domains/maskzero.site/public_html
== PYTHON FILE PATCH ==
PATCH_FILE=wp-content/themes/dreamos-emergence/parts/header.html
PATCH_FILE=wp-content/plugins/emergence-character-generator/assets/emergence-cg.js
PATCH_FILE=wp-content/plugins/emergence-character-generator/assets/emergence-character-generator.js
PATCH_COUNT=3
== HTACCESS UPDATE WITH PYTHON ==
HTACCESS_PATCHED=YES
== FLUSH CACHE ==
Success: The cache was flushed.
Success: Rewrite rules flushed.
REMOTE_NO_PERL_ROUTE_PATCH=PASS
```

## Homepage route verification

```text
86:      <a href="/spark-generator/?spark=v075">Generate</a>
696:              <a class="btn" href="/spark-generator/?spark=v075">Generate Your Spark →</a><br />
781:              <a class="btn" href="/spark-generator/?spark=v075">Create a Spark First →</a>
```

## Versioned route verification

```text
0.7.5-visible-dossier-state-001
0.7.5-visible-dossier-state-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js
0.7.5-visible-dossier-state-001
emergence-character-generator.js
0.7.5-visible-dossier-state-001
```

## Result

Recovered the previous failed lane. Generate CTAs now point to `/spark-generator/?spark=v075` without relying on remote `perl`.
