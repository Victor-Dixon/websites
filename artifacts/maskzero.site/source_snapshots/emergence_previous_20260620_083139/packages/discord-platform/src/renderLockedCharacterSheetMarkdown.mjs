export function renderLockedCharacterSheetMarkdown(locked) {
  const sheet = locked.character || {};
  const domains = Array.isArray(sheet.domains) ? sheet.domains : [];
  const powers = Array.isArray(sheet.powers) ? sheet.powers : [];
  const threatTags = Array.isArray(sheet.threatTags) ? sheet.threatTags : [];

  return [
    `# ${sheet.name || 'Unknown Character'}`,
    ``,
    `**Discord User:** ${locked.discordUsername || 'Unknown'}`,
    `**Locked Role:** ${locked.activeRole || 'player'}`,
    `**Percentile:** ${sheet.percentile ?? 'Unknown'}`,
    `**Threat Classification:** ${sheet.threatClassification || sheet.threat_class || 'Unknown'}`,
    ``,
    `## Domains`,
    ...(domains.length ? domains.map((d) => `- **${d.name}** — Tier ${d.tier} (${d.score})`) : ['- None']),
    ``,
    `## Powers`,
    ...(powers.length ? powers.map((p) => `- **${p.name}** — Tier ${p.tier} / ${p.classification || 'primary'} / ${p.domain || 'unknown'}`) : ['- None']),
    ``,
    `## Threat Tags`,
    ...(threatTags.length ? threatTags.map((t) => `- ${t}`) : ['- None']),
    ``,
    `This sheet is locked and must remain the source of truth.`
  ].join('\n');
}
