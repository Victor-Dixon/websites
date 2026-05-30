# Hostinger FreeRideInvestor Install Packet 026

## Upload Candidates

1. `~/projects/websites/_hostinger_build/dist/freerideinvestor-content-engine-0.1.0.zip`
2. `~/projects/websites/_hostinger_build/dist/dreamos-trading-tools-0.1.0.zip`

## Install Order

### Plugin 1

Upload and activate:

`freerideinvestor-content-engine-0.1.0.zip`

Expected after activation:

- No fatal error
- Admin sidebar shows CPTs:
  - `cheat_sheet`
  - `free_investor`
  - `tbow_tactics`

### Plugin 2

Upload and activate:

`dreamos-trading-tools-0.1.0.zip`

Expected after activation:

- No fatal error

## Smoke Test Page

Create a page titled:

`Plugin Smoke Test`

Content:

```text
[current_year]
[custom_message]
[cheat_sheet]
[tbow_tactics]
```

## Expected

- `[current_year]` renders current year.
- `[custom_message]` renders message output or safe fallback.
- `[cheat_sheet]` renders no fatal error.
- `[tbow_tactics]` renders no fatal error.

## Proof To Capture

Paste back:

1. Plugin activation result for both plugins.
2. Whether CPTs appeared.
3. Smoke page render result.
4. Any WordPress fatal/error text if shown.
