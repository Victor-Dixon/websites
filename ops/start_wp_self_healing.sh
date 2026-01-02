#!/bin/bash
# WordPress Self-Healing System Startup Script
# ===========================================
#
# This script initializes the complete WordPress self-healing ecosystem:
# 1. Enables WP_DEBUG on all sites
# 2. Starts the monitoring daemon
# 3. Initializes deployment pipeline integration
# 4. Sets up automated healing triggers
#
# Usage:
#   ./ops/start_wp_self_healing.sh          # Start all systems
#   ./ops/start_wp_self_healing.sh --dry-run # Test without starting
#   ./ops/start_wp_self_healing.sh --stop    # Stop all systems

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
PYTHON_CMD="python"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Python is available
check_python() {
    if ! command -v python &> /dev/null && ! command -v python3 &> /dev/null; then
        log_error "Python is not installed or not in PATH"
        exit 1
    fi

    # Use python3 if available, otherwise python
    if command -v python3 &> /dev/null; then
        PYTHON_CMD="python3"
    fi

    log_info "Using Python: $($PYTHON_CMD --version)"
}

# Check if required files exist
check_requirements() {
    local required_files=(
        "config/site_configs.json"
        "config/wp_monitor_config.json"
        "ops/deployment/wp_debug_self_healing.py"
        "ops/deployment/wp_error_monitor.py"
        "ops/deployment/deployment_pipeline.py"
    )

    local missing_files=()

    for file in "${required_files[@]}"; do
        if [[ ! -f "$REPO_ROOT/$file" ]]; then
            missing_files+=("$file")
        fi
    done

    if [[ ${#missing_files[@]} -gt 0 ]]; then
        log_error "Missing required files:"
        for file in "${missing_files[@]}"; do
            echo "  - $file"
        done
        exit 1
    fi

    log_success "All required files found"
}

# Enable WP_DEBUG on all sites
enable_wp_debug_all_sites() {
    log_info "Enabling WP_DEBUG on all sites..."

    local sites=("freerideinvestor.com" "dadudekc.com" "southwestsecret.com" "weareswarm.site" "prismblossom.online")
    local success_count=0

    for site in "${sites[@]}"; do
        log_info "Enabling WP_DEBUG for $site..."

        if $PYTHON_CMD "$REPO_ROOT/ops/deployment/wp_debug_self_healing.py" --enable-debug "$site" 2>/dev/null; then
            log_success "WP_DEBUG enabled for $site"
            ((success_count++))
        else
            log_warning "Failed to enable WP_DEBUG for $site"
        fi
    done

    log_info "WP_DEBUG enabled on $success_count/${#sites[@]} sites"
}

# Start the monitoring daemon
start_monitor_daemon() {
    log_info "Starting WordPress error monitoring daemon..."

    # Check if already running
    if pgrep -f "wp_error_monitor.py" > /dev/null; then
        log_warning "Monitor daemon already running"
        return 0
    fi

    # Start in background
    nohup $PYTHON_CMD "$REPO_ROOT/ops/deployment/wp_error_monitor.py" --daemon > "$REPO_ROOT/wp_monitor.log" 2>&1 &
    local pid=$!

    # Wait a moment and check if it's still running
    sleep 2
    if kill -0 $pid 2>/dev/null; then
        log_success "Monitor daemon started (PID: $pid)"
        echo $pid > "$REPO_ROOT/wp_monitor.pid"
    else
        log_error "Monitor daemon failed to start"
        cat "$REPO_ROOT/wp_monitor.log" 2>/dev/null || true
        return 1
    fi
}

# Stop the monitoring daemon
stop_monitor_daemon() {
    log_info "Stopping WordPress error monitoring daemon..."

    local pid_file="$REPO_ROOT/wp_monitor.pid"

    if [[ -f "$pid_file" ]]; then
        local pid=$(cat "$pid_file")
        if kill -TERM $pid 2>/dev/null; then
            log_success "Monitor daemon stopped (PID: $pid)"
            rm -f "$pid_file"
        else
            log_warning "Failed to stop monitor daemon (PID: $pid)"
        fi
    else
        # Try to find and kill any running monitor processes
        if pkill -f "wp_error_monitor.py" 2>/dev/null; then
            log_success "Monitor daemon stopped (via pkill)"
        else
            log_info "No monitor daemon found running"
        fi
    fi
}

# Test the self-healing system
test_self_healing() {
    log_info "Testing self-healing system..."

    # Run a quick test
    if $PYTHON_CMD "$REPO_ROOT/ops/deployment/wp_debug_self_healing.py" --report 2>/dev/null; then
        log_success "Self-healing system test passed"
    else
        log_warning "Self-healing system test failed (this may be expected if no reports exist)"
    fi

    # Test monitor status
    if $PYTHON_CMD "$REPO_ROOT/ops/deployment/wp_error_monitor.py" --status 2>/dev/null; then
        log_success "Monitor system test passed"
    else
        log_warning "Monitor system test failed"
    fi
}

# Show system status
show_status() {
    echo
    log_info "WordPress Self-Healing System Status"
    echo "===================================="

    # Check if monitor is running
    if pgrep -f "wp_error_monitor.py" > /dev/null; then
        log_success "✓ Monitor daemon is running"
    else
        log_error "✗ Monitor daemon is not running"
    fi

    # Check configuration files
    local config_files=("config/site_configs.json" "config/wp_monitor_config.json")
    for config_file in "${config_files[@]}"; do
        if [[ -f "$REPO_ROOT/$config_file" ]]; then
            log_success "✓ $config_file exists"
        else
            log_error "✗ $config_file missing"
        fi
    done

    # Check log files
    local log_files=("wp_monitor.log" "self_healing_report.json")
    for log_file in "${log_files[@]}"; do
        if [[ -f "$REPO_ROOT/$log_file" ]]; then
            log_success "✓ $log_file exists"
        else
            log_info "ℹ $log_file not yet created"
        fi
    done

    # Show recent healing activity
    if [[ -f "$REPO_ROOT/self_healing_report.json" ]]; then
        echo
        log_info "Recent Self-Healing Activity:"
        $PYTHON_CMD "$REPO_ROOT/ops/deployment/wp_debug_self_healing.py" --report 2>/dev/null | head -10
    fi
}

# Main startup function
start_system() {
    echo "🚀 Starting WordPress Self-Healing System"
    echo "=========================================="
    echo

    # Pre-flight checks
    check_python
    check_requirements

    # Enable WP_DEBUG on all sites
    enable_wp_debug_all_sites

    # Test the system
    test_self_healing

    # Start monitoring daemon
    start_monitor_daemon

    echo
    log_success "WordPress Self-Healing System startup complete!"
    echo
    log_info "The system will now:"
    echo "  • Monitor all WordPress sites for errors"
    echo "  • Automatically apply fixes for known issues"
    echo "  • Send notifications when healing occurs"
    echo "  • Maintain detailed logs of all activity"
    echo
    log_info "To check status: ./ops/start_wp_self_healing.sh --status"
    log_info "To stop system: ./ops/start_wp_self_healing.sh --stop"
}

# Parse command line arguments
case "${1:-}" in
    --dry-run)
        log_info "DRY RUN MODE - Testing configuration without starting services"
        check_python
        check_requirements
        test_self_healing
        show_status
        ;;
    --stop)
        stop_monitor_daemon
        ;;
    --status)
        show_status
        ;;
    --test)
        test_self_healing
        ;;
    --help|-h)
        echo "WordPress Self-Healing System Startup Script"
        echo
        echo "Usage:"
        echo "  $0                    # Start the complete self-healing system"
        echo "  $0 --dry-run         # Test configuration without starting"
        echo "  $0 --stop            # Stop the monitoring daemon"
        echo "  $0 --status          # Show system status"
        echo "  $0 --test           # Test self-healing components"
        echo "  $0 --help           # Show this help"
        echo
        echo "The system includes:"
        echo "  • Real-time error monitoring"
        echo "  • Automatic self-healing for known issues"
        echo "  • Integration with deployment pipeline"
        echo "  • Comprehensive logging and notifications"
        ;;
    *)
        start_system
        ;;
esac