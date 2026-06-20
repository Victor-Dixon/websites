# Emergence → MaskZero Promotion Plan

## Authority

- `maskzero.site` is canonical Emergence/Spark home.
- `dadudekc.site` is legacy/reference only.
- No deletion in this lane.

## Destination

`websites/runtime/content/maskzero.site`

## Required Routes

| Route | Present | Action |
|---|---:|---|
| `spark-dashboard` | `True` | `keep` |
| `spark-generator` | `True` | `keep` |
| `spark-login` | `True` | `keep` |
| `spark-signup` | `True` | `keep` |
| `spark-os` | `True` | `keep` |
| `meridian-map` | `True` | `keep` |
| `missions` | `True` | `keep` |
| `battle-simulator` | `True` | `keep` |
| `battles` | `True` | `keep` |
| `gauntlet` | `False` | `create_or_promote` |
| `what-if-arena` | `False` | `create_or_promote` |
| `newspaper` | `False` | `create_or_promote` |
| `emergence` | `False` | `create_or_promote` |

## Source Roots

- `/data/data/com.termux/files/home/projects/spark-protocol` exists=True files=194
- `/data/data/com.termux/files/home/projects/DreamVault/runtime/data/spark_protocol` exists=True files=5
- `/data/data/com.termux/files/home/projects/DreamVault/runtime/data/meridian_city` exists=True files=1
- `/data/data/com.termux/files/home/projects/DreamVault/runtime/tools/spark_protocol` exists=True files=11
- `/data/data/com.termux/files/home/projects/DreamVault/runtime/tools/meridian_city` exists=True files=1
- `/data/data/com.termux/files/home/projects/websites/runtime/plugins/emergence-character-generator` exists=True files=7
- `/data/data/com.termux/files/home/projects/websites/runtime/plugins/spark-battle-sim` exists=True files=47
- `/data/data/com.termux/files/home/projects/websites/runtime/themes/dreamos-emergence` exists=True files=9

## Legacy Roots

- `/data/data/com.termux/files/home/projects/websites/runtime/content/dadudekc.site` exists=True files=14 classification=legacy_reference_only
- `/data/data/com.termux/files/home/projects/DreamVault/sites/production/websites/dadudekc.site` exists=True files=35 classification=legacy_reference_only
- `/data/data/com.termux/files/home/projects/DreamVault/runtime/content/dadudekc.site` exists=True files=3 classification=legacy_reference_only

## Blocked

- Do not make dadudekc.site canonical.
- Do not delete legacy artifacts.
- Do not overwrite maskzero.site files silently.