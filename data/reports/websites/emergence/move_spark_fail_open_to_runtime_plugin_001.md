# Move Spark generator fail-open guard to runtime plugin assets

Generated: 2026-06-05T01:27:06-05:00

## Correction

Previous patch targeted diagnostic tmp assets. This lane moves the fail-open guard to the actual deployable plugin assets.

## Real targets

- `runtime/plugins/emergence-character-generator/assets/emergence-cg.js`
- `runtime/plugins/emergence-character-generator/assets/emergence-cg.css`

## Removed from tracking

- `data/reports/websites/emergence/tmp/diagnose_spark_generator_blank_001/assets/emergence-cg.js`
- `data/reports/websites/emergence/tmp/diagnose_spark_generator_blank_001/assets/emergence-cg.css`

## Verification

```text
827:/* DreamOS Spark Generator Fail-Open Guard
867:    return "/wp-json/emergence/v1/generate";
918:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',
477:/* DreamOS Spark Generator Fail-Open Visibility Guard */
```
