# Website Repo Delete-Ready Manifest

generated=2026-06-07T05:49:43.675004+00:00
projects=/data/data/com.termux/files/home/projects
canonical_websites=/data/data/com.termux/files/home/projects/websites

## Summary

```json
{
  "delete_ready_after_final_review": 3,
  "hold_needs_salvage_proof": 17,
  "preserve": 1
}
```

## Repos

| Decision | Dirty | Repo | Reason | Salvage Proof |
|---|---:|---|---|---|
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/AgentTools` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 36 | `/data/data/com.termux/files/home/projects/DreamOS` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 1 | `/data/data/com.termux/files/home/projects/DreamVault` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/FocusForge` | website-like repo but canonical salvage proof not found | NONE |
| delete_ready_after_final_review | 0 | `/data/data/com.termux/files/home/projects/FreeRideInvestor` | salvage proof exists in canonical websites repo | `_deploy`<br>`_shared_plugins` |
| delete_ready_after_final_review | 0 | `/data/data/com.termux/files/home/projects/FreerideinvestorWebsite` | salvage proof exists in canonical websites repo | `_deploy`<br>`_shared_plugins` |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/HomeSchool_Mastery` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/ProfessorSama` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 8 | `/data/data/com.termux/files/home/projects/TROOP` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/The-emergence-` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 2 | `/data/data/com.termux/files/home/projects/Thea` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/TradingRobotPlugWeb` | website-like repo but canonical salvage proof not found | NONE |
| delete_ready_after_final_review | 0 | `/data/data/com.termux/files/home/projects/bible-application` | Bible app salvaged into websites/experiments/bible-application | `data/reports/repo_consolidation`<br>`experiments/bible-application/README.md`<br>`experiments/bible-application/clean_bible_downloader.py`<br>`experiments/bible-application/index.html` |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/contract-leads` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 1 | `/data/data/com.termux/files/home/projects/discord_teks_tester` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/gpt_automation` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/projectscanner` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/socialmediamanager` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/stocktwits-analyzer` | website-like repo but canonical salvage proof not found | NONE |
| hold_needs_salvage_proof | 0 | `/data/data/com.termux/files/home/projects/trade_analyzer` | website-like repo but canonical salvage proof not found | NONE |
| preserve | 5 | `/data/data/com.termux/files/home/projects/websites` | explicit preserve rule | NONE |

## Delete-Ready Commands

Not executed. Review before running.

```bash
# rm -rf /data/data/com.termux/files/home/projects/FreeRideInvestor
# rm -rf /data/data/com.termux/files/home/projects/FreerideinvestorWebsite
# rm -rf /data/data/com.termux/files/home/projects/bible-application
```

STATUS=WEBSITE_REPO_DELETE_READY_MANIFEST_CREATED