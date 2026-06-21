import { NextResponse } from "next/server";
import { generateStoryScenes } from "@/lib/prompt-engine";

interface StoryPayload {
  idea?: string;
}

export async function POST(request: Request) {
  const payload = (await request.json()) as StoryPayload;

  return NextResponse.json({
    title: "DreamMotion Short Film Blueprint",
    scenes: generateStoryScenes(payload.idea ?? ""),
  });
}
