import pytest

from runtime.account_system.spark_account_contract import (
    SparkAccountError,
    assert_character_owner,
    build_discord_identity_handoff,
    create_account,
    create_character,
)


def test_account_save_contract_creates_stable_provider_account():
    account = create_account(
        provider="discord",
        provider_subject="discord-user-123",
        display_name="Test Spark",
    )

    again = create_account(
        provider="discord",
        provider_subject="discord-user-123",
        display_name="Test Spark",
    )

    assert account.account_id == again.account_id
    assert account.provider == "discord"
    assert account.display_name == "Test Spark"


def test_character_ownership_contract_binds_character_to_account():
    account = create_account("local", "local-user-001", "Local Tester")
    character = create_character(
        account=account,
        character_name="Astra Vale",
        spark_type="Mind Spark",
        domains=["Mind", "Energy"],
        power_signature_rating=81,
        combat_capability_rating=44,
    )

    assert character.account_id == account.account_id
    assert character.character_id.startswith("char_")
    assert_character_owner(account, character)


def test_character_ownership_rejects_cross_account_access():
    owner = create_account("local", "owner-001", "Owner")
    intruder = create_account("local", "intruder-001", "Intruder")
    character = create_character(
        account=owner,
        character_name="Astra Vale",
        spark_type="Mind Spark",
        domains=["Mind", "Energy"],
        power_signature_rating=81,
        combat_capability_rating=44,
    )

    with pytest.raises(SparkAccountError):
        assert_character_owner(intruder, character)


def test_discord_identity_handoff_payload_is_public_safe_and_routable():
    account = create_account("discord", "discord-user-123", "Test Spark")
    character = create_character(
        account=account,
        character_name="Astra Vale",
        spark_type="Mind Spark",
        domains=["Mind", "Energy"],
        power_signature_rating=81,
        combat_capability_rating=44,
    )

    payload = build_discord_identity_handoff(account, character)

    assert payload["event_type"] == "spark.character.account_bound"
    assert payload["proof"]["ownership_verified"] is True
    assert payload["proof"]["handoff_schema"] == "spark.character.account_bound.v1"
    assert payload["discord"]["recommended_channel"] == "spark-character-registry"
    assert payload["discord"]["routing_key"].startswith("spark.account.acct_")
    assert payload["character"]["character_name"] == "Astra Vale"
    assert payload["character"]["domains"] == ["Mind", "Energy"]
