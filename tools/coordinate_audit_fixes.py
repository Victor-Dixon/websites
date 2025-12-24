#!/usr/bin/env python3
"""
Coordinate Audit Fixes
======================

Analyzes comprehensive audit report and creates coordination plan for
addressing identified issues across all websites.

Agent-6: Coordination & Communication Specialist
Task: Coordinate fixes based on comprehensive website audit
"""

import json
from pathlib import Path
from datetime import datetime
from typing import Dict, List

def load_latest_audit_report() -> Dict:
    """Load the most recent audit report."""
    project_root = Path(__file__).parent.parent
    reports_dir = project_root / "docs" / "audit_reports"
    
    if not reports_dir.exists():
        return {"status": "not_found", "note": f"Reports directory not found: {reports_dir}"}
    
    # Find latest JSON report
    json_files = sorted(reports_dir.glob("comprehensive_audit_*.json"), reverse=True)
    if not json_files:
        return {"status": "not_found", "note": f"No audit reports found in {reports_dir}"}
    
    latest_file = json_files[0]
    try:
        with open(latest_file, 'r', encoding='utf-8') as f:
            report = json.load(f)
            report["file"] = str(latest_file)
            report["status"] = "found"
            return report
    except Exception as e:
        return {"status": "error", "error": str(e), "file": str(latest_file)}

def analyze_issues_by_agent(report: Dict) -> Dict:
    """Analyze issues and assign coordination agents."""
    coordination_plan = {
        "timestamp": datetime.now().isoformat(),
        "audit_report": report.get("file", "unknown"),
        "summary": report.get("summary", {}),
        "agent_assignments": {},
        "priority_actions": [],
        "coordination_needs": []
    }
    
    # Agent assignments based on issue categories
    agent_assignments = {
        "Agent-7": {
            "categories": ["SEO", "Content", "Performance"],
            "issues": [],
            "priority": "HIGH"
        },
        "Agent-1": {
            "categories": ["Health", "Content"],
            "issues": [],
            "priority": "HIGH"
        },
        "Agent-3": {
            "categories": ["Security", "Performance"],
            "issues": [],
            "priority": "MEDIUM"
        },
        "Agent-8": {
            "categories": ["Health", "Content"],
            "issues": [],
            "priority": "HIGH"
        }
    }
    
    # Analyze each site's issues
    for site_result in report.get("sites", []):
        site_name = site_result.get("site", "unknown")
        status = site_result.get("overall_status", "UNKNOWN")
        
        # Critical sites need immediate attention
        if status == "CRITICAL" or status == "DOWN":
            for issue in site_result.get("issues", []):
                category = issue.get("category", "Unknown")
                severity = issue.get("severity", "UNKNOWN")
                
                # Assign based on category
                if category in ["SEO", "Content", "Performance"]:
                    agent_assignments["Agent-7"]["issues"].append({
                        "site": site_name,
                        "issue": issue,
                        "status": status
                    })
                elif category in ["Health"]:
                    agent_assignments["Agent-1"]["issues"].append({
                        "site": site_name,
                        "issue": issue,
                        "status": status
                    })
                    agent_assignments["Agent-8"]["issues"].append({
                        "site": site_name,
                        "issue": issue,
                        "status": status
                    })
        
        # Sites needing attention
        elif status == "NEEDS_ATTENTION":
            for issue in site_result.get("issues", []):
                category = issue.get("category", "Unknown")
                
                if category in ["SEO", "Content", "Performance"]:
                    agent_assignments["Agent-7"]["issues"].append({
                        "site": site_name,
                        "issue": issue,
                        "status": status
                    })
                elif category in ["Security"]:
                    agent_assignments["Agent-3"]["issues"].append({
                        "site": site_name,
                        "issue": issue,
                        "status": status
                    })
    
    # Create priority actions
    critical_sites = [s for s in report.get("sites", []) if s.get("overall_status") in ["CRITICAL", "DOWN"]]
    if critical_sites:
        for site in critical_sites:
            coordination_plan["priority_actions"].append({
                "priority": "CRITICAL",
                "site": site.get("site"),
                "action": f"Fix {site.get('overall_status')} issues immediately",
                "agents": ["Agent-1", "Agent-7", "Agent-8"],
                "issues": [i.get("issue") for i in site.get("issues", [])]
            })
    
    # Count issues by category for coordination
    issues_by_category = report.get("issues_by_category", {})
    if issues_by_category.get("SEO", 0) > 0:
        coordination_plan["coordination_needs"].append({
            "type": "BATCH_FIX",
            "category": "SEO",
            "count": issues_by_category["SEO"],
            "agent": "Agent-7",
            "action": "Batch fix SEO issues (meta descriptions, title tags, H1 headings)"
        })
    
    if issues_by_category.get("Security", 0) > 0:
        coordination_plan["coordination_needs"].append({
            "type": "BATCH_FIX",
            "category": "Security",
            "count": issues_by_category["Security"],
            "agent": "Agent-3",
            "action": "Add missing security headers (HSTS, X-Frame-Options, etc.)"
        })
    
    if issues_by_category.get("Content", 0) > 0:
        coordination_plan["coordination_needs"].append({
            "type": "IMMEDIATE",
            "category": "Content",
            "count": issues_by_category["Content"],
            "agents": ["Agent-7", "Agent-8"],
            "action": "Investigate and fix empty content areas"
        })
    
    coordination_plan["agent_assignments"] = {
        agent: {
            "total_issues": len(data["issues"]),
            "sites_affected": len(set(i["site"] for i in data["issues"])),
            "priority": data["priority"],
            "issues": data["issues"][:10]  # First 10 issues
        }
        for agent, data in agent_assignments.items()
        if data["issues"]
    }
    
    return coordination_plan

