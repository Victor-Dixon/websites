# Cycle Snapshot System - Phase 2 Architecture Specification

**Document**: Phase 2 Implementation Architecture
**Version**: 1.0
**Date**: 2026-01-04
**Author**: Agent-2 (Architecture & Design Specialist)
**Status**: Architecture Guidance Complete

## Executive Summary

Phase 2 transforms the Cycle Snapshot System from batch processing to real-time coordination, adding live monitoring, automated alerts, and intelligent analytics. This phase introduces WebSocket connectivity, streaming data pipelines, and proactive issue detection.

## Phase 2 Architecture Overview

### Enhanced Central Hub Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    CYCLE SNAPSHOT HUB v2.0                       │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                 REAL-TIME COLLECTOR                     │    │
│  │  ┌─────────────┬─────────────┬─────────────────────┐    │    │
│  │  │Data Sources │Live Updates │WebSocket Streams    │    │    │
│  │  └─────────────┴─────────────┴─────────────────────┘    │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │               INTELLIGENT COORDINATION ENGINE          │    │
│  │  ┌─────────────┬─────────────┬─────────────────────┐    │    │
│  │  │Task Routing │Alert Engine│Conflict Resolution  │    │    │
│  │  └─────────────┴─────────────┴─────────────────────┘    │    │
│  └─────────────────────────────────────────────────────────┘    │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │            ANALYTICS & MONITORING DASHBOARD            │    │
│  │  ┌─────────────┬─────────────┬─────────────────────┐    │    │
│  │  │Real-time    │Historical   │Predictive Analytics│    │    │
│  │  │Dashboards   │Analysis     │& Trends            │    │    │
│  │  └─────────────┴─────────────┴─────────────────────┘    │    │
│  └─────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
```

## Core Components

### 1. Real-Time Data Collector

#### WebSocket Integration Layer
```python
# WebSocket Manager for Live Status Updates
class WebSocketStatusManager:
    def __init__(self, hub_port: int = 8765):
        self.hub_port = hub_port
        self.active_connections: Dict[str, WebSocket] = {}
        self.status_streams: Dict[str, asyncio.Queue] = {}

    async def register_agent(self, agent_id: str, websocket: WebSocket):
        """Register agent for real-time status updates"""
        self.active_connections[agent_id] = websocket
        self.status_streams[agent_id] = asyncio.Queue()

    async def broadcast_status_update(self, agent_id: str, status_data: dict):
        """Broadcast status change to all connected clients"""
        update_message = {
            "type": "status_update",
            "agent_id": agent_id,
            "data": status_data,
            "timestamp": datetime.utcnow().isoformat()
        }
        await self._broadcast_to_all(update_message)
```

#### Live Data Pipeline
```python
# Real-time Data Pipeline
class RealTimeDataPipeline:
    def __init__(self, storage_backend: SnapshotStorage):
        self.storage = storage_backend
        self.change_detectors = {}
        self.alert_engine = AlertEngine()

    async def process_live_update(self, agent_id: str, update_data: dict):
        """Process real-time status update with change detection"""
        # Detect significant changes
        changes = self._detect_changes(agent_id, update_data)

        # Update storage
        await self.storage.store_live_update(agent_id, update_data)

        # Trigger alerts for significant changes
        if changes.get("significant"):
            await self.alert_engine.process_changes(changes)

        # Broadcast to WebSocket clients
        await self.websocket_manager.broadcast_status_update(agent_id, update_data)
```

### 2. Intelligent Alert Engine

#### Alert Rules Engine
```python
# Alert Rules and Conditions
@dataclass
class AlertRule:
    rule_id: str
    name: str
    condition: Callable[[dict], bool]
    severity: AlertSeverity
    cooldown_minutes: int
    message_template: str

