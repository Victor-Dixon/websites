# TSLA Command Center Hostinger Install Packet 001

generated=2026-06-01T07:01:58-05:00

## Purpose

Install the FreeRideInvestor TSLA command center surface.

This connects:

```text
DreamVault exporter
→ tsla-command-center.json
→ WordPress uploads path
→ [freeride_tsla_command_center]
```

## Files

### Plugin

```text
/data/data/com.termux/files/home/projects/websites/_hostinger_plan/freerideinvestor/upload_payload_tsla_command_center_001/dreamos-trading-tools-0.1.1.zip
```

Upload in WordPress:

```text
Plugins → Add New → Upload Plugin
Upload: dreamos-trading-tools-0.1.1.zip
Activate
```

### Snapshot JSON

```text
/data/data/com.termux/files/home/projects/websites/_hostinger_plan/freerideinvestor/upload_payload_tsla_command_center_001/wp-content/uploads/freerideinvestor/tsla-command-center.json
```

Upload through Hostinger File Manager or SFTP to:

```text
wp-content/uploads/freerideinvestor/tsla-command-center.json
```

Create the folder if missing:

```text
wp-content/uploads/freerideinvestor/
```

## WordPress page

Create or edit a page:

```text
Title: TSLA Command Center
Slug: tsla-command-center
Content:
[freeride_tsla_command_center]
```

Expected page URL:

```text
https://freerideinvestor.com/tsla-command-center/
```

## Verification

After upload:

1. Plugin activates without PHP error.
2. Page renders card titled: TSLA Daytrading Command Center.
3. Status badge shows: pass / fresh.
4. Snapshot generated time appears.
5. No provider API keys exist in WordPress.

## Safety

- WordPress displays derived command-center snapshot only.
- Raw provider collection remains private in DreamVault/DreamTradeData.
- No Polygon/Alpaca/Finnhub keys are stored in the plugin.

TSLA_COMMAND_CENTER_HOSTINGER_INSTALL_PACKET_001=PASS
