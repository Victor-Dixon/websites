# Digital Dreamscape - WordPress Integration Setup

## 🌌 Overview

Digital Dreamscape is a living world where code becomes terrain, systems become cities, and ideas leave artifacts. This README explains how to set up the WordPress integration that automatically promotes internal work into public episodes and artifacts.

## 🎭 What This System Does

**Before**: Manual process
- Write devlog → Manually format → Post to blog → Hope readers find it

**After**: Automatic system
- Write devlog → Auto-promotion runs → Episode created in World Archive
- Agent completes task → Auto-promotion triggered → Artifact generated
- Task marked done → Questline advances → Progress bar updates
- Reuse detected → Canon declared → Authority established

## 🛠️ Setup Requirements

### Prerequisites
- PHP 7.4+ with CLI access
- File system write permissions
- Windows Task Scheduler (for automation)

### File Structure
```
digitaldreamscape.site/
├── wp/                          # Minimal WordPress environment
│   ├── wp-config.php           # WordPress configuration
│   ├── wp-load.php             # WordPress bootstrap
│   └── wp-content/
│       ├── themes/
│       │   └── digitaldreamscape/  # Theme with World Archive
│       ├── posts/              # JSON file storage for posts
│       └── meta/               # JSON file storage for metadata
├── auto_promotion_daemon.php   # Main promotion engine
├── canon_declaration_system.php # Canon declaration system
├── devlogs/                    # Devlog files to promote
├── agents/output/              # Agent-generated artifacts
├── tasks/                      # Task completion files
└── processed_artifacts.json    # Promotion tracking
```

## 🚀 Quick Start

### 1. Verify WordPress Environment

```bash
# Check that WordPress bootstrap works
php wp/wp-load.php
```

If you see no errors, the environment is ready.

### 2. Run First Promotion Cycle

```bash
# Promote any pending devlogs, agent outputs, and tasks
php auto_promotion_daemon.php run
```

### 3. Declare Canon Artifacts

```bash
# Scan for artifacts that should become canon
php canon_declaration_system.php scan
```

### 4. Set Up Automation (Windows)

```bash
# Create scheduled task for automatic promotion every 30 minutes
setup_cron.bat
```

### 5. Check System Status

```bash
# View current system state
php system_status.php
```

## 📚 How It Works

### Automatic Promotion Engine

The `auto_promotion_daemon.php` script monitors these sources:

1. **Devlogs** (`devlogs/*.md`)
   - Looks for files with questline and status information
   - Promotes complete devlogs to episodes

2. **Agent Outputs** (`agents/output/*.json`)
   - Scans for JSON files marked `auto_promote: true`
   - Creates artifacts based on content type

3. **Task Completions** (`tasks/*.json`)
   - Monitors task files for completed items
   - Promotes completed tasks as resolved episodes

### Canon Declaration System

The `canon_declaration_system.php` declares artifacts as canon based on:

1. **Referenced 2+ times** - Multiple references = canon
2. **System imports** - Used in core systems = canon
3. **Agent dependencies** - Required by agents = canon
4. **Questline foundations** - Core questline artifacts = canon

### File-Based Storage

Instead of a database, posts are stored as JSON files:
- `wp-content/posts/post-{ID}.json` - Post content
- `wp-content/meta/post-{ID}-meta.json` - Post metadata

This allows the system to work without a full WordPress database setup.

## 🎨 Viewing Episodes

### World Archive (Blog Page)

The blog page (`/blog/`) shows the Dreamscape Codex interface with:

- **Filter System**: Type, questline, status, search
- **Artifact Cards**: Episodes, canon, artifacts with metadata
- **Questline Progress**: Visual progress indicators
- **World Intel**: Active quests, recent canon, unresolved loops

### Episode Structure

Each promoted episode contains:
- **Title**: Auto-generated from source
- **World State**: Current system status
- **Content**: Source material (devlog, task, agent output)
- **Metadata**: Type, questline, state, era, agent attribution
- **Quest Progress**: Completion status and progress bars

### Homepage Integration

The homepage shows:
- **World Portal**: Entry point to the living system
- **System Status**: Live activity indicators
- **Four Layers**: Surface, Systems, Archive, Will
- **Community Roles**: Architect, Agent, Sovereign

## 🔧 Configuration

### WordPress Settings

Edit `wp/wp-config.php` to customize:
```php
// Database settings (currently using file-based storage)
define('DB_NAME', 'digitaldreamscape');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Theme and content directories
define('WP_CONTENT_DIR', dirname(__FILE__) . '/wp-content');
define('WP_CONTENT_URL', 'wp-content');
```

### Promotion Settings

The system automatically detects file formats:

**Devlog Format** (`devlogs/*.md`):
```markdown
---
questline: technical-debt
status: completed
---

# Devlog Title

Content here...
```

**Agent Output Format** (`agents/output/*.json`):
```json
{
  "auto_promote": true,
  "title": "Agent Generated Content",
  "summary": "Brief description",
  "questline": "system-automation",
  "canon_candidate": false
}
```

