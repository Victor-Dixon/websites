/**
 * Trading Robot Plug - Centralized API Configuration Utilities
 * Provides consistent environment detection and API URL management across all JavaScript files
 *
 * @version 1.0.0
 * @author Agent-7 (Web Development Specialist)
 */

// #region Environment Detection
/**
 * Check if we're in a development environment
 * @returns {boolean} True if development environment detected
 */
function isDevelopmentEnvironment() {
    const hostname = window.location.hostname;

    // Development environment patterns
    return hostname === 'localhost' ||
           hostname === '127.0.0.1' ||
           hostname.includes('.local') ||
           hostname.includes('dev.') ||
           hostname.includes('staging');
}

/**
 * Get the current environment type
 * @returns {string} 'development', 'staging', or 'production'
 */
function getEnvironmentType() {
    const hostname = window.location.hostname;

    if (hostname === 'localhost' || hostname === '127.0.0.1' || hostname.includes('.local')) {
        return 'development';
    } else if (hostname.includes('staging') || hostname.includes('dev.')) {
        return 'staging';
    } else {
        return 'production';
    }
}
// #endregion

// #region API URL Builders
/**
 * Get WebSocket URL for real-time events
 * @returns {string} Complete WebSocket URL
 */
function getWebSocketUrl() {
    // Check if WordPress config provides override
    if (typeof tradingRobotPlugConfig !== 'undefined' && tradingRobotPlugConfig.websocketUrl) {
        return tradingRobotPlugConfig.websocketUrl;
    }

    // Environment-based URL selection
    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const isDevelopment = isDevelopmentEnvironment();

    if (isDevelopment) {
        return `${protocol}//localhost:8001/ws/events`;
    } else {
        return `${protocol}//api.tradingrobotplug.com/ws/events`;
    }
}

/**
 * Get ingestion API URL for logging/analytics
 * @returns {string} Complete ingestion API URL
 */
function getIngestionApiUrl() {
    // Check if WordPress config provides override
    if (typeof tradingRobotPlugConfig !== 'undefined' && tradingRobotPlugConfig.ingestionUrl) {
        return tradingRobotPlugConfig.ingestionUrl;
    }

    // Environment-based URL selection
    const isDevelopment = isDevelopmentEnvironment();

    if (isDevelopment) {
        return 'http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89';
    } else {
        return 'https://api.tradingrobotplug.com/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89';
    }
}

/**
 * Get FastAPI base URL
 * @returns {string} Complete FastAPI base URL
 */
function getFastApiUrl() {
    // Check if WordPress config provides override
    if (typeof tradingRobotPlugConfig !== 'undefined' && tradingRobotPlugConfig.fastApiUrl) {
        return tradingRobotPlugConfig.fastApiUrl;
    }

    // Environment-based URL selection
    const protocol = window.location.protocol === 'https:' ? 'https:' : 'http:';
    const isDevelopment = isDevelopmentEnvironment();

    if (isDevelopment) {
        return `${protocol}//localhost:8001`;
    } else {
        return `${protocol}//api.tradingrobotplug.com`;
    }
}
// #endregion

// #region Utility Functions
/**
 * Get protocol-appropriate URL prefix
 * @param {boolean} secure - Force HTTPS/WSS
 * @returns {string} Protocol string with ://
 */
function getProtocolPrefix(secure = null) {
    if (secure === true) {
        return 'https:';
    } else if (secure === false) {
        return 'http:';
    } else {
        return window.location.protocol;
    }
}

/**
 * Create a full API URL with path
 * @param {string} baseUrl - Base API URL
 * @param {string} path - API endpoint path (without leading slash)
 * @returns {string} Complete API URL
 */
function buildApiUrl(baseUrl, path) {
    const cleanBase = baseUrl.replace(/\/$/, '');
    const cleanPath = path.replace(/^\//, '');
    return `${cleanBase}/${cleanPath}`;
}
// #endregion

// Export functions for module usage (if supported)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        isDevelopmentEnvironment,
        getEnvironmentType,
        getWebSocketUrl,
        getIngestionApiUrl,
        getFastApiUrl,
        getProtocolPrefix,
        buildApiUrl
    };
}