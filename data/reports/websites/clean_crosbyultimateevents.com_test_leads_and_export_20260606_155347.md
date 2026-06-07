# Clean Crosby Test Leads And Add Export

generated=2026-06-06T15:53:47-05:00
domain=crosbyultimateevents.com
remote_root=/home/u996867598/domains/crosbyultimateevents.com/public_html

== REMOTE CLEAN TEST LEADS ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
BACKUP=/home/u996867598/domains/crosbyultimateevents.com/public_html/.private/leads/event_inquiries.before_test_cleanup_20260606_155347.csv
ROWS_BEFORE=2
ROWS_REMOVED=2
ROWS_AFTER=0
CSV_CLEAN=PASS

== WRITE EXPORT SCRIPT ==
EXPORT_SCRIPT=/data/data/com.termux/files/home/projects/websites/runtime/scripts/export_crosby_event_inquiries.sh

== TEST EXPORT ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
EXPORT=PASS
OUT=/data/data/com.termux/files/home/projects/websites/data/exports/crosbyultimateevents/event_inquiries_20260606_155348.csv
1 /data/data/com.termux/files/home/projects/websites/data/exports/crosbyultimateevents/event_inquiries_20260606_155348.csv

== REMOTE CSV VERIFY CLEAN ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
CSV_LINE_COUNT=1
TEST_ROWS_PRESENT=FAIL