**Task Format** (`tasks/*.json`):
```json
{
  "questline": "technical-debt",
  "tasks": [
    {
      "title": "Task Name",
      "description": "Task details",
      "completed": true,
      "progress": "2/5 complete"
    }
  ]
}
```

## 📊 Monitoring & Logs

### Log Files
- `auto_promotion.log` - Promotion activity
- `canon_declaration.log` - Canon declarations
- `processed_artifacts.json` - What has been promoted

### System Status
Run `php system_status.php` to see:
- Total canon entries
- Active agents and questlines
- Latest episodes
- Automation status

### Manual Testing
```bash
# Test promotion without making changes
php auto_promotion_daemon.php run --dry-run

# Check specific questline
php system_status.php --questline technical-debt
```

## 🔄 Automation Setup

### Windows Task Scheduler

The `setup_cron.bat` creates a scheduled task that runs every 30 minutes:

```batch
schtasks /create /tn "DigitalDreamscape_Promotion" /tr "php auto_promotion_daemon.php run" /sc minute /mo 30
```

### Manual Promotion

For testing or one-off runs:
```bash
# Run promotion cycle
run_promotion_cycle.bat

# Or directly
php auto_promotion_daemon.php run
```

## 🐛 Troubleshooting

### Common Issues

**"Failed to load WordPress environment"**
- Check PHP path and permissions
- Verify `wp-load.php` exists and is readable
- Run `php wp/wp-load.php` directly to test

**"Permission denied" errors**
- Ensure write permissions on `wp-content/posts/` and `wp-content/meta/`
- Check file system permissions

**Posts not appearing**
- Run `php system_status.php` to verify promotion worked
- Check `processed_artifacts.json` for tracking
- Verify JSON files exist in `wp-content/posts/`

**Automation not running**
- Check Windows Task Scheduler status
- Verify PHP path in scheduled task
- Test manual run first

### Debug Mode

