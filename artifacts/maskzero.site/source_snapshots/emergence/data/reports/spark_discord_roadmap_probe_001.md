# Spark Discord Roadmap Probe

Generated: 2026-05-23T21:13:11-05:00
PWD: /data/data/com.termux/files/home/projects/spark-protocol

## Git
fatal: not a git repository (or any parent up to mount point /data/data)
Stopping at filesystem boundary (GIT_DISCOVERY_ACROSS_FILESYSTEM not set).

## Runtime Tasks
runtime/tasks/spark_protocol_absorb_quizbot_into_node_001.yaml
runtime/tasks/spark_protocol_foundation_bootstrap_001.yaml

## Key Scripts
absorb_quizbot_into_node_001.sh
adaptive_quiz_comic_profile_001.sh
add_adaptive_questions_left_tests_001.sh
add_adaptive_quiz_stress_percentile_tests_001.sh
add_ai_interpretation_and_e2e_001.sh
add_percentile_distribution_tests_001.sh
add_percentile_floor_tests_001.sh
add_quiz_contract_tests_001.sh
ai_profile_flavor_reader_001.sh
complete_adaptive_engine_and_fix_questions_001.sh
diagnose_discord_button_runtime_001.sh
fix_discord_interaction_race_001.sh
fix_node_quiz_paths_001.sh
fix_quiz_contract_red_001.sh
harden_quiz_state_machine_001.sh
inspect_quizbot_absorb_001.sh
patch_discord_button_reducer_001.sh
quiz_view_self_heal_001.sh
remove_mutable_question_pointer_001.sh
spark_protocol_scoring_engine_001.sh
stale_safe_button_quiz_001.sh
upgrade_quiz_schema_a_to_g_001.sh
wire_adaptive_quiz_to_discord_001.sh
wire_quiz_session_reducer_to_discord_001.sh
wire_scoring_to_discord_sheet_001.sh
write_question_specific_g_options_001.sh

## Package
{
  "name": "spark-protocol",
  "private": true,
  "workspaces": [
    "apps/*",
    "packages/*"
  ],
  "scripts": {
    "dev": "node apps/discord-bot/src/index.js",
    "test": "node --test tests/*.test.js",
    "test:quiz": "node --test tests/quiz_contract.test.js",
    "test:e2e": "node --test tests/e2e/quiz_flow.e2e.test.js",
    "test:all": "node --test tests/*.test.js tests/e2e/*.test.js tests/scoring/*.test.js",
    "test:scoring": "node --test tests/scoring/scoring_engine.test.js",
    "test:contract": "node --test tests/quiz_contract.test.js",
    "test:mobile": "npm run test:contract && npm run test:answers && npm run test:e2e && npm run test:scoring && npm run test:adaptive && npm run test:adaptive-progress && npm run test:adaptive-discord && npm run test:adaptive-full && npm run test:stress && npm run test:percentile && npm run test:percentile-distribution && npm run test:ai && npm run test:discord-session && npm run test:derived-pointer && npm run test:quiz-session && npm run test:view-self-heal",
    "test:adaptive": "node --test tests/scoring/adaptive_quiz.test.js",
    "test:adaptive-progress": "node --test tests/scoring/adaptive_progress.test.js",
    "test:adaptive-discord": "node --test tests/e2e/adaptive_discord_model.e2e.test.js",
    "test:answers": "node --test tests/answer_choices.test.js",
    "test:adaptive-full": "node --test tests/scoring/adaptive_engine_full.test.js",
    "test:stress": "node --test tests/e2e/adaptive_quiz_stress_percentile.e2e.test.js",
    "test:percentile": "node --test tests/scoring/percentile_floor.test.js",
    "test:percentile-distribution": "node --test tests/scoring/percentile_distribution.test.js",
    "test:ai": "node --test tests/ai/*.test.js",
    "test:discord-session": "node --test tests/e2e/discord_session_integrity.e2e.test.js",
    "test:derived-pointer": "node --test tests/e2e/derived_question_pointer.e2e.test.js",
    "test:quiz-session": "node --test tests/e2e/quiz_session_state_machine.e2e.test.js",
    "test:view-self-heal": "node --test tests/e2e/quiz_view_self_heal.e2e.test.js",
    "test:stale-buttons": "node --test tests/e2e/stale_safe_button_quiz.e2e.test.js"
  },
  "type": "module"
}

## Workspace packages
apps/discord-bot/package.json
apps/discord-bot/src/index.js
apps/discord-quiz-bot/bot.py
packages/aegis-interpreter/index.js
packages/profile-flavor-reader/index.js
packages/quiz-button-session/index.js
packages/quiz-engine/index.js
packages/quiz-session/index.js
packages/scoring-engine/index.js

## Existing Tests
tests/ai/profile_flavor_reader.test.js
tests/answer_choices.test.js
tests/e2e/adaptive_discord_model.e2e.test.js
tests/e2e/adaptive_quiz_stress_percentile.e2e.test.js
tests/e2e/derived_question_pointer.e2e.test.js
tests/e2e/discord_session_integrity.e2e.test.js
tests/e2e/quiz_flow.e2e.test.js
tests/e2e/quiz_session_state_machine.e2e.test.js
tests/e2e/quiz_view_self_heal.e2e.test.js
tests/e2e/scored_sheet_flow.e2e.test.js
tests/e2e/stale_safe_button_quiz.e2e.test.js
tests/quiz_contract.test.js
tests/scoring/adaptive_engine_full.test.js
tests/scoring/adaptive_progress.test.js
tests/scoring/adaptive_quiz.test.js
tests/scoring/percentile_distribution.test.js
tests/scoring/percentile_floor.test.js
tests/scoring/scoring_engine.test.js

## NPM test

> test
> node --test tests/*.test.js

✔ each canonical quiz question accepts A-G answers (24.650039ms)
✔ each question exposes exactly seven domain choices (8.116346ms)
✔ canonical quiz loads with 72 questions and A-G schema (16.20073ms)
✔ canonical quiz has required metadata (17.809269ms)
✔ every question has id, text, and seven A-G options (5.369616ms)
✔ domain and flavor question ranges are structurally valid (6.072115ms)
ℹ tests 6
ℹ suites 0
ℹ pass 6
ℹ fail 0
ℹ cancelled 0
ℹ skipped 0
ℹ todo 0
ℹ duration_ms 625.180769
