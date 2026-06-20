#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== USE PRIVATE THREAD QUIZ BUTTONS =="

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

s = s.replace(
"GatewayIntentBits,",
"ChannelType,\n  GatewayIntentBits,"
)

old = """    if (interaction.commandName === 'generate') {
      const session = getSession(interaction);
      saveSession(session);
      await interaction.reply({
        embeds: [buildQuizEmbed(session)],
        components: buildRows(session),
        flags: MessageFlags.Ephemeral
      });
      return;
    }"""

new = """    if (interaction.commandName === 'generate') {
      const session = getSession(interaction);

      if (!session.threadId) {
        const thread = await interaction.channel.threads.create({
          name: `spark-${interaction.user.username}`.slice(0, 90),
          type: ChannelType.PrivateThread,
          invitable: false,
          reason: 'Spark Protocol private classification'
        });

        await thread.members.add(interaction.user.id);
        session.threadId = thread.id;

        const quizMessage = await thread.send({
          content: `<@${interaction.user.id}> Spark Protocol classification started.`,
          embeds: [buildQuizEmbed(session)],
          components: buildRows(session)
        });

        session.messageId = quizMessage.id;
        saveSession(session);

        await interaction.reply({
          content: `Classification thread opened: <#${thread.id}>`,
          flags: MessageFlags.Ephemeral
        });
        return;
      }

      const thread = await client.channels.fetch(session.threadId);
      const quizMessage = await thread.messages.fetch(session.messageId);

      await quizMessage.edit({
        embeds: [buildQuizEmbed(session, 'View refreshed from saved state.')],
        components: buildRows(session)
      });

      saveSession(session);

      await interaction.reply({
        content: `Classification thread resumed: <#${thread.id}>`,
        flags: MessageFlags.Ephemeral
      });
      return;
    }"""

if old not in s:
    raise SystemExit("GENERATE_BLOCK_NOT_FOUND")

s = s.replace(old, new)

s = s.replace(
"""await interaction.editReply({
    embeds: [buildSheetEmbed(interaction.user, sheet, aegisPacket.interpretation)],
    components: []
  });""",
"""await interaction.editReply({
    embeds: [buildSheetEmbed(interaction.user, sheet, aegisPacket.interpretation)],
    components: []
  });"""
)

p.write_text(s)
print("PRIVATE_THREAD_GENERATE_PATCH=PASS")
PY

node --check apps/discord-bot/src/index.js
npm run test:mobile

echo "USE_PRIVATE_THREAD_QUIZ_BUTTONS=PASS"