class AlertEngine:
    def __init__(self):
        self.rules = self._load_alert_rules()
        self.active_alerts: Dict[str, Alert] = {}
        self.alert_history: List[Alert] = []

    def _load_alert_rules(self) -> List[AlertRule]:
        """Load predefined alert rules"""
        return [
            AlertRule(
                rule_id="agent_inactive",
                name="Agent Inactive Alert",
                condition=lambda status: status.get("status") == "INACTIVE",
                severity=AlertSeverity.WARNING,
                cooldown_minutes=30,
                message_template="Agent {agent_id} has been inactive for {inactive_minutes} minutes"
            ),
            AlertRule(
                rule_id="task_blocked",
                name="Task Blocked Alert",
                condition=lambda status: len(status.get("blockers", [])) > 0,
                severity=AlertSeverity.ERROR,
                cooldown_minutes=15,
                message_template="Agent {agent_id} has {blocker_count} active blockers"
            ),
            AlertRule(
                rule_id="high_cycle_count",
                name="High Cycle Count Alert",
                condition=lambda status: status.get("cycle_count", 0) > 50,
                severity=AlertSeverity.INFO,
                cooldown_minutes=60,
                message_template="Agent {agent_id} has completed {cycle_count} cycles"
            )
        ]
```

#### Alert Processing Pipeline
```python
async def process_changes(self, changes: Dict[str, Any]):
    """Process detected changes and trigger alerts"""
    applicable_rules = [
        rule for rule in self.rules
        if rule.condition(changes.get("new_status", {}))
    ]

    for rule in applicable_rules:
        if self._should_trigger_alert(rule):
            alert = await self._create_alert(rule, changes)
            await self._dispatch_alert(alert)
```

### 3. Real-Time Analytics Dashboard

#### Dashboard Architecture
```python
# Real-time Dashboard Manager
class RealTimeDashboard:
    def __init__(self, websocket_manager: WebSocketStatusManager):
        self.websocket = websocket_manager
        self.dashboard_clients: Set[WebSocket] = set()
        self.metrics_cache = TTLCache(maxsize=1000, ttl=300)  # 5 minute cache

    async def register_dashboard_client(self, websocket: WebSocket):
        """Register dashboard client for real-time updates"""
        self.dashboard_clients.add(websocket)
        # Send initial dashboard state
        initial_state = await self._generate_dashboard_state()
        await websocket.send_json(initial_state)

    async def broadcast_dashboard_update(self, update_type: str, data: dict):
        """Broadcast dashboard update to all connected clients"""
        message = {
            "type": "dashboard_update",
            "update_type": update_type,
            "data": data,
            "timestamp": datetime.utcnow().isoformat()
        }

        dead_clients = set()
        for client in self.dashboard_clients:
            try:
                await client.send_json(message)
            except Exception:
                dead_clients.add(client)

        # Clean up dead connections
        self.dashboard_clients -= dead_clients
```

#### Live Metrics Aggregation
```python
class LiveMetricsAggregator:
    def __init__(self, storage: SnapshotStorage):
        self.storage = storage
        self.metrics_windows = {
            "1m": 60,      # 1 minute rolling
            "5m": 300,     # 5 minute rolling
            "15m": 900,    # 15 minute rolling
            "1h": 3600,    # 1 hour rolling
        }

    async def get_live_metrics(self, metric_type: str, window: str) -> dict:
        """Get live aggregated metrics for dashboard"""
        window_seconds = self.metrics_windows.get(window, 300)

        # Query recent data
        recent_data = await self.storage.get_recent_data(window_seconds)

        # Aggregate based on metric type
        if metric_type == "agent_activity":
            return self._aggregate_agent_activity(recent_data)
        elif metric_type == "task_velocity":
            return self._aggregate_task_velocity(recent_data)
        elif metric_type == "system_health":
            return self._aggregate_system_health(recent_data)

        return {}
```

## Implementation Roadmap

### Sprint 1: Real-Time Infrastructure (Week 1)
1. **WebSocket Server Implementation**
   - Basic WebSocket server for agent connections
   - Connection management and heartbeat monitoring
   - Message routing and broadcasting

2. **Live Status Updates**
   - Agent status change detection
   - Real-time status broadcasting
   - Connection state management

3. **Data Pipeline Enhancement**
   - Streaming data ingestion
   - Real-time data validation
   - Memory-efficient buffering

### Sprint 2: Alert System (Week 2)
1. **Alert Rules Engine**
   - Define alert conditions and thresholds
   - Alert priority and severity levels
   - Alert cooldown and deduplication

2. **Alert Processing Pipeline**
   - Change detection algorithms
   - Alert generation and formatting
   - Alert routing and delivery

3. **Alert Integration**
   - Discord webhook integration
   - Email alert notifications
   - Alert acknowledgment system

### Sprint 3: Analytics Dashboard (Week 3)
1. **Dashboard Backend**
   - Real-time metrics aggregation
   - Dashboard data APIs
   - Client connection management

2. **Dashboard Frontend**
   - Real-time data visualization
   - Interactive charts and graphs
   - Alert display and management

3. **Historical Analytics**
   - Trend analysis algorithms
   - Performance metrics calculation
   - Predictive analytics foundation

## Integration Patterns

### Agent Integration
```python
# Agent-side WebSocket client integration
class AgentWebSocketClient:
    def __init__(self, agent_id: str, hub_url: str):
        self.agent_id = agent_id
        self.hub_url = hub_url
        self.websocket = None
        self.status_change_callbacks = []

    async def connect(self):
        """Connect to central hub and register"""
        self.websocket = await websockets.connect(self.hub_url)
        await self._register_with_hub()

        # Start status monitoring loop
        asyncio.create_task(self._monitor_status_changes())

    async def _monitor_status_changes(self):
        """Monitor local status.json for changes"""
        last_status = None
        while True:
            current_status = self._read_current_status()
            if current_status != last_status:
                await self._send_status_update(current_status)
                last_status = current_status
            await asyncio.sleep(5)  # Check every 5 seconds
