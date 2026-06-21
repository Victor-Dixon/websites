import { NextResponse } from "next/server";
import { enhancePrompt, type AnimationDuration, type AnimationStyle } from "@/lib/prompt-engine";

interface EnhancePayload {
  prompt?: string;
  style?: AnimationStyle;
  duration?: AnimationDuration;
}

export async function POST(request: Request) {
  const payload = (await request.json()) as EnhancePayload;

  return NextResponse.json({
    enhancedPrompt: enhancePrompt({
      prompt: payload.prompt ?? "",
      style: payload.style,
      duration: payload.duration,
    }),
  });
}
