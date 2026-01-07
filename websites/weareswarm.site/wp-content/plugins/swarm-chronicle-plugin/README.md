# Swarm Chronicle WordPress Plugin

Displays the complete Swarm operating chronicle including cycle accomplishments, project state, and mission logs.

## Features

- **Complete Chronicle Display**: Shows all Swarm activities, accomplishments, and missions
- **Real-time Synchronization**: Syncs data from Swarm systems via API
- **Multiple Views**: Different shortcodes for missions, accomplishments, and project state
- **Admin Dashboard**: WordPress admin interface for configuration and monitoring
- **REST API**: Programmatic access to chronicle data

## Shortcodes

### [swarm_chronicle]
Display the main chronicle overview
```
[swarm_chronicle type="overview" limit="50" agent="all"]
```

### [swarm_missions]
Show active missions and tasks
```
[swarm_missions status="active" limit="20" agent="Agent-1"]
```

### [swarm_accomplishments]
Display recent accomplishments
```
[swarm_accomplishments period="current" limit="25" agent="all"]
```

### [swarm_project_state]
Show project metrics and health
```
[swarm_project_state detail="summary" metrics="true"]
```

## Installation

1. Upload plugin files to `wp-content/plugins/swarm-chronicle-plugin/`
2. Activate the plugin in WordPress admin
3. Configure API settings in Settings → Swarm Chronicle
4. Add shortcodes to your pages/posts

## Configuration

### API Settings
- **API Endpoint**: URL of the Swarm API server
- **API Key**: Authentication key for API access
- **Auto Sync**: Enable automatic data synchronization
- **Sync Interval**: How often to sync data (hourly, daily, etc.)

### Manual Sync
Use the "Sync Now" button in the admin dashboard to manually trigger data synchronization.

## Data Sources

The plugin syncs data from multiple Swarm systems:

- **Master Task Log**: All missions, tasks, and objectives
- **Cycle Accomplishments**: Weekly/monthly achievement reports
- **Project Scanner**: Codebase analysis and metrics
- **Agent Activity**: Individual agent contributions and status

## API Endpoints

### GET /wp-json/swarm-chronicle/v1/data
Retrieve chronicle data
```
GET /wp-json/swarm-chronicle/v1/data?type=missions&limit=20&agent=Agent-1
```

### POST /wp-json/swarm-chronicle/v1/sync
Sync data from external sources (admin only)
```
POST /wp-json/swarm-chronicle/v1/sync
Authorization: Bearer {api_key}
```

## Development

### File Structure
```
swarm-chronicle-plugin/
├── swarm-chronicle-plugin.php      # Main plugin file
├── includes/
│   ├── class-swarm-chronicle.php   # Main plugin class
│   ├── class-chronicle-api.php     # API and data handling
│   └── class-chronicle-admin.php   # Admin interface
├── assets/
│   ├── css/
│   │   └── swarm-chronicle.css     # Frontend styles
│   └── js/
│       └── swarm-chronicle.js      # Frontend scripts
└── README.md                       # This file
```

### Hooks and Filters

#### Actions
- `swarm_chronicle_data_synced` - Fired after successful data sync
- `swarm_chronicle_api_error` - Fired on API errors

#### Filters
- `swarm_chronicle_api_endpoint` - Modify API endpoint URL
- `swarm_chronicle_display_limit` - Modify default display limits
- `swarm_chronicle_allowed_agents` - Filter allowed agents

## Support

For issues and feature requests, please contact the Swarm development team.

## License

MIT License - see LICENSE file for details.
