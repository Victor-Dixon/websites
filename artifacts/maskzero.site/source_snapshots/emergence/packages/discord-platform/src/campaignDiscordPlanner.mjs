import {
  DEFAULT_DM_PERMISSION_POLICY,
  evaluateDmActionPlan
} from "./dmPermissionPolicy.mjs";

function slugify(value) {
  return String(value)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "")
    .slice(0, 80);
}

export function buildCampaignDiscordPlan({
  campaign,
  policy = DEFAULT_DM_PERMISSION_POLICY
}) {
  if (!campaign || !campaign.campaignId || !campaign.title) {
    throw new Error("campaign with campaignId and title is required");
  }

  const base = slugify(campaign.title);
  const partyNames = campaign.party.map((member) => slugify(member.activeRole));

  const actions = [
    {
      type: "create_campaign_category",
      name: `🎲 ${campaign.title}`,
      reason: "Campaign root category"
    },
    {
      type: "create_text_channel",
      name: `${base}-table`,
      parent: campaign.campaignId,
      reason: "Main table chat"
    },
    {
      type: "create_text_channel",
      name: `${base}-character-sheets`,
      parent: campaign.campaignId,
      reason: "Locked character sheet display"
    },
    {
      type: "create_text_channel",
      name: `${base}-battle-log`,
      parent: campaign.campaignId,
      reason: "Battle packets and adjudication logs"
    },
    {
      type: "create_text_channel",
      name: `${base}-recaps`,
      parent: campaign.campaignId,
      reason: "Session recap feed"
    },
    {
      type: "create_private_gm_channel",
      name: `${base}-gm-notes`,
      parent: campaign.campaignId,
      reason: "Private AI DM planning notes"
    },
    {
      type: "create_voice_channel",
      name: `${base}-voice`,
      parent: campaign.campaignId,
      reason: "Live session voice"
    },
    {
      type: "pin_message",
      channel: `${base}-character-sheets`,
      reason: "Pin locked sheet truth"
    }
  ];

  for (const name of partyNames) {
    actions.push({
      type: "create_thread",
      name: `${name}-sheet-thread`,
      parent: `${base}-character-sheets`,
      reason: "Per-character discussion and sheet history"
    });
  }

  if (actions.length > policy.limits.maxChannelsPerCampaign + policy.limits.maxThreadsPerSession) {
    actions.push({
      type: "requires_operator_review",
      reason: "Plan exceeds campaign structure limit"
    });
  }

  const evaluated = evaluateDmActionPlan(actions, policy);

  return {
    schemaVersion: 1,
    type: "campaign_discord_platform_plan",
    campaignId: campaign.campaignId,
    title: campaign.title,
    mode: "dry_run",
    policy,
    actions: evaluated,
    summary: {
      total: evaluated.length,
      allowed: evaluated.filter((entry) => entry.decision.allowed).length,
      blocked: evaluated.filter((entry) => !entry.decision.allowed).length
    }
  };
}

export function assertPlanHasNoBlockedActions(plan) {
  const blocked = plan.actions.filter((entry) => !entry.decision.allowed);
  if (blocked.length > 0) {
    throw new Error(`Discord platform plan has blocked actions: ${blocked.map((entry) => entry.action.type).join(", ")}`);
  }

  return true;
}
