export interface SparkSessionUser {
  id: string;
  email: string;
  display_name: string;
  game_role: string;
  is_owner: boolean;
  can_access_admin_panel: boolean;
  can_render_video?: boolean;
  skymotion_access?: boolean;
  spark_plan?: string;
  skymotion_render_credits?: number;
  is_staff?: boolean;
  render_billing_mode?: string;
}

export interface SparkSession {
  ok: boolean;
  logged_in: boolean;
  user: SparkSessionUser | null;
  email?: string;
  display_name?: string;
  can_render_video?: boolean;
  skymotion_access?: boolean;
  spark_plan?: string;
  skymotion_render_credits?: number;
  is_staff?: boolean;
}

export interface SparkBillingCheckout {
  ok: boolean;
  mode?: string;
  url?: string;
  message?: string;
}

const AUTH_URL = "/api/spark-auth.php?action=session";
const BILLING_CHECKOUT_URL = "/api/spark-billing.php?action=checkout";

export async function fetchSparkSession(): Promise<SparkSession> {
  const response = await fetch(AUTH_URL, {
    credentials: "include",
    cache: "no-store",
  });
  const payload = (await response.json()) as SparkSession;
  return payload;
}

export async function startSkyMotionCheckout(): Promise<SparkBillingCheckout> {
  const response = await fetch(BILLING_CHECKOUT_URL, {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "checkout" }),
  });
  return (await response.json()) as SparkBillingCheckout;
}

export function sparkLoginUrl(redirectTo = "/"): string {
  return `/spark-login/?redirect_to=${encodeURIComponent(redirectTo)}`;
}

export function sparkSignupUrl(): string {
  return "/spark-signup/";
}

export function canGenerateVideo(session: SparkSession | null): boolean {
  if (!session?.logged_in) {
    return false;
  }
  if (session.can_render_video || session.skymotion_access) {
    return true;
  }
  return Boolean(session.user?.can_render_video || session.user?.skymotion_access);
}

export function isStaffSession(session: SparkSession | null): boolean {
  return Boolean(session?.is_staff || session?.user?.is_staff || session?.user?.is_owner);
}
