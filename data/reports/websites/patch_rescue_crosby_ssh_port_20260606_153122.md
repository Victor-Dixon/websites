# Patch Crosby Rescue SSH Port

generated=2026-06-06T15:31:22-05:00
script=/data/data/com.termux/files/home/.cache/rescue_crosby_wordpress_500_001.sh
root=/data/data/com.termux/files/home/projects/websites
domain=crosbyultimateevents.com

== PRECHECK ==
46:SSH_PORT="${HOSTINGER_SSH_PORT:-${SSH_PORT:-22}}"
68:echo "SSH_PORT=$SSH_PORT" | tee -a "$REPORT"
200:scp -P "$SSH_PORT" "$REMOTE_SCRIPT" "$SSH_TARGET:/tmp/remote_rescue_${DOMAIN}_${STAMP}.sh" 2>&1 | tee -a "$REPORT"
204:ssh -p "$SSH_PORT" "$SSH_TARGET" "bash /tmp/remote_rescue_${DOMAIN}_${STAMP}.sh '$REMOTE_ROOT' '$DOMAIN' '$STAMP'" 2>&1 | tee -a "$REPORT"

== PATCH PORT + KEY HANDLING ==
PATCH=PASS

== VERIFY PATCH ==
46:SSH_PORT="${HOSTINGER_PORT:-${HOSTINGER_SSH_PORT:-${SSH_PORT:-22}}}"
69:echo "SSH_PORT=$SSH_PORT" | tee -a "$REPORT"
47:SSH_KEY="${HOSTINGER_SSH_PRIVATE_KEY_FILE:-${SSH_KEY:-}}"
202:  scp -i "$SSH_KEY" -P "$SSH_PORT" "$REMOTE_SCRIPT" "$SSH_TARGET:/tmp/remote_rescue_${DOMAIN}_${STAMP}.sh" 2>&1 | tee -a "$REPORT"
204:  scp -P "$SSH_PORT" "$REMOTE_SCRIPT" "$SSH_TARGET:/tmp/remote_rescue_${DOMAIN}_${STAMP}.sh" 2>&1 | tee -a "$REPORT"
210:  ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_TARGET" "bash /tmp/remote_rescue_${DOMAIN}_${STAMP}.sh '$REMOTE_ROOT' '$DOMAIN' '$STAMP'" 2>&1 | tee -a "$REPORT"
212:  ssh -p "$SSH_PORT" "$SSH_TARGET" "bash /tmp/remote_rescue_${DOMAIN}_${STAMP}.sh '$REMOTE_ROOT' '$DOMAIN' '$STAMP'" 2>&1 | tee -a "$REPORT"

== CLOSEOUT ==
STATUS=PATCHED
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/patch_rescue_crosby_ssh_port_20260606_153122.md
