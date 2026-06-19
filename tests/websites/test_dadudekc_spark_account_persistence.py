from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
DADUDEKC = ROOT / "runtime/content/dadudekc.site"
PLUGIN = ROOT / "runtime/plugins/emergence-character-generator/emergence-character-generator.php"


def test_dadudekc_spark_pages_boot_account_character_persistence():
    generator = (DADUDEKC / "spark-generator/index.html").read_text(encoding="utf-8")
    spark_os = (DADUDEKC / "spark-os/index.html").read_text(encoding="utf-8")
    cards = (DADUDEKC / "cards/index.html").read_text(encoding="utf-8")
    missions = (DADUDEKC / "missions/index.html").read_text(encoding="utf-8")

    assert "window.DreamOSSparkAccount||{}" in generator
    assert "saveAccountCharacter(r,record);" in generator
    assert 'id="spark-account-save-status"' in generator
    assert "bootAccountCharacter();" in generator

    assert "window.DreamOSSparkAccount || {}" in spark_os
    assert "saveAccountCharacter(r, record);" in spark_os
    assert 'id="spark-os-account-status"' in spark_os
    assert "bootAccountCharacter();" in spark_os

    assert "SparkHeroCards.loadAccountCharacter().finally(render);" in cards
    assert "hydrateAccountCharacter().finally(function(){ renderStatus(); });" in missions


def test_plugin_emits_static_account_bridge_and_sanitizes_extended_character_payload():
    php = PLUGIN.read_text(encoding="utf-8")

    assert "function dreamos_emergence_static_account_config()" in php
    assert "window.DreamOSSparkAccount" in php
    assert "array('/spark-generator', '/spark-os', '/cards', '/missions', '/battles')" in php
    assert "'nonce' => wp_create_nonce('wp_rest')" in php
    assert "'loggedIn' => is_user_logged_in()" in php

    assert "'lead_domain' => isset($payload['lead_domain']) ? sanitize_text_field($payload['lead_domain']) : ''" in php
    assert "'manifested_domains' => array()" in php
    assert "'domains' => array()" in php
    assert "'card_id' => isset($payload['card_id']) ? sanitize_key($payload['card_id']) : ''" in php
    assert "'team_name' => isset($payload['team_name']) ? sanitize_text_field($payload['team_name']) : ''" in php
    assert "'domain' => isset($power['domain']) ? sanitize_text_field($power['domain']) : ''" in php
