export function tierNumber(tier) {
  const match = String(tier).match(/T?([1-5])/i);
  if (!match) throw new Error(`Invalid tier: ${tier}`);
  return Number(match[1]);
}

export function tierInteraction(attackerTier, defenderTier) {
  const gap = tierNumber(attackerTier) - tierNumber(defenderTier);

  if (gap >= 2) return { effect: "overwhelming", canWound: true, modifier: 0.2 };
  if (gap === 1) return { effect: "strong", canWound: true, modifier: 0.12 };
  if (gap === 0) return { effect: "matched", canWound: true, modifier: 0 };
  if (gap === -1) return { effect: "limited", canWound: true, modifier: -0.12 };
  return { effect: "ineffective", canWound: false, modifier: -0.25 };
}
