# Purge Sensitive Archive, Keep Manifest

generated=2026-06-06T17:46:37-05:00

## Result

UNSAFE_ARCHIVE_REMOVED=PASS_OR_SKIP
MANIFEST_ONLY_ARCHIVE=runtime/quarantine/spark_emergence_sensitive_archive_purged_20260606_174637
SECRET_MATERIAL_COMMITTED=NO

## Reason

A failed-run archive contained credential material. The archive content was removed from commit scope. Only a manifest explaining the purge is safe to commit.

## Required Follow-Up

Credential owner should rotate exposed credentials. Do not use or republish the leaked value.