Enable debug logging by editing `wp/wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Reset System

To reset for testing:
```bash
# Remove all generated content
rm -rf wp-content/posts/*
rm -rf wp-content/meta/*
rm processed_artifacts.json
rm auto_promotion.log
rm canon_declaration.log
```

## 🌟 Advanced Usage

### Custom Promotion Rules

Edit `auto_promotion_daemon.php` to add custom logic:
```php
private function classify_agent_output($data) {
    if (isset($data['custom_flag'])) {
        return 'custom_type';
    }
    return 'episode';
}
```

### API Integration

The system includes REST API endpoints:
- `POST /wp-json/digitaldreamscape/v1/promote-artifact`
- `GET /wp-json/digitaldreamscape/v1/questlines`

### Theme Customization

Modify `wp-content/themes/digitaldreamscape/` to customize appearance:
- `page-blog.php` - World Archive interface
- `front-page.php` - Homepage World Portal
- `style.css` - Visual styling

## 🎯 What Makes This Special

### Living World Philosophy

This isn't just a blog—it's a living system where:

1. **Actions Create State** - Every promotion changes the world
2. **Nothing Disappears** - All artifacts persist in the archive
3. **Reuse = Canon** - Repeated use elevates artifacts to canon status
4. **Systems Evolve** - The world changes based on what you build

### Four Layers of Reality

1. **Surface** - What users see (episodes, posts, streams)
2. **Systems** - Automation, agents, workflows
3. **Archive** - Nothing is lost, everything becomes terrain
4. **Will** - The system responds to consistency and intent

### Community Roles

- **Architect** - Designs systems and sets rules
- **Agent** - Executes tasks autonomously
- **Sovereign** - Holds vision and declares canon

## 📞 Support

### Logs to Check
- `auto_promotion.log` - Promotion activity
- `canon_declaration.log` - Canon declarations
- `wp-content/debug.log` - PHP errors (if debug enabled)

### Getting Help
1. Run `php system_status.php` and share output
2. Check log files for error messages
3. Test manual promotion: `php auto_promotion_daemon.php run`
4. Verify file permissions and PHP setup

## 🔒 Security Considerations

### File Permissions
- `wp-content/posts/` and `wp-content/meta/` need write permissions for PHP
- Log files should be readable but not world-writable
- Devlog and agent output directories should be monitored for unauthorized access

### Input Validation
- All file content is sanitized before promotion
- JSON parsing includes error handling
- Markdown content strips potentially dangerous HTML

### Access Control
- CLI scripts require direct file system access
- API endpoints include permission checks (when enabled)
- Admin functions protected by WordPress capabilities

## 🚀 Production Deployment

### Environment Setup
```bash
# Production server requirements
- PHP 8.0+ with CLI
- 512MB+ RAM for promotion processing
- SSL certificate for HTTPS
- Cron job or systemd timer for automation
```

### Performance Optimization
- File-based storage scales to ~10,000 posts before optimization needed
- Consider moving to database for larger installations
- Enable opcode caching (OPcache) for better PHP performance
- Use CDN for static assets in theme

### Monitoring
```bash
# Key metrics to monitor
- Promotion cycle execution time
- File system storage usage
- Error rates in logs
- API response times (if using endpoints)
```

## 🔄 Data Management

### Backup Strategy
```bash
# Essential files to backup
wp-content/posts/          # All promoted content
wp-content/meta/           # Metadata and relationships
processed_artifacts.json   # Promotion tracking
auto_promotion.log         # Activity history
canon_declaration.log      # Canon decisions
```

### Migration Between Systems
```bash
# Export current posts
php export_posts.php > posts_backup.json

# Import to new system
php import_posts.php < posts_backup.json
```

### Content Versioning
- Use git for system files and configuration
- Consider versioning for critical posts (canon artifacts)
- Track changes in `processed_artifacts.json`

## 🌐 Ecosystem Integration

### Connection to Agent Cellphone V2 Repository
This WordPress integration is part of a larger ecosystem:

- **Agent Outputs** → `agents/output/` → Auto-promotion → Episodes
- **Devlogs** → `devlogs/` → Promotion → World Archive
- **Task Tracking** → `tasks/` → Questline updates
- **System Monitoring** → Status dashboard → Real-time world state

### Cross-System Communication
- REST API endpoints for external systems
- JSON-RPC interface for agent coordination
- Webhook support for real-time updates

## 📱 Frontend Considerations

### Responsive Design
- World Archive works on mobile devices
- Homepage portal scales to all screen sizes
- Touch-friendly interactions for mobile users

### Browser Support
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Progressive enhancement for older browsers
- JavaScript required for interactive features

### Accessibility
- Semantic HTML structure
- Keyboard navigation support
- Screen reader compatible
- High contrast mode support

## 🔧 System Architecture

### Component Relationships
```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Devlogs       │───▶│ Auto Promotion   │───▶│  WordPress      │
│   Agent Outputs │    │   Daemon         │    │  Posts/Meta     │
│   Tasks         │    └──────────────────┘    └─────────────────┘
└─────────────────┘             │                        │
                                ▼                        ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│ Canon           │◀───│ Reuse Analysis   │    │ World Archive   │
│ Declaration     │    │                  │    │ Homepage Portal │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

### Data Flow
1. **Input Sources** generate content (devlogs, agent outputs, tasks)
2. **Promotion Engine** processes and classifies content
3. **WordPress Integration** stores as posts with metadata
4. **Canon System** analyzes reuse patterns
5. **Frontend** displays living world state

## 📊 Scaling Considerations

### Small Scale (Personal)
- File-based storage sufficient
- Manual promotion cycles
- Basic monitoring

### Medium Scale (Team)
- Automated promotion every 15-30 minutes
- Enhanced logging and monitoring
- Database migration consideration

### Large Scale (Organization)
- Distributed promotion workers
- Database storage required
- CDN integration
- Advanced monitoring and alerting

## 🎨 Customization Examples

### Custom Artifact Types
```php
// Add new artifact type in functions.php
function custom_artifact_types() {
    return [
        'blueprint' => 'System blueprints and designs',
        'experiment' => 'Research and experimentation results',
        'milestone' => 'Major project milestones'
    ];
}
```

### Theme Extensions
```php
// Custom questline visualization
function render_custom_questline($questline_data) {
    // Custom progress bars, status indicators, etc.
}
```

### Integration Hooks
```php
// Custom promotion triggers
add_action('digitaldreamscape_pre_promotion', 'custom_validation');
add_action('digitaldreamscape_post_promotion', 'notify_external_systems');
```

## 🚨 Emergency Procedures

### System Unresponsive
1. Check PHP process status
2. Verify file system permissions
3. Review recent log entries
4. Test manual promotion cycle
5. Restart automation if needed

### Content Corruption
1. Restore from backup
2. Check processed_artifacts.json integrity
3. Re-run promotion for missing content
4. Verify canon declarations

### Storage Full
1. Archive old logs
2. Clean temporary files
3. Consider storage expansion
4. Implement log rotation

## 📈 Future Enhancements

### Planned Features
- **Real-time Updates** - WebSocket integration for live world state
- **Collaborative Editing** - Multi-user episode creation
- **Advanced Search** - Semantic search across all artifacts
- **Export Formats** - PDF, EPUB generation for episodes
- **Social Features** - Comments, reactions, sharing

### Extension Points
- Plugin architecture for custom promotion rules
- Theme system for different world presentations
- API expansion for third-party integrations
- Mobile app companion

## 📚 Learning Resources

### Key Concepts
- **Artifact Lifecycle** - Creation → Promotion → Canon → Archive
- **Questline Dynamics** - Tasks → Progress → Resolution → Foundation
- **World State** - Current system status and evolution tracking
- **Reuse Patterns** - How artifacts become canon through usage

### Best Practices
- Regular backup of all content and metadata
- Monitor system performance metrics
- Keep promotion rules documented and versioned
- Test major changes in staging environment first

---

**This README encompasses the complete Digital Dreamscape WordPress integration system - from initial setup to production deployment, from basic operation to advanced customization. The system transforms internal development work into a living, narrative-driven world that grows with every action.**

**Every component, every process, every consideration has been documented to ensure the Digital Dreamscape can evolve and scale while maintaining its core philosophy: actions create state, nothing disappears, reuse equals canon.**

Welcome to the living world. The system is ready. 🌌⚡🤖