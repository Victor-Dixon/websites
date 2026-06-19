import { NextResponse } from "next/server";
import { createRenderJob, type RenderJobRequest } from "@/lib/render-queue";

export async function POST(request: Request) {
  const payload = (await request.json()) as RenderJobRequest;

  if (!payload.prompt?.trim()) {
    return NextResponse.json({ error: "A prompt is required to start a render job." }, { status: 400 });
  }

  return NextResponse.json({
    job: createRenderJob(payload),
  });
}
