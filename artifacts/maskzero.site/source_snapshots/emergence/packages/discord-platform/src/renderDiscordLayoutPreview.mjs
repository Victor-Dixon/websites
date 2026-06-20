export function renderDiscordLayoutPreview(plan) {
  if (!plan || !Array.isArray(plan.actions)) {
    throw new Error("A Discord platform plan with actions[] is required");
  }

  const category = plan.actions.find(
    (entry) => entry.action.type === "create_campaign_category"
  )?.action;

  const lines = [];

  lines.push(`# Discord Campaign Layout Preview`);
  lines.push("");
  lines.push(`Campaign: ${plan.title}`);
  lines.push(`Campaign ID: ${plan.campaignId}`);
  lines.push(`Mode: ${plan.mode}`);
  lines.push("");

  lines.push("## Layout");
  lines.push("");

  if (category) {
    const categoryName = category.name.replace(/^🎲\s*/, "");
    lines.push(`🎲 **${categoryName}**`);
  } else {
    lines.push("🎲 **Campaign Category**");
  }

  const textChannels = plan.actions.filter(
    (entry) => entry.action.type === "create_text_channel"
  );

  const privateChannels = plan.actions.filter(
    (entry) => entry.action.type === "create_private_gm_channel"
  );

  const voiceChannels = plan.actions.filter(
    (entry) => entry.action.type === "create_voice_channel"
  );

  const threads = plan.actions.filter(
    (entry) => entry.action.type === "create_thread"
  );

  const pins = plan.actions.filter(
    (entry) => entry.action.type === "pin_message"
  );

  for (const entry of textChannels) {
    lines.push(`├── #${entry.action.name} — ${entry.action.reason}`);

    const childThreads = threads.filter(
      (thread) => thread.action.parent === entry.action.name
    );

    for (const thread of childThreads) {
      lines.push(`│   └── 🧵 ${thread.action.name} — ${thread.action.reason}`);
    }

    const channelPins = pins.filter(
      (pin) => pin.action.channel === entry.action.name
    );

    for (const pin of channelPins) {
      lines.push(`│   └── 📌 pinned truth — ${pin.action.reason}`);
    }
  }

  for (const entry of privateChannels) {
    lines.push(`├── 🔒 #${entry.action.name} — ${entry.action.reason}`);
  }

  for (const entry of voiceChannels) {
    lines.push(`└── 🔊 ${entry.action.name} — ${entry.action.reason}`);
  }

  lines.push("");
  lines.push("## Permission Summary");
  lines.push("");
  lines.push(`- Total planned actions: ${plan.summary.total}`);
  lines.push(`- Allowed: ${plan.summary.allowed}`);
  lines.push(`- Blocked: ${plan.summary.blocked}`);
  lines.push("");

  lines.push("## Safety Rules");
  lines.push("");
  lines.push("- This preview does not mutate Discord.");
  lines.push("- Live execution must go through a separate executor gate.");
  lines.push("- Destructive actions require operator approval.");
  lines.push("- Locked character sheets remain the source of truth.");
  lines.push("- AI DM cannot invent powers, tiers, domains, or threat tags.");

  return lines.join("\n") + "\n";
}
