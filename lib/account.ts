export type AccountPlan = "free" | "premium";
export type SessionSource = "local" | "supabase";

export interface SkyMotionSession {
  email: string;
  plan: AccountPlan;
  source: SessionSource;
  signedInAt: string;
}

export interface StoredApiKey {
  provider: string;
  key: string;
  savedAt: string;
}

export const SKY_SESSION_KEY = "skymotion.session";
export const SKY_API_KEY = "skymotion.freeApiKey";

export const premiumLimits = {
  hourly: 8,
  daily: 30,
} as const;

export const freeLimits = {
  daily: 100,
} as const;

export function maskApiKey(value: string): string {
  const cleanValue = value.trim();
  if (cleanValue.length <= 8) {
    return "configured";
  }

  return `${cleanValue.slice(0, 4)}...${cleanValue.slice(-4)}`;
}

export function readLocalSession(): SkyMotionSession | null {
  if (typeof window === "undefined") {
    return null;
  }

  const value = window.localStorage.getItem(SKY_SESSION_KEY);
  if (!value) {
    return null;
  }

  try {
    return JSON.parse(value) as SkyMotionSession;
  } catch {
    window.localStorage.removeItem(SKY_SESSION_KEY);
    return null;
  }
}

export function writeLocalSession(session: SkyMotionSession) {
  window.localStorage.setItem(SKY_SESSION_KEY, JSON.stringify(session));
}

export function clearLocalAccount() {
  window.localStorage.removeItem(SKY_SESSION_KEY);
  window.localStorage.removeItem(SKY_API_KEY);
}

export function readStoredApiKey(): StoredApiKey | null {
  if (typeof window === "undefined") {
    return null;
  }

  const value = window.localStorage.getItem(SKY_API_KEY);
  if (!value) {
    return null;
  }

  try {
    return JSON.parse(value) as StoredApiKey;
  } catch {
    window.localStorage.removeItem(SKY_API_KEY);
    return null;
  }
}

export function writeStoredApiKey(apiKey: StoredApiKey) {
  window.localStorage.setItem(SKY_API_KEY, JSON.stringify(apiKey));
}
