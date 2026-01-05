# Swarm Heartbeat

Swarm Heartbeat is a lightweight WordPress plugin that renders a live proof card for multi-agent activity. It supports a shortcode, a Gutenberg block, and an optional header/banner injection.

## Setup

1. Upload the plugin folder to `wp-content/plugins/swarm-heartbeat`.
2. Activate **Swarm Heartbeat** in the WordPress admin.
3. Visit **Settings → Swarm Heartbeat** to copy the secret token and adjust stale thresholds.
4. Post heartbeat payloads to the REST endpoint or use the manual admin form.

## REST ingestion

Endpoint: `POST /wp-json/swarm/v1/heartbeat`

Include the secret token in the `X-Swarm-Token` header or `token` parameter.

```bash
curl -X POST "https://example.com/wp-json/swarm/v1/heartbeat" \
  -H "Content-Type: application/json" \
  -H "X-Swarm-Token: YOUR_SECRET_TOKEN" \
  -d '{
    "last_ping_iso": "2024-06-01T18:25:43Z",
    "agents_active": 6,
    "last_mission": {
      "title": "Launch the ROI proof banner",
      "url": "https://example.com/mission",
      "status": "in-progress"
    },
    "last_event": {
      "label": "Agent swarm updated CTA",
      "url": "https://example.com/event"
    }
  }'
```

## Shortcode

Use the shortcode anywhere in content:

```
[swarm_heartbeat]
```

Optional attribute to force the banner styling:

```
[swarm_heartbeat show_banner="true"]
```

## Block

Insert the **Swarm Heartbeat** block in the block editor. The front end renders the live card.

## Header/CTA banner

Enable **Show header banner** in settings to inject the card at the top of the page using `wp_body_open` (with a footer fallback).

## Admin preview & logs

The settings page includes:
- Manual payload form.
- Preview button that renders the card.
- Log table of the last 20 heartbeat posts (timestamp + IP).

## Styling

Minimal CSS is bundled in `swarm-heartbeat.css`.
