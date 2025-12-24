#!/usr/bin/env python3
"""
Coordinate Text Rendering Fixes
================================

Coordinates urgent fixes for text rendering issues across all websites.
This is a CRITICAL visual quality issue that makes our sites look unprofessional.

Agent-6: Coordination & Communication Specialist
Task: Coordinate text rendering fixes (CRITICAL visual quality issue)
"""

import json
from pathlib import Path
from datetime import datetime
from typing import Dict, List

def load_quality_assessment() -> Dict:
    """Load the latest visual quality assessment."""
    project_root = Path(__file__).parent.parent
    reports_dir = project_root / "docs" / "quality_assessments"
    
    json_files = sorted(reports_dir.glob("visual_quality_assessment_*.json"), reverse=True)
    if not json_files:
        return {"status": "not_found"}
    
    latest_file = json_files[0]
    try:
        with open(latest_file, 'r', encoding='utf-8') as f:
            report = json.load(f)
            report["file"] = str(latest_file)
            report["status"] = "found"
            return report
    except Exception as e:
        return {"status": "error", "error": str(e)}

def create_coordination_plan(report: Dict) -> Dict:
    """Creates coordination plan for fixing text rendering issues."""
    plan = {
        "timestamp": datetime.now().isoformat(),
        "issue": "CRITICAL: Text rendering problems across all websites",
        "severity": "CRITICAL",
        "impact": "Makes all sites look unprofessional and broken - reflects poorly on Swarm quality",
        "affected_sites": len([s for s in report.get("sites", []) if s.get("text_rendering", {}).get("has_issues")]),
        "coordination_agents": [],
        "fix_priority": "IMMEDIATE",
        "recommended_actions": []
    }
    
    # Agent assignments
    plan["coordination_agents"] = [
        {
            "agent": "Agent-7",
            "role": "Web Development Specialist",
            "responsibility": "Fix text rendering issues - likely CSS/WordPress theme font rendering problem",
            "priority": "CRITICAL",
            "sites_affected": plan["affected_sites"],
            "estimated_effort": "2-4 hours (batch fix across all sites)"
        },
        {
            "agent": "Agent-1",
            "role": "Integration & Core Systems Specialist",
            "responsibility": "Verify fixes don't break existing functionality, test across sites",
            "priority": "HIGH",
            "estimated_effort": "1-2 hours (testing and validation)"
        },
        {
            "agent": "Agent-3",
            "role": "Infrastructure & DevOps Specialist",
            "responsibility": "Deploy fixes and verify deployment success",
            "priority": "HIGH",
            "estimated_effort": "1 hour (deployment validation)"
        }
    ]
    
    # Recommended actions
    plan["recommended_actions"] = [
        {
            "action": "Investigate root cause of text rendering issues",
            "agent": "Agent-7",
            "priority": "CRITICAL",
            "description": "Check CSS font rendering, WordPress theme settings, character encoding issues"
        },
        {
            "action": "Create batch fix tool for all affected sites",
            "agent": "Agent-7",
            "priority": "CRITICAL",
            "description": "Develop automated fix that can be applied across all 11 sites"
        },
        {
            "action": "Test fix on one site first (freerideinvestor.com recommended)",
            "agent": "Agent-7",
            "priority": "HIGH",
            "description": "Validate fix works before applying to all sites"
        },
        {
            "action": "Deploy fixes to all affected sites",
            "agent": "Agent-7 + Agent-3",
            "priority": "CRITICAL",
            "description": "Batch deploy fix across all sites"
        },
        {
            "action": "Verify visual quality after fixes",
            "agent": "Agent-6",
            "priority": "HIGH",
            "description": "Re-run visual quality assessment to confirm fixes"
        }
    ]
    
    return plan

def main():
    """Main execution."""
    print("=" * 70)
    print("TEXT RENDERING FIXES COORDINATION")
    print("=" * 70)
    print()
    print("🚨 CRITICAL VISUAL QUALITY ISSUE DETECTED")
    print("=" * 70)
    print()
    
    assessment = load_quality_assessment()
    if assessment.get("status") != "found":
        print(f"❌ Could not load quality assessment: {assessment.get('status', 'unknown')}")
        return
    
    print(f"Loaded assessment: {assessment.get('file', 'unknown')}")
    print(f"Total sites assessed: {assessment.get('total_sites', 0)}")
    print(f"Sites with text rendering issues: {assessment.get('sites_with_text_rendering_issues', 0)}")
    print()
    
    coordination_plan = create_coordination_plan(assessment)
    
    print("=" * 70)
    print("COORDINATION PLAN")
    print("=" * 70)
    print()
    print(f"Issue: {coordination_plan['issue']}")
    print(f"Severity: {coordination_plan['severity']}")
    print(f"Impact: {coordination_plan['impact']}")
    print(f"Affected Sites: {coordination_plan['affected_sites']}")
    print(f"Fix Priority: {coordination_plan['fix_priority']}")
    print()
    
    print("=" * 70)
    print("AGENT ASSIGNMENTS:")
    print("-" * 70)
    for agent in coordination_plan["coordination_agents"]:
        print(f"\n{agent['agent']} ({agent['role']}):")
        print(f"  Responsibility: {agent['responsibility']}")
        print(f"  Priority: {agent['priority']}")
        if agent.get('sites_affected'):
            print(f"  Sites Affected: {agent['sites_affected']}")
        print(f"  Estimated Effort: {agent['estimated_effort']}")
    
    print()
    print("=" * 70)
    print("RECOMMENDED ACTIONS:")
    print("-" * 70)
    for i, action in enumerate(coordination_plan["recommended_actions"], 1):
        print(f"\n{i}. [{action['priority']}] {action['action']}")
        print(f"   Agent(s): {action['agent']}")
        print(f"   Description: {action['description']}")
    
    # Save coordination plan
    project_root = Path(__file__).parent.parent
    reports_dir = project_root / "docs" / "coordination"
    reports_dir.mkdir(parents=True, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    plan_file = reports_dir / f"text_rendering_fixes_coordination_{timestamp}.json"
    
    with open(plan_file, 'w', encoding='utf-8') as f:
        json.dump(coordination_plan, f, indent=2)
    
    print()
    print("=" * 70)
    print("⚠️  URGENT ACTION REQUIRED")
    print("=" * 70)
    print("This is a CRITICAL visual quality issue affecting ALL websites.")
    print("It makes our sites look unprofessional and reflects poorly on Swarm quality.")
    print("Immediate coordination with Agent-7 is required to fix text rendering issues.")
    print()
    print(f"✅ Coordination plan saved to: {plan_file}")

if __name__ == "__main__":
    main()


