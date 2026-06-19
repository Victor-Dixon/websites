export type AnimationStyle =
  | "cinematic"
  | "anime"
  | "cartoon"
  | "realistic"
  | "fantasy"
  | "3d";

export type AnimationDuration = "5s" | "10s" | "30s" | "60s";

export interface PromptEnhancementInput {
  prompt: string;
  style?: AnimationStyle;
  duration?: AnimationDuration;
}

export interface StoryScene {
  id: string;
  title: string;
  prompt: string;
  camera: string;
  audio: string;
  duration: AnimationDuration;
}

const styleLanguage: Record<AnimationStyle, string> = {
  cinematic: "cinematic lighting, anamorphic lens flares, rich production design",
  anime: "expressive anime direction, dynamic speed lines, vibrant character acting",
  cartoon: "playful cartoon motion, squash-and-stretch timing, bold readable silhouettes",
  realistic: "photoreal rendering, natural motion physics, nuanced facial performance",
  fantasy: "mythic fantasy atmosphere, magical particles, luminous environmental storytelling",
  "3d": "premium 3D animation, polished materials, physically based lighting",
};

export function enhancePrompt({
  prompt,
  style = "cinematic",
  duration = "10s",
}: PromptEnhancementInput): string {
  const cleanPrompt = prompt.trim() || "a dreamlike hero discovering a floating city above the clouds";
  const styleCue = styleLanguage[style];

  return [
    `A breathtaking ${duration} ${style} animated sequence of ${cleanPrompt}.`,
    styleCue,
    "Layered foreground, midground, and background motion with intentional camera choreography.",
    "Ultra detailed atmosphere, expressive characters, premium VFX, sound-design-ready beats, and a clear emotional arc.",
  ].join(" ");
}

export function generateStoryScenes(idea: string): StoryScene[] {
  const storyIdea = idea.trim() || "a young inventor follows a comet into a city built inside the sky";

  return [
    {
      id: "scene-01",
      title: "Inciting Vision",
      prompt: enhancePrompt({
        prompt: `${storyIdea}, opening with a mysterious signal illuminating the main character's face`,
        style: "cinematic",
        duration: "10s",
      }),
      camera: "Slow dolly-in, subtle handheld wonder, glowing parallax background.",
      audio: "Soft synth pad, distant thunder swell, whispered character reaction.",
      duration: "10s",
    },
    {
      id: "scene-02",
      title: "World Reveal",
      prompt: enhancePrompt({
        prompt: `${storyIdea}, revealing the full world with breathtaking scale and layered movement`,
        style: "fantasy",
        duration: "30s",
      }),
      camera: "Epic crane rise into a sweeping orbit with cloud-level speed ramps.",
      audio: "Rising orchestral theme, wind rush, magical particle chimes.",
      duration: "30s",
    },
    {
      id: "scene-03",
      title: "Character Choice",
      prompt: enhancePrompt({
        prompt: `${storyIdea}, the hero deciding to take action while companions react emotionally`,
        style: "realistic",
        duration: "10s",
      }),
      camera: "Intimate over-the-shoulder framing, rack focus to determined eyes.",
      audio: "Dialogue-ready pause, heartbeat percussion, subtle fabric movement.",
      duration: "10s",
    },
    {
      id: "scene-04",
      title: "Final Motion",
      prompt: enhancePrompt({
        prompt: `${storyIdea}, ending with a triumphant launch into the unknown`,
        style: "3d",
        duration: "30s",
      }),
      camera: "Heroic tracking shot into a vortex transition and final logo-safe wide frame.",
      audio: "Full score impact, engine flare, crowd swell, clean outro tail.",
      duration: "30s",
    },
  ];
}
