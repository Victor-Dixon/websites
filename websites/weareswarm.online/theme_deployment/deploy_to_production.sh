#!/bin/bash
# WeAreSwarm Theme Deployment Script
# Deploys swarm intelligence theme to production WordPress installation

echo "🐝 WeAreSwarm Theme Deployment - Swarm Intelligence Activation"
echo "=========================================================="

# Configuration - MODIFY THESE PATHS FOR YOUR ENVIRONMENT
PRODUCTION_WP_PATH="/var/www/weareswarm.online"  # Path to live WordPress installation
BACKUP_SUFFIX="_backup_$(date +%Y%m%d_%H%M%S)"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Production WordPress path: $PRODUCTION_WP_PATH"
echo "Deployment source: $SCRIPT_DIR"

# Verify source files exist
echo "📋 Verifying deployment package..."
if [ ! -f "$SCRIPT_DIR/weareswarm/style.css" ]; then
    echo "❌ ERROR: style.css not found in deployment package"
    exit 1
fi

if [ ! -f "$SCRIPT_DIR/weareswarm/functions.php" ]; then
    echo "❌ ERROR: functions.php not found in deployment package"
    exit 1
fi

if [ ! -f "$SCRIPT_DIR/weareswarm/hero-swarm.php" ]; then
    echo "❌ ERROR: hero-swarm.php not found in deployment package"
    exit 1
fi

echo "✅ Deployment package verified"

# Create backup of existing theme (if it exists)
if [ -d "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm" ]; then
    echo "📦 Creating backup of existing theme..."
    mv "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm" "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm$BACKUP_SUFFIX"
    echo "✅ Backup created: weareswarm$BACKUP_SUFFIX"
fi

# Deploy theme files
echo "🚀 Deploying WeAreSwarm theme with swarm intelligence features..."
cp -r "$SCRIPT_DIR/weareswarm" "$PRODUCTION_WP_PATH/wp-content/themes/"
echo "✅ Theme files deployed"

# Set proper permissions
echo "🔧 Setting secure permissions..."
find "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm" -type f -exec chmod 644 {} \;
find "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm" -type d -exec chmod 755 {} \;

# Create necessary directories for swarm features
echo "🏗️ Creating swarm intelligence directories..."
mkdir -p "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm/css"
mkdir -p "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm/js"
mkdir -p "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm/images"

# Create swarm intelligence CSS file
cat > "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm/css/swarm-intelligence.css" << 'EOF'
/* Swarm Intelligence Core Styles */
.swarm-core-active { display: block; }
.swarm-agent-status { position: relative; }
.swarm-agent-status::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 8px;
    height: 8px;
    background: #a855f7;
    border-radius: 50%;
    animation: swarmPulse 2s infinite;
}
EOF

# Create swarm intelligence JavaScript file
cat > "$PRODUCTION_WP_PATH/wp-content/themes/weareswarm/js/swarm-intelligence.js" << 'EOF'
/**
 * Swarm Intelligence JavaScript
 * Collective AI Agent Coordination
 */

(function($) {
    'use strict';

    // Swarm Intelligence Core
    window.SwarmIntelligence = {

        init: function() {
            this.bindEvents();
            this.initializeAgentStatus();
            this.startCoordinationPulse();
        },

        bindEvents: function() {
            $(document).on('click', '.swarm-agent-link', this.handleAgentInteraction.bind(this));
            $(document).on('mouseenter', '.agent-node', this.showAgentTooltip.bind(this));
            $(document).on('mouseleave', '.agent-node', this.hideAgentTooltip.bind(this));
        },

        initializeAgentStatus: function() {
            // Initialize agent status monitoring
            if (typeof swarmIntelligence !== 'undefined') {
                this.updateAgentStatuses(swarmIntelligence.agents);
            }
        },

        updateAgentStatuses: function(agents) {
            agents.forEach(function(agent) {
                var statusElement = $('.agent-status-' + agent.id.toLowerCase().replace('-', ''));
                if (statusElement.length) {
                    statusElement.attr('data-status', agent.status);
                    statusElement.toggleClass('active', agent.status === 'active');
                }
            });
        },

        startCoordinationPulse: function() {
            // Pulse effect for swarm coordination
            setInterval(function() {
                $('.swarm-coordination-active').toggleClass('pulse-active');
            }, 3000);
        },

        handleAgentInteraction: function(e) {
            e.preventDefault();
            var agentId = $(e.currentTarget).data('agent-id');

            // Trigger swarm coordination event
            $(document).trigger('swarm:agent:interact', [agentId]);

            // Visual feedback
            this.showCoordinationEffect($(e.currentTarget));
        },

        showCoordinationEffect: function(element) {
            element.addClass('coordination-active');
            setTimeout(function() {
                element.removeClass('coordination-active');
            }, 1000);
        },

        showAgentTooltip: function(e) {
            // Implementation for agent tooltips
        },

        hideAgentTooltip: function(e) {
            // Implementation for hiding tooltips
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        SwarmIntelligence.init();
    });

})(jQuery);
EOF

echo "✅ Swarm intelligence assets created"

# Activate theme via WP-CLI (if available)
if command -v wp &> /dev/null; then
    echo "🎯 Activating WeAreSwarm theme..."
    cd "$PRODUCTION_WP_PATH"
    wp theme activate weareswarm
    echo "✅ Theme activated"

    # Flush permalinks
    wp rewrite flush
    echo "✅ Permalinks flushed"
else
    echo "⚠️ WP-CLI not found - manually activate theme in WordPress admin"
fi

echo ""
echo "🎉 DEPLOYMENT COMPLETE - Swarm Intelligence Activated!"
echo ""
echo "Next steps:"
echo "1. Activate theme in WordPress admin (if not done automatically)"
echo "2. Test swarm intelligence features on homepage"
echo "3. Verify agent coordination animations"
echo "4. Check theme customization options"
echo "5. Remove backup directory if everything works: rm -rf weareswarm$BACKUP_SUFFIX"
echo ""
echo "🐝 Swarm Intelligence Theme Successfully Deployed!"
echo "   Theme: WeAreSwarm v1.0.0"
echo "   Features: Collective AI Agent Coordination"
echo "   Status: ACTIVE 🐝⚡️🔥"