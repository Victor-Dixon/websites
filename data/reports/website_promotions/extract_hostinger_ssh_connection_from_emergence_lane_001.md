# Extract Hostinger SSH Connection From Emergence Lane 001

- Generated: 2026-06-03T17:43:20
- Status: SSH_CONNECTION_CANDIDATE_SELECTED
- Candidate count: 183

## Selected

- Host: SET
- User: u996867598
- Port: 65002

## Guardrail

No secrets printed. No upload performed. Route-only preflight only.

## Host Candidates

- `weareswarm` score=164
- `weareswarm`` score=68
- = score=14
- dadudekc.com score=12

## Top Source Candidates

### data/reports/website_promotions/resolve_weareswarm_sftp_target_001.json
- Score: 18
- Hits: hostinger, ssh, sftp, dadudekc.com, maskzero.site, emergence, u996867598, 65002, remote_root, public_html
- Hosts found: `weareswarm`
- Ports found: none
- Users found: u201311, u996867598
- L14:         "hostinger"
- L19:           "text": "    source_root: collected/hostinger/wordpress"
- L23:           "text": "    manifest: runtime/deploy/hostinger_custom_asset_collection_manifest.yaml"
- L30:         "hostinger"
- L35:           "text": "hostinger_profile: ~/.config/dreamos/hostinger_freeride_github_secrets.env"
- L40:       "path": "runtime/deploy/hostinger/hostinger_access_registry_manifest.yaml",
- L43:         "hostinger"
- L48:           "text": "name: hostinger_access_registry"

### data/reports/website_promotions/resolve_weareswarm_sftp_target_001.md
- Score: 17
- Hits: hostinger, ssh, sftp, dadudekc.com, maskzero.site, emergence, u996867598, remote_root, public_html
- Hosts found: `weareswarm``
- Ports found: none
- Users found: u996867598
- L1: # Resolve WeAreSwarm SFTP Target 001
- L20: ### `runtime/deploy/hostinger/hostinger_access_registry_manifest.yaml`
- L21: - Hit terms: `weareswarm, hostinger`
- L22: - L2: `name: hostinger_access_registry`
- L23: - L4: `env_dir: runtime/env/hostinger/sites`
- L24: - L5: `preflight_script: runtime/scripts/hostinger_access_preflight.sh`
- L25: - L6: `latest_report_txt: _reports/hostinger_access_preflight_002b.txt`
- L26: - L7: `latest_report_md: _reports/hostinger_access_preflight_002b.md`

### _reports/tsla_command_center_hostinger_deploy_diagnosis_001.txt
- Score: 14
- Hits: hostinger, dadudekc.com, maskzero.site, emergence, u996867598, public_html
- Hosts found: =
- Ports found: none
- Users found: u996867598
- L1: == TSLA COMMAND CENTER HOSTINGER DEPLOY DIAGNOSIS 001 ==
- L9: u996867598
- L10: /home/u996867598
- L15: /home/u996867598/domains
- L16: /home/u996867598/domains/ariajet.site
- L17: /home/u996867598/domains/ariajet.site/public_html
- L18: /home/u996867598/domains/crosbyultimateevents.com
- L19: /home/u996867598/domains/crosbyultimateevents.com/public_html

### data/reports/website_promotions/reuse_emergence_hostinger_creds_for_weareswarm_preflight_001.md
- Score: 13
- Hits: hostinger, ssh, sftp, dadudekc.com, maskzero.site, emergence, u996867598, 65002, remote_root, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L1: # Reuse Emergence Hostinger Credentials For WeAreSwarm Preflight 001
- L9: Secrets are not printed. Use existing Hostinger credentials only for SSH preflight; no upload.
- L13: ### `data/reports/website_promotions/preflight_weareswarm_site_sftp_route_upload_001.json`
- L15: - Hits: `HOSTINGER_SSH_HOST, HOSTINGER_SSH_USER, HOSTINGER_SSH_PORT, SSH_HOST, SSH_USER, SSH_PORT, u996867598, 65002`
- L16: - L3: `  "status": "BLOCKED_MISSING_SSH_HOST",`
- L17: - L5: `  "ssh_host_present": false,`
- L18: - L6: `  "ssh_host": "",`
- L19: - L7: `  "ssh_user": "u996867598",`

### data/reports/website_promotions/reuse_emergence_hostinger_creds_for_weareswarm_preflight_001.json
- Score: 12
- Hits: hostinger, ssh, sftp, dadudekc.com, maskzero.site, u996867598, 65002, remote_root, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L3:   "status": "BLOCKED_SSH_PREFLIGHT_NOT_READY",
- L7:       "path": "data/reports/website_promotions/preflight_weareswarm_site_sftp_route_upload_001.json",
- L10:         "HOSTINGER_SSH_HOST",
- L11:         "HOSTINGER_SSH_USER",
- L12:         "HOSTINGER_SSH_PORT",
- L13:         "SSH_HOST",
- L14:         "SSH_USER",
- L15:         "SSH_PORT",

