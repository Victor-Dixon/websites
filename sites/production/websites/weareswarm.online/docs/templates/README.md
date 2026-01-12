# Blogging Content Templates

**Purpose**: Content templates for automated blog posting across WordPress sites

## Templates

### trading_education.md
- **Site**: freerideinvestor.com
- **Purpose**: Trading education and market analysis
- **Use Case**: Educational content, trading strategies, market insights

### swarm_update.md
- **Sites**: weareswarm.online, weareswarm.site
- **Purpose**: Swarm system updates and technical announcements
- **Use Case**: System updates, agent achievements, architecture changes

### plugin_changelog.md
- **Site**: tradingrobotplug.com
- **Purpose**: Plugin updates and changelog
- **Use Case**: Version releases, feature announcements, bug fixes

### personal_update.md
- **Site**: prismblossom.online
- **Purpose**: Personal updates and memories
- **Use Case**: Personal milestones, event announcements, memories

### music_release.md
- **Site**: southwestsecret.com
- **Purpose**: Music releases and event announcements
- **Use Case**: New releases, DJ sets, event information

## Usage

Templates can be used with the unified blogging automation tool:

```bash
python tools/unified_blogging_automation.py \
  --site freerideinvestor \
  --title "Trading Strategy: [Title]" \
  --content templates/blogging/trading_education.md \
  --purpose trading_education \
  --status draft
```

## Template Variables

Templates support variable substitution:
- `{title}` - Post title
- `{content}` - Main content
- `{date}` - Publication date
- `{author}` - Author name
- `{version}` - Version number (for changelogs)
- `{agent}` - Agent name (for swarm updates)

## Customization

Templates can be customized per site while maintaining consistent structure. The content adaptation engine will automatically apply appropriate categories and tags based on site purpose.


