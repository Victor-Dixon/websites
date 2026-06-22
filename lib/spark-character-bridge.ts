export interface SparkCharacterRecord {
  name?: string;
  lead_domain?: string;
  cast?: string;
  spark_signature?: string;
  combat_capability?: string;
  domains?: string[];
  manifested?: string[];
  image_prompt?: string;
  profile_shape?: string;
}

const RECORD_KEY = "mz.manifestation.v1";
const LEGACY_KEYS = ["dreamos.currentSparkCharacter.v1", "dreamos.singleSparkCharacter.v1"];

function readStorage(key: string): SparkCharacterRecord | null {
  if (typeof window === "undefined") {
    return null;
  }
  try {
    const raw = window.localStorage.getItem(key);
    if (!raw) {
      return null;
    }
    const parsed = JSON.parse(raw) as SparkCharacterRecord;
    return parsed && typeof parsed === "object" ? parsed : null;
  } catch {
    return null;
  }
}

export function loadSparkCharacter(): SparkCharacterRecord | null {
  const primary = readStorage(RECORD_KEY);
  if (primary) {
    return primary;
  }
  for (const key of LEGACY_KEYS) {
    const legacy = readStorage(key);
    if (legacy) {
      return legacy;
    }
  }
  return null;
}

export function buildSkyMotionPromptFromCharacter(character: SparkCharacterRecord): string {
  const lead = character.lead_domain || "Manifestation";
  const cast = character.cast || "hero";
  const signature = character.spark_signature || "unknown spark signature";
  const domains = (character.manifested || character.domains || []).join(", ") || "core domains";
  const visual = character.image_prompt || character.profile_shape || "";
  return [
    `Cinematic MaskZero Spark character scene: ${lead} archetype, ${cast}.`,
    `Signature: ${signature}. Domains: ${domains}.`,
    visual ? `Visual direction: ${visual}` : "",
    "Dreamy Meridian City atmosphere, premium animated film quality, character-centered composition.",
  ]
    .filter(Boolean)
    .join(" ");
}

export function readCharacterFromQuery(): SparkCharacterRecord | null {
  if (typeof window === "undefined") {
    return null;
  }
  const params = new URLSearchParams(window.location.search);
  if (params.get("from") !== "spark") {
    return null;
  }
  return loadSparkCharacter();
}
