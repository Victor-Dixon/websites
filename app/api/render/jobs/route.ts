import { NextResponse } from "next/server";
import { premiumLimits, type AccountPlan } from "@/lib/account";
import { createRenderJob, type RenderJobRequest } from "@/lib/render-queue";

interface RateBucket {
  hourly: number[];
  daily: number[];
}

const rateLimitState = globalThis as typeof globalThis & {
  __skymotionPremiumRateLimit?: Map<string, RateBucket>;
};

const premiumRateLimits = rateLimitState.__skymotionPremiumRateLimit ?? new Map<string, RateBucket>();
rateLimitState.__skymotionPremiumRateLimit = premiumRateLimits;

function pruneWindow(values: number[], windowMs: number, now: number): number[] {
  return values.filter((value) => now - value < windowMs);
}

function applyPremiumLimit(accountId: string) {
  const now = Date.now();
  const hourMs = 60 * 60 * 1000;
  const dayMs = 24 * hourMs;
  const bucket = premiumRateLimits.get(accountId) ?? { hourly: [], daily: [] };
  bucket.hourly = pruneWindow(bucket.hourly, hourMs, now);
  bucket.daily = pruneWindow(bucket.daily, dayMs, now);

  if (bucket.hourly.length >= premiumLimits.hourly) {
    return {
      allowed: false,
      remaining: 0,
      reset: new Date(bucket.hourly[0] + hourMs).toISOString(),
      error: `Premium managed renders are limited to ${premiumLimits.hourly} per hour.`,
    };
  }

  if (bucket.daily.length >= premiumLimits.daily) {
    return {
      allowed: false,
      remaining: 0,
      reset: new Date(bucket.daily[0] + dayMs).toISOString(),
      error: `Premium managed renders are limited to ${premiumLimits.daily} per day.`,
    };
  }

  bucket.hourly.push(now);
  bucket.daily.push(now);
  premiumRateLimits.set(accountId, bucket);

  return {
    allowed: true,
    remaining: Math.min(premiumLimits.hourly - bucket.hourly.length, premiumLimits.daily - bucket.daily.length),
    reset: new Date(now + hourMs).toISOString(),
  };
}

export async function POST(request: Request) {
  const payload = (await request.json()) as RenderJobRequest;
  const accountId = request.headers.get("x-skymotion-account-id")?.trim();
  const plan = (request.headers.get("x-skymotion-plan") ?? "free").trim() as AccountPlan;
  const userApiKey = request.headers.get("x-skymotion-user-api-key")?.trim();

  if (!payload.prompt?.trim()) {
    return NextResponse.json({ error: "A prompt is required to start a render job." }, { status: 400 });
  }

  if (!accountId) {
    return NextResponse.json({ error: "Log in before starting a render job." }, { status: 401 });
  }

  if (plan !== "free" && plan !== "premium") {
    return NextResponse.json({ error: "Unknown account plan." }, { status: 400 });
  }

  if (plan === "free" && !userApiKey) {
    return NextResponse.json({ error: "Free accounts must provide their own API key before rendering." }, { status: 402 });
  }

  const premiumQuota = plan === "premium" ? applyPremiumLimit(accountId) : null;
  if (premiumQuota && !premiumQuota.allowed) {
    return NextResponse.json({ error: premiumQuota.error, reset: premiumQuota.reset }, { status: 429 });
  }

  return NextResponse.json({
    job: createRenderJob(payload),
    quota: {
      plan,
      remaining: premiumQuota?.remaining ?? 99,
      reset: premiumQuota?.reset ?? "provider billed through user API key",
    },
  });
}
