from __future__ import annotations

from dataclasses import dataclass, asdict
from datetime import datetime, timezone
from hashlib import sha256
from typing import Any


class SparkAccountError(ValueError):
    pass


@dataclass(frozen=True)
class SparkAccount:
    account_id: str
    provider: str
    provider_subject: str
    display_name: str
    created_at: str


@dataclass(frozen=True)
class SparkCharacter:
    character_id: str
    account_id: str
    character_name: str
    spark_type: str
    domains: tuple[str, ...]
    power_signature_rating: int
    combat_capability_rating: int
    created_at: str


def utc_now() -> str:
    return datetime.now(timezone.utc).isoformat(timespec="seconds")


def stable_id(prefix: str, *parts: str) -> str:
    raw = "::".join([prefix, *parts]).encode("utf-8")
    return f"{prefix}_{sha256(raw).hexdigest()[:16]}"


def create_account(provider: str, provider_subject: str, display_name: str) -> SparkAccount:
    provider = provider.strip().lower()
    provider_subject = provider_subject.strip()
    display_name = display_name.strip()

    if provider not in {"wordpress", "google", "github", "discord", "local"}:
        raise SparkAccountError(f"unsupported provider: {provider}")
    if not provider_subject:
        raise SparkAccountError("provider_subject is required")
    if not display_name:
        raise SparkAccountError("display_name is required")

    return SparkAccount(
        account_id=stable_id("acct", provider, provider_subject),
        provider=provider,
        provider_subject=provider_subject,
        display_name=display_name,
        created_at=utc_now(),
    )


def create_character(
    *,
    account: SparkAccount,
    character_name: str,
    spark_type: str,
    domains: list[str] | tuple[str, ...],
    power_signature_rating: int,
    combat_capability_rating: int,
) -> SparkCharacter:
    character_name = character_name.strip()
    spark_type = spark_type.strip()
    normalized_domains = tuple(d.strip() for d in domains if d.strip())

    if not character_name:
        raise SparkAccountError("character_name is required")
    if not spark_type:
        raise SparkAccountError("spark_type is required")
    if not normalized_domains:
        raise SparkAccountError("at least one domain is required")
    if not 0 <= power_signature_rating <= 100:
        raise SparkAccountError("power_signature_rating must be 0-100")
    if not 0 <= combat_capability_rating <= 100:
        raise SparkAccountError("combat_capability_rating must be 0-100")

    return SparkCharacter(
        character_id=stable_id("char", account.account_id, character_name, spark_type, ",".join(normalized_domains)),
        account_id=account.account_id,
        character_name=character_name,
        spark_type=spark_type,
        domains=normalized_domains,
        power_signature_rating=power_signature_rating,
        combat_capability_rating=combat_capability_rating,
        created_at=utc_now(),
    )


def assert_character_owner(account: SparkAccount, character: SparkCharacter) -> None:
    if character.account_id != account.account_id:
        raise SparkAccountError("character does not belong to account")


def build_discord_identity_handoff(account: SparkAccount, character: SparkCharacter) -> dict[str, Any]:
    assert_character_owner(account, character)

    return {
        "event_type": "spark.character.account_bound",
        "account": {
            "account_id": account.account_id,
            "provider": account.provider,
            "display_name": account.display_name,
        },
        "character": {
            "character_id": character.character_id,
            "character_name": character.character_name,
            "spark_type": character.spark_type,
            "domains": list(character.domains),
            "power_signature_rating": character.power_signature_rating,
            "combat_capability_rating": character.combat_capability_rating,
        },
        "discord": {
            "recommended_channel": "spark-character-registry",
            "routing_key": f"spark.account.{account.account_id}",
            "summary": f"{account.display_name} bound {character.character_name} as {character.spark_type}.",
        },
        "proof": {
            "ownership_verified": True,
            "handoff_schema": "spark.character.account_bound.v1",
        },
    }


def serialize_account(account: SparkAccount) -> dict[str, Any]:
    return asdict(account)


def serialize_character(character: SparkCharacter) -> dict[str, Any]:
    data = asdict(character)
    data["domains"] = list(character.domains)
    return data
