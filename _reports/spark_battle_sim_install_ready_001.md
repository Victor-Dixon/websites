# Spark Battle Sim Install Readiness Packet

generated: 2026-06-02T06:56:25-05:00

## Status

PASS

## Package

```text
/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/spark-battle-sim-install-ready-001.zip
```

## Verified

- Plugin directory exists: `/data/data/com.termux/files/home/projects/websites/runtime/plugins/spark-battle-sim`
- Secret leak scan passed
- Install-ready zip produced
- Task artifact written: `/data/data/com.termux/files/home/projects/websites/runtime/tasks/package_spark_battle_sim_install_ready_001.yaml`

## Hostinger / WordPress Upload Checklist

1. WordPress Admin → Plugins → Add New
2. Upload Plugin
3. Upload:
   ```text
   /data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/spark-battle-sim-install-ready-001.zip
   ```
4. Activate plugin
5. Create or update page:
   ```text
   /battles/
   ```
6. Add shortcode if required:
   ```text
   [spark_battle_sim]
   ```
7. Smoke test:
   - page loads
   - character matchup can resolve
   - no browser-visible secret
   - narrator output returns through server-side path only

## Verification Commands

```bash
test -f "/data/data/com.termux/files/home/projects/websites/_hostinger_build/dist/spark-battle-sim-install-ready-001.zip"
grep -RInE "OPENAI_API_KEY|ANTHROPIC_API_KEY|GEMINI_API_KEY|x-api-key|sk-[A-Za-z0-9]|AIza[[:alnum:]_-]{20,}" "/data/data/com.termux/files/home/projects/websites/runtime/plugins/spark-battle-sim" "/data/data/com.termux/files/home/projects/websites/runtime/tasks" || true
```

## Commit

```text
Add Spark Battle Sim install readiness packet
```
