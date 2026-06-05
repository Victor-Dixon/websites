# Patch Spark generator fail-open guard

Generated: 2026-06-05T01:26:27-05:00

## Diagnosis basis

Backend generation works. POST to `/wp-json/emergence/v1/generate` returned HTTP 200 with a valid Spark Protocol v8.5 result. The blank page is frontend render failure.

## Target

- JS: `./data/reports/websites/emergence/tmp/diagnose_spark_generator_blank_001/assets/emergence-cg.js`
- CSS: `./data/reports/websites/emergence/tmp/diagnose_spark_generator_blank_001/assets/emergence-cg.css`

## Verification

```text
133:/* DreamOS Spark Generator Fail-Open Guard
173:    return "/wp-json/emergence/v1/generate";
216:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',
133:/* DreamOS Spark Generator Fail-Open Visibility Guard */
```

## Result

Added a fail-open frontend guard. If the primary generator UI is hidden, empty, or crashes, the page renders a recovery panel with a direct diagnostic Generate Spark button using the healthy REST endpoint.
