import json
from pathlib import Path
from datetime import datetime

REPO_ROOT = Path(__file__).resolve().parents[1]
GRADECARD_FILE = REPO_ROOT / "global_website_gradecard.json"
TASK_LIST_FILE = REPO_ROOT / "MASTER_AUDIT_TASKS.md"

def load_gradecard():
    if not GRADECARD_FILE.exists():
        return None
    with open(GRADECARD_FILE, 'r') as f:
        return json.load(f)

def generate_tasks_for_site(domain, data):
    tasks = []
    
    # Check UX
    ux_details = data.get("details", {}).get("design_ux", [])
    if data["scores"]["design_ux"] < 100:
        for detail in ux_details:
            if "Missing" in detail or "No theme found" in detail or "Not Responsive" in detail or "Legacy CSS" in detail:
                 tasks.append(f"- [ ] **UX**: Fix issue: {detail}")

    # Check SEO
    seo_details = data.get("details", {}).get("seo", [])
    if data["scores"]["seo"] < 100:
        for detail in seo_details:
             if "Missing" in detail or "No theme found" in detail:
                 tasks.append(f"- [ ] **SEO**: Fix issue: {detail}")

    # Check Professionalism
    pro_details = data.get("details", {}).get("professionalism", [])
    if data["scores"]["professionalism"] < 100:
        for detail in pro_details:
             if "Missing" in detail or "found" in detail or "No theme found" in detail:
                 # Filter out positive "found" messages if any (though logic usually puts negatives here)
                 # Actually "No Lorem Ipsum detected" contains "No", but "Lorem Ipsum found" contains "found"
                 # Let's be more specific based on the known strings from gradecard.py
                 
                 if "Lorem Ipsum found" in detail:
                     tasks.append(f"- [ ] **Content**: Remove Lorem Ipsum placeholder text")
                 elif "TODOs/FIXMEs found" in detail:
                     tasks.append(f"- [ ] **Code**: Resolve TODOs/FIXMEs in code")
                 elif "Missing Favicon" in detail:
                     tasks.append(f"- [ ] **Branding**: Add Favicon")
                 elif "No theme found" in detail:
                     tasks.append(f"- [ ] **Config**: Verify theme directory structure (Auditor could not find theme)")

    return tasks

def main():
    gradecard = load_gradecard()
    if not gradecard:
        print("❌ No gradecard found. Run tools/website_gradecard.py first.")
        return

    markdown_content = []
    markdown_content.append(f"# 📋 Master Audit Task List")
    markdown_content.append(f"Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M')}")
    markdown_content.append(f"Based on: `global_website_gradecard.json`")
    markdown_content.append("")
    markdown_content.append("> **Objective**: Improve all sites to Grade A (90+ score).")
    markdown_content.append("")

    total_tasks = 0
    
    # Sort sites by grade (F first)
    sorted_sites = sorted(gradecard["websites"].items(), key=lambda x: x[1]["total_score"])

    for domain, data in sorted_sites:
        tasks = generate_tasks_for_site(domain, data)
        if tasks:
            grade = data["grade"]
            score = data["total_score"]
            emoji = "🔴" if grade == "F" else "qh" if grade == "D" else "🟡" if grade == "C" else "🟢"
            
            markdown_content.append(f"## {emoji} {domain} (Grade: {grade} - {score}%)")
            markdown_content.extend(tasks)
            markdown_content.append("")
            total_tasks += len(tasks)

    with open(TASK_LIST_FILE, 'w') as f:
        f.write("\n".join(markdown_content))

    print(f"✅ Master Task List generated at: {TASK_LIST_FILE}")
    print(f"   Total tasks identified: {total_tasks}")

if __name__ == "__main__":
    main()
