export const DEFAULT_DM_PERMISSION_POLICY = {
  allowedActions: [
    "create_campaign_category",
    "create_text_channel",
    "create_voice_channel",
    "create_private_gm_channel",
    "create_thread",
    "pin_message",
    "post_recap",
    "archive_campaign_channel"
  ],
  approvalRequiredActions: [
    "delete_channel",
    "modify_server_role",
    "mention_everyone",
    "force_character_reroll",
    "expose_private_gm_notes"
  ],
  limits: {
    maxChannelsPerCampaign: 12,
    maxThreadsPerSession: 8,
    maxMentionsPerMessage: 5
  }
};

export function evaluateDmAction(action, policy = DEFAULT_DM_PERMISSION_POLICY) {
  if (!action || !action.type) {
    return {
      allowed: false,
      status: "rejected",
      reason: "Action type is required"
    };
  }

  if (policy.approvalRequiredActions.includes(action.type)) {
    return {
      allowed: false,
      status: "approval_required",
      reason: `Action requires operator approval: ${action.type}`
    };
  }

  if (!policy.allowedActions.includes(action.type)) {
    return {
      allowed: false,
      status: "rejected",
      reason: `Action is not in AI DM permission policy: ${action.type}`
    };
  }

  if (action.type === "mention_everyone") {
    return {
      allowed: false,
      status: "approval_required",
      reason: "Mass mentions require approval"
    };
  }

  return {
    allowed: true,
    status: "allowed",
    reason: "Action allowed by AI DM permission policy"
  };
}

export function evaluateDmActionPlan(actions, policy = DEFAULT_DM_PERMISSION_POLICY) {
  return actions.map((action) => ({
    action,
    decision: evaluateDmAction(action, policy)
  }));
}
