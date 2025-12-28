#!/usr/bin/env python3
"""
Coordinate freerideinvestor.com Content Display Issue
=====================================================

Facilitates coordination for CRITICAL issue: Site accessible (HTTP 200) but
main content area is empty. Only header and navigation visible, no main
content rendering.

Agent-6: Coordination & Communication Specialist
Task: Coordinate investigation and fix for freerideinvestor.com empty content issue
"""

import json
from pathlib import Path
from datetime import datetime
from typing import Dict, List

def load_audit_report() -> Dict:
    """Load comprehensive audit report if available."""
    project_root = Path(__file__).parent.parent
    audit_file = project_root / "docs" / "freerideinvestor_comprehensive_audit_2025-12-22.md"
    
    if not audit_file.exists():
        return {"status": "not_found", "note": "Audit report not found"}
    
    try:
        with open(audit_file, 'r', encoding='utf-8') as f:
            content = f.read()
            return {
                "status": "found",
                "content": content[:1000],  # First 1000 chars
                "file": str(audit_file)
            }
    except Exception as e:
        return {"status": "error", "error": str(e)}

def analyze_coordination_needs() -> Dict:
    """Analyze coordination needs for the content issue."""
    audit = load_audit_report()
    
    coordination_plan = {
        "timestamp": datetime.now().isoformat(),
        "issue": "freerideinvestor.com main content area empty",
        "severity": "CRITICAL",
        "status": "Site accessible (HTTP 200) but no main content rendering",
        "audit_report": audit,
        "coordination_agents": [],
        "investigation_steps": [],
        "next_actions": []
    }
    
    # Identify coordination agents based on issue type
    # This is a WordPress/theme issue, so Agent-7 (web) and Agent-1 (integration) are key
    coordination_plan["coordination_agents"] = [
        {
            "agent": "Agent-7",
            "role": "Web Development Specialist",
            "responsibility": "WordPress theme investigation, content rendering fix",
            "priority": "HIGH"
        },
        {
            "agent": "Agent-1",
            "role": "Integration & Core Systems Specialist",
            "responsibility": "Previous fixes verification, theme file investigation",
            "priority": "MEDIUM"
        },
        {
            "agent": "Agent-8",
            "role": "SSOT & System Integration Specialist",
            "responsibility": "Audit report review, technical diagnostics",
            "priority": "MEDIUM"
        }
    ]
    
    # Investigation steps
    coordination_plan["investigation_steps"] = [
        "1. Verify theme template files (index.php, home.php, front-page.php) are rendering content",
        "2. Check WordPress loop (the_content(), the_title(), etc.) in theme templates",
        "3. Verify database content (posts, pages) exists and is published",
        "4. Check for JavaScript errors preventing content rendering",
        "5. Verify theme functions.php isn't suppressing content output",
        "6. Check for CSS issues hiding content (display: none, visibility: hidden)",
        "7. Verify WordPress query is returning posts/pages correctly"
    ]
    
    # Next actions
    coordination_plan["next_actions"] = [
        {
            "action": "Coordinate with Agent-7 to investigate WordPress theme template files",
            "priority": "CRITICAL",
            "agents": ["Agent-7"]
        },
        {
            "action": "Coordinate with Agent-1 to verify previous fixes didn't break content rendering",
            "priority": "HIGH",
            "agents": ["Agent-1"]
        },
        {
            "action": "Review Agent-8's comprehensive audit report for additional diagnostics",
            "priority": "HIGH",
            "agents": ["Agent-6"]
        }
    ]
    
    return coordination_plan

def main():
    """Main execution."""
    print("=" * 70)
    print("FREERIDEINVESTOR.COM CONTENT ISSUE COORDINATION")
    print("=" * 70)
    print()
    
    plan = analyze_coordination_needs()
    
    print("ISSUE:")
    print(f"  Site: freerideinvestor.com")
    print(f"  Severity: {plan['severity']}")
    print(f"  Status: {plan['status']}")
    print()
    
    print("=" * 70)
    print("COORDINATION AGENTS:")
    print("-" * 70)
    for agent in plan["coordination_agents"]:
        print(f"  • {agent['agent']} ({agent['role']})")
        print(f"    Responsibility: {agent['responsibility']}")
        print(f"    Priority: {agent['priority']}")
        print()
    
    print("=" * 70)
    print("INVESTIGATION STEPS:")
    print("-" * 70)
    for step in plan["investigation_steps"]:
        print(f"  {step}")
    
    print()
    print("=" * 70)
    print("NEXT ACTIONS:")
    print("-" * 70)
    for action in plan["next_actions"]:
        print(f"  • {action['action']}")
        print(f"    Priority: {action['priority']}")
        print(f"    Agents: {', '.join(action['agents'])}")
        print()
    
    # Save coordination plan
    project_root = Path(__file__).parent.parent
    reports_dir = project_root / "docs" / "coordination"
    reports_dir.mkdir(parents=True, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    plan_file = reports_dir / f"freerideinvestor_content_coordination_{timestamp}.json"
    
    with open(plan_file, 'w', encoding='utf-8') as f:
        json.dump(plan, f, indent=2)
    
    print(f"✅ Coordination plan saved to: {plan_file}")

if __name__ == "__main__":
    main()