### _reports/websites_hostinger_access_inspection_001.txt
- Score: 11
- Hits: hostinger, ssh, dadudekc.com, maskzero.site, emergence, u996867598, remote_root, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L1: == WEBSITES HOSTINGER ACCESS INSPECTION 001 ==
- L5: == HOSTINGER FILES ==
- L6: ./.github/workflows/deploy-freeride-hostinger.yml
- L8: ./_configs/hostinger_plan_config_013.json
- L9: ./_hostinger_plan/freerideinvestor/hostinger_freeride_install_packet_026.md
- L10: ./_hostinger_plan/freerideinvestor/hostinger_freeride_install_proof_026.md
- L11: ./_hostinger_plan/freerideinvestor/tsla_command_center_hostinger_install_packet_001.md
- L12: ./_reports/emergence_hostinger_image_env_095.txt

### data/reports/website_promotions/weareswarm_sftp_target_candidates_001.json
- Score: 11
- Hits: hostinger, ssh, sftp, dadudekc.com, maskzero.site, u996867598, remote_root, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L4:   "source": "data/reports/website_promotions/resolve_weareswarm_sftp_target_001.json",
- L12:       "path": "_reports/hostinger_access_preflight_002b.md",
- L16:         "public_html",
- L18:         "hostinger",
- L20:         "ssh"
- L25:           "text": "# Hostinger Access Preflight 002b"
- L29:           "text": "Verify Hostinger SSH/root access for all configured site env files before deploy lanes."
- L33:           "text": "== HOSTINGER ACCESS PREFLIGHT =="

### data/reports/website_promotions/weareswarm_sftp_target_candidates_001.md
- Score: 11
- Hits: hostinger, ssh, sftp, dadudekc.com, maskzero.site, u996867598, remote_root, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L1: # WeAreSwarm SFTP Target Candidates 001
- L16: ### 1. `_reports/hostinger_access_preflight_002b.md`
- L18: - Reasons: `weareswarm, public_html, domains, hostinger, deploy, ssh`
- L20:   - `/home/u996867598/domain`
- L21:   - `public_html`
- L23:   - L1: `# Hostinger Access Preflight 002b`
- L24:   - L5: `Verify Hostinger SSH/root access for all configured site env files before deploy lanes.`
- L25:   - L10: `== HOSTINGER ACCESS PREFLIGHT ==`

### _reports/hostinger_access_preflight_002b.md
- Score: 10
- Hits: hostinger, ssh, dadudekc.com, maskzero.site, u996867598, remote_root, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L1: # Hostinger Access Preflight 002b
- L5: Verify Hostinger SSH/root access for all configured site env files before deploy lanes.
- L10: == HOSTINGER ACCESS PREFLIGHT ==
- L15: REMOTE_ROOT=/home/u996867598/domains/ariajet.site/public_html
- L16: SSH_ROOT_WRITABLE=PASS
- L22: REMOTE_ROOT=/home/u996867598/domains/crosbyultimateevents.com/public_html
- L23: SSH_ROOT_WRITABLE=PASS
- L26: == SITE dadudekc.com ==

### _reports/hostinger_access_preflight_002b.txt
- Score: 10
- Hits: hostinger, ssh, dadudekc.com, maskzero.site, u996867598, remote_root, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L1: == HOSTINGER ACCESS PREFLIGHT ==
- L6: REMOTE_ROOT=/home/u996867598/domains/ariajet.site/public_html
- L7: SSH_ROOT_WRITABLE=PASS
- L13: REMOTE_ROOT=/home/u996867598/domains/crosbyultimateevents.com/public_html
- L14: SSH_ROOT_WRITABLE=PASS
- L17: == SITE dadudekc.com ==
- L20: REMOTE_ROOT=/home/u996867598/domains/dadudekc.com/public_html
- L21: SSH_ROOT_WRITABLE=PASS

### _reports/hostinger_wordpress_inventory_044.md
- Score: 10
- Hits: hostinger, ssh, dadudekc.com, maskzero.site, u996867598, 65002, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L1: # Hostinger WordPress Inventory 044
- L9: - `/data/data/com.termux/files/home/projects/websites/runtime/deploy/hostinger_sites_manifest.yaml`
- L10: - `/data/data/com.termux/files/home/projects/websites/runtime/deploy/hostinger_plugin_registry.yaml`
- L11: - `/data/data/com.termux/files/home/projects/websites/runtime/deploy/hostinger_theme_registry.yaml`
- L12: - `/data/data/com.termux/files/home/projects/websites/runtime/deploy/hostinger_deploy_proof_profile.yaml`
- L13: - `/data/data/com.termux/files/home/projects/websites/_reports/hostinger_wordpress_raw_inventory_044.txt`
- L18: HOSTINGER_SSH_LOGIN=PASS
- L19: HOSTINGER_USER=u996867598

### _reports/hostinger_wordpress_raw_inventory_044.txt
- Score: 10
- Hits: hostinger, ssh, dadudekc.com, maskzero.site, u996867598, 65002, public_html
- Hosts found: none
- Ports found: none
- Users found: u996867598
- L1: HOSTINGER_SSH_LOGIN=PASS
- L2: HOSTINGER_USER=u996867598
- L3: HOSTINGER_HOST=157.173.214.121
- L4: HOSTINGER_PORT=65002
- L8: WP_ROOT=/home/u996867598/domains/ariajet.site/public_html
- L10: PLUGINS_DIR=/home/u996867598/domains/ariajet.site/public_html/wp-content/plugins
- L17: PLUGIN=hostinger
- L18: PLUGIN_MAIN=hostinger|index.php

## Next Action

Export selected host/user/port and run guarded remote preflight. No upload.