```

### Dashboard Integration
```python
# Dashboard client integration
class DashboardWebSocketClient:
    def __init__(self, dashboard_element_id: str):
        self.dashboard_id = dashboard_element_id
        self.websocket = None
        self.update_handlers = {
            "status_update": self._handle_status_update,
            "alert": self._handle_alert,
            "metrics_update": self._handle_metrics_update
        }

    async def connect_and_listen(self):
        """Connect to hub and listen for updates"""
        uri = f"ws://localhost:8765/dashboard/{self.dashboard_id}"
        async with websockets.connect(uri) as websocket:
            self.websocket = websocket

            # Send initial dashboard subscription
            await websocket.send(json.dumps({
                "type": "dashboard_subscribe",
                "dashboard_id": self.dashboard_id
            }))

            # Listen for updates
            async for message in websocket:
                data = json.loads(message)
                handler = self.update_handlers.get(data["type"])
                if handler:
                    await handler(data)
```

## Performance & Scalability

### Connection Management
- **Connection Pooling**: Efficient WebSocket connection management
- **Load Balancing**: Distribute connections across multiple hub instances
- **Connection Recovery**: Automatic reconnection with exponential backoff

### Data Processing
- **Streaming Processing**: Handle high-frequency status updates
- **Memory Management**: Efficient buffering and cleanup
- **Async Processing**: Non-blocking data processing pipelines

### Storage Optimization
- **Time-Series Storage**: Optimized storage for time-series data
- **Compression**: Automatic compression for historical data
- **Caching**: Multi-level caching for frequently accessed metrics

## Security Considerations

### Authentication & Authorization
- **Connection Authentication**: Agent identity verification
- **Dashboard Access Control**: Role-based dashboard access
- **API Security**: Secure WebSocket connections with TLS

### Data Protection
- **Encryption**: End-to-end encryption for sensitive data
- **Audit Logging**: Complete audit trail for all operations
- **Data Sanitization**: Remove sensitive information from broadcasts

## Testing Strategy

### Unit Testing
- WebSocket connection handling
- Alert rule evaluation
- Metrics aggregation logic
- Dashboard data processing

### Integration Testing
- End-to-end WebSocket communication
- Alert system integration
- Dashboard real-time updates
- Cross-agent coordination

### Performance Testing
- High-frequency status updates
- Large numbers of concurrent connections
- Memory usage under load
- Database performance with streaming data

## Success Metrics

### Performance Metrics
- **Real-time Latency**: <100ms average update propagation
- **Connection Reliability**: >99.9% uptime for WebSocket connections
- **Alert Response Time**: <5 seconds from event to alert delivery

### User Experience Metrics
- **Dashboard Responsiveness**: <500ms dashboard update time
- **Alert Accuracy**: >95% reduction in false positive alerts
- **System Visibility**: Complete real-time view of all systems

### Business Value Metrics
- **Issue Detection Speed**: 60% faster problem identification
- **Coordination Efficiency**: 40% reduction in coordination overhead
- **System Reliability**: 50% reduction in undetected system issues

---

**Phase 2 Architecture**: ✅ Specification Complete
**Technical Leadership**: Agent-2 (Architecture & Design)
**Implementation Lead**: Agent-3 (Core Implementation)
**Next Phase**: Implementation Planning & Prototyping