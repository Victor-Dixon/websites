# Emergence OpenAI Provider Error Classification 096

## Result

- Captured sanitized OpenAI provider response.
- Classified cause: `billing_or_quota`
- Recommended fix: Check OpenAI billing, project limits, usage caps, and available credits.
- No key-shaped leaks detected.

## Sanitized JSON

```json
{
  "status": "provider_error",
  "provider": "openai",
  "prompt_only": true,
  "image_url_present": false,
  "message_sanitized": "OpenAI image provider returned HTTP 400. Billing hard limit has been reached.",
  "classification": "billing_or_quota",
  "recommended_fix": "Check OpenAI billing, project limits, usage caps, and available credits.",
  "no_key_leaks": true
}
```

## Raw Proof

```text
== INSPECT PROVIDER ERROR SAFELY ==
PROVIDER_STATUS=provider_error
PROVIDER=openai
IMAGE_URL_PRESENT=False
CLASSIFICATION=billing_or_quota
RECOMMENDED_FIX=Check OpenAI billing, project limits, usage caps, and available credits.
SANITIZED_MESSAGE_BEGIN
OpenAI image provider returned HTTP 400. Billing hard limit has been reached.
SANITIZED_MESSAGE_END
PROVIDER_ERROR_INSPECTION_JSON=PASS
PROVIDER_ERROR_NO_KEY_LEAKS=PASS
EMERGENCE_OPENAI_PROVIDER_ERROR_INSPECTION=PASS
```

STATUS=PASS
