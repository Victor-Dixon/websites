#!/usr/bin/env python3
"""
Monitor Swarm Activity
======================

Tracks force multiplier delegations, loop closures, communication bottlenecks
across the swarm for the websites project.

Agent-6: Coordination & Communication Specialist
Task: Monitor swarm activity (MEDIUM priority)
"""

import json
from pathlib import Path
from datetime import datetime
from typing import Dict, List
import subprocess

def load_agent_statuses() -> Dict:
    """Load agent status files from Agent_Cellphone_V2_Repository."""
    agent_repo = Path("D:/Agent_Cellphone_V2_Repository")
    if not agent_repo.exists():
        return {}
    
    agents = {}
    agent_workspaces = agent_repo / "agent_workspaces"
    
    if not agent_workspaces.exists():
        return {}
    
    for agent_dir in agent_workspaces.iterdir():
        if agent_dir.is_dir() and agent_dir.name.startswith("Agent-"):
            status_file = agent_dir / "status.json"
            if status_file.exists():
                try:
                    with open(status_file, 'r', encoding='utf-8') as f:
                        agents[agent_dir.name] = json.load(f)
                except Exception:
                    pass
    
    return agents

def analyze_force_multiplier_delegations(agents: Dict) -> List[Dict]:
    """Analyze force multiplier delegations from agent statuses."""
    delegations = []
    
    for agent_id, status in agents.items():
        current_tasks = status.get("current_tasks", [])
        for task in current_tasks:
            task_str = str(task).lower()
            if "force multiplier" in task_str or "bilateral" in task_str or "coordination" in task_str:
                delegations.append({
                    "agent": agent_id,
                    "task": str(task)[:200],
                    "type": "force_multiplier"
                })
    
    return delegations

def analyze_loop_closures(agents: Dict) -> List[Dict]:
    """Analyze loop closures from agent statuses."""
    closures = []
    
    for agent_id, status in agents.items():
        current_tasks = status.get("current_tasks", [])
        completed_tasks = status.get("completed_tasks", [])
        
        # Look for completed coordination tasks
        for task in completed_tasks:
            task_str = str(task).lower()
            if "coordination" in task_str or "bilateral" in task_str or "complete" in task_str:
                closures.append({
                    "agent": agent_id,
                    "task": str(task)[:200],
                    "type": "loop_closure"
                })
    
    return closures

def analyze_communication_bottlenecks(agents: Dict) -> List[Dict]:
    """Analyze communication bottlenecks."""
    bottlenecks = []
    
    for agent_id, status in agents.items():
        current_tasks = status.get("current_tasks", [])
        for task in current_tasks:
            task_str = str(task).lower()
            if "waiting" in task_str or "blocker" in task_str or "pending" in task_str:
                bottlenecks.append({
                    "agent": agent_id,
                    "task": str(task)[:200],
                    "type": "bottleneck"
                })
    
    return bottlenecks

def generate_swarm_activity_report() -> Dict:
    """Generate comprehensive swarm activity report."""
    agents = load_agent_statuses()
    
    report = {
        "timestamp": datetime.now().isoformat(),
        "agents_analyzed": len(agents),
        "force_multiplier_delegations": analyze_force_multiplier_delegations(agents),
        "loop_closures": analyze_loop_closures(agents),
        "communication_bottlenecks": analyze_communication_bottlenecks(agents),
        "summary": {}
    }
    
    # Calculate summary
    report["summary"] = {
        "total_delegations": len(report["force_multiplier_delegations"]),
        "total_closures": len(report["loop_closures"]),
        "total_bottlenecks": len(report["communication_bottlenecks"]),
        "active_agents": sum(1 for a in agents.values() if a.get("status") in ["ACTIVE", "ACTIVE_AGENT_MODE"]),
        "coordination_health": "GOOD" if len(report["communication_bottlenecks"]) < 5 else "NEEDS_ATTENTION"
    }
    
    return report

def main():
    """Main execution."""
    print("=" * 70)
    print("SWARM ACTIVITY MONITOR")
    print("=" * 70)
    print()
    
    report = generate_swarm_activity_report()
    
    print(f"Agents Analyzed: {report['agents_analyzed']}")
    print()
    
    print("=" * 70)
    print("FORCE MULTIPLIER DELEGATIONS:")
    print("-" * 70)
    if report["force_multiplier_delegations"]:
        for delegation in report["force_multiplier_delegations"]:
            print(f"  • {delegation['agent']}: {delegation['task'][:100]}...")
    else:
        print("  No force multiplier delegations found")
    
    print()
    print("=" * 70)
    print("LOOP CLOSURES:")
    print("-" * 70)
    if report["loop_closures"]:
        for closure in report["loop_closures"][:10]:  # Show first 10
            print(f"  ✅ {closure['agent']}: {closure['task'][:100]}...")
    else:
        print("  No recent loop closures found")
    
    print()
    print("=" * 70)
    print("COMMUNICATION BOTTLENECKS:")
    print("-" * 70)
    if report["communication_bottlenecks"]:
        for bottleneck in report["communication_bottlenecks"]:
            print(f"  ⚠️  {bottleneck['agent']}: {bottleneck['task'][:100]}...")
    else:
        print("  ✅ No communication bottlenecks identified")
    
    print()
    print("=" * 70)
    print("SUMMARY:")
    print("-" * 70)
    summary = report["summary"]
    print(f"  Total delegations: {summary['total_delegations']}")
    print(f"  Total closures: {summary['total_closures']}")
    print(f"  Total bottlenecks: {summary['total_bottlenecks']}")
    print(f"  Active agents: {summary['active_agents']}")
    print(f"  Coordination health: {summary['coordination_health']}")
    
    # Save report
    project_root = Path(__file__).parent.parent
    reports_dir = project_root / "docs" / "swarm_activity"
    reports_dir.mkdir(parents=True, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    report_file = reports_dir / f"swarm_activity_report_{timestamp}.json"
    
    with open(report_file, 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2)
    
    print()
    print(f"✅ Report saved to: {report_file}")

if __name__ == "__main__":
    main()