def main():
    """Main execution."""
    print("=" * 70)
    print("AUDIT FIXES COORDINATION")
    print("=" * 70)
    print()
    
    audit_report = load_latest_audit_report()
    if audit_report.get("status") != "found":
        print(f"❌ Could not load audit report: {audit_report.get('status', 'unknown')}")
        return
    
    print(f"Loaded audit report: {audit_report.get('file', 'unknown')}")
    print(f"Total sites: {audit_report.get('total_sites', 0)}")
    print(f"Summary: {audit_report.get('summary', {})}")
    print()
    
    coordination_plan = analyze_issues_by_agent(audit_report)
    
    print("=" * 70)
    print("AGENT ASSIGNMENTS:")
    print("-" * 70)
    for agent, data in coordination_plan["agent_assignments"].items():
        print(f"\n{agent}:")
        print(f"  Total Issues: {data['total_issues']}")
        print(f"  Sites Affected: {data['sites_affected']}")
        print(f"  Priority: {data['priority']}")
        if data['issues']:
            print(f"  Sample Issues:")
            for issue_data in data['issues'][:3]:
                print(f"    - {issue_data['site']}: {issue_data['issue'].get('issue', 'Unknown')}")
    
    print()
    print("=" * 70)
    print("PRIORITY ACTIONS:")
    print("-" * 70)
    for action in coordination_plan["priority_actions"]:
        print(f"\n[{action['priority']}] {action['site']}:")
        print(f"  Action: {action['action']}")
        print(f"  Agents: {', '.join(action['agents'])}")
        print(f"  Issues: {len(action['issues'])}")
    
    print()
    print("=" * 70)
    print("COORDINATION NEEDS:")
    print("-" * 70)
    for need in coordination_plan["coordination_needs"]:
        print(f"\n[{need['type']}] {need['category']}:")
        print(f"  Count: {need['count']} issues")
        print(f"  Agent(s): {', '.join(need.get('agents', [need.get('agent', 'Unknown')]))}")
        print(f"  Action: {need['action']}")
    
    # Save coordination plan
    project_root = Path(__file__).parent.parent
    reports_dir = project_root / "docs" / "coordination"
    reports_dir.mkdir(parents=True, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    plan_file = reports_dir / f"audit_fixes_coordination_{timestamp}.json"
    
    with open(plan_file, 'w', encoding='utf-8') as f:
        json.dump(coordination_plan, f, indent=2)
    
    print()
    print(f"✅ Coordination plan saved to: {plan_file}")

if __name__ == "__main__":
    main()

