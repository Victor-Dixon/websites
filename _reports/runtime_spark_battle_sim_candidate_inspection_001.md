# Runtime Spark Battle Sim Candidate Inspection

generated: 2026-06-02T07:03:11-05:00

## Status

PASS

## Target

```text
runtime/spark-battle-sim
```

## Canonical Compare Path

```text
runtime/plugins/spark-battle-sim
```

## Counts

- candidate_files: 5
- plugin_files: 47
- candidate_only_relative_paths: 5
- plugin_only_relative_paths: 47
- same_checksum_matches: 0
- differing_checksum_matches: 0

## Classification Rule

- Promote only if candidate has unique source not present in plugin path.
- Archive if candidate is generated duplicate or stale.
- Ignore only after a restore-safe backup or explicit manifest.
- No movement performed in this lane.

## Comparison Artifact

```text
_reports/runtime_spark_battle_sim_candidate_compare_001.txt
```

## Recommendation

Review `_reports/runtime_spark_battle_sim_candidate_compare_001.txt`.

If candidate-only count is zero and diff count is zero, quarantine candidate next.

If candidate-only or diff count is nonzero, inspect diffs before promotion.

## Commit

```text
Add Spark Battle Sim runtime source inspection
```
