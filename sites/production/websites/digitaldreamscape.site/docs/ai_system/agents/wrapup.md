# 🚨 **AGENT WRAPUP SEQUENCE - QUALITY ASSURANCE MANDATORY** 🚨

<<<<<<< HEAD
**Agent:** `{agent_id}`
**Session End Time:** `{timestamp}`
**Mission:** `{mission_name}`
**Status:** WRAPUP SEQUENCE INITIATED
=======
**Agent:** `{agent_id}`  
**Session End Time:** `{timestamp}`  
**Mission:** `{mission_name}`  
**Status:** WRAPUP SEQUENCE INITIATED  
>>>>>>> origin/codex/catalog-functions-in-utils-directories

---

## 🎯 **WRAPUP OBJECTIVES - IMMEDIATE EXECUTION REQUIRED**

### **1. 📋 WORK COMPLETION VALIDATION**
- **Verify all assigned tasks are complete**
- **Confirm deliverables meet acceptance criteria**
- **Document any incomplete work with status**

### **2. 🔍 DUPLICATION PREVENTION AUDIT**
- **Check for existing similar implementations**
- **Verify no duplicate functionality created**
- **Ensure single source of truth (SSOT) compliance**

### **3. 📏 CODING STANDARDS COMPLIANCE**
- **Validate against project coding standards**
- **Check file size limits (V2 compliance)**
- **Ensure proper documentation and comments**
- **Verify import organization and structure**

### **4. 🧹 TECHNICAL DEBT CLEANUP**
- **Identify and remove any technical debt created**
- **Clean up temporary files and test artifacts**
- **Ensure proper error handling and logging**
- **Validate against project architecture patterns**

---

## 🚨 **MANDATORY WRAPUP ACTIONS - EXECUTE IN ORDER**

### **ACTION 1: WORK COMPLETION AUDIT**
```bash
# Document completion status
echo "## WORK COMPLETION AUDIT - $(date)" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**Mission:** {mission_name}" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**Status:** COMPLETE/INCOMPLETE" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRUPUP_REPORT.md
echo "**Deliverables:** [List all deliverables]" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
```

### **ACTION 2: DUPLICATION PREVENTION CHECK**
```bash
# Search for potential duplicates
find . -name "*.py" -exec grep -l "similar_function_name" {} \;
find . -name "*.py" -exec grep -l "duplicate_class_name" {} \;
find . -name "*.py" -exec grep -l "redundant_import" {} \;

# Document findings
echo "## DUPLICATION AUDIT RESULTS" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**Duplicates Found:** [List any duplicates]" >> agent_workspaces/Agent-4/inbox/Agent_{agent_id}_WRAPUP_REPORT.md
echo "**SSOT Compliance:** [Yes/No with details]" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
```

### **ACTION 3: CODING STANDARDS VALIDATION**
```bash
# Check file sizes for V2 compliance
find . -name "*.py" -exec wc -l {} \; | sort -nr | head -10

# Check for proper imports and structure
find . -name "*.py" -exec grep -l "^import" {} \;
find . -name "*.py" -exec grep -l "^from" {} \;

# Document compliance status
echo "## CODING STANDARDS COMPLIANCE" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**V2 File Size Compliance:** [Yes/No with details]" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**Documentation Standards:** [Yes/No with details]" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**Import Organization:** [Yes/No with details]" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
```

### **ACTION 4: TECHNICAL DEBT CLEANUP**
```bash
# Remove temporary files
find . -name "*.tmp" -delete
find . -name "*.bak" -delete
find . -name "*.old" -delete

# Clean up test artifacts
find . -name "__pycache__" -type d -exec rm -rf {} +
find . -name "*.pyc" -delete

# Document cleanup actions
echo "## TECHNICAL DEBT CLEANUP" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**Files Cleaned:** [List cleaned files]" >> agent_workspaces/Agent-4/inbox/AGENT_{agent_id}_WRAPUP_REPORT.md
echo "**Technical Debt Removed:** [List removed debt]" >> agent_workspaces/Agent-4/inbox/Agent_{agent_id}_WRAPUP_REPORT.md
```

### **ACTION 5: FINAL STATUS UPDATE**
```bash
# Update status.json with wrapup completion
echo '{"last_updated": "'$(date)'", "status": "Wrapup sequence completed", "fsm_state": "completed", "mission": "{mission_name}", "wrapup_status": "complete"}' > status.json

# Log to devlog
python scripts/devlog.py "Wrapup Sequence Completed" "Agent-{agent_id} completed wrapup sequence for {mission_name}. All quality checks passed."

# Commit wrapup completion
git add . && git commit -m "Agent-{agent_id}: Wrapup sequence completed for {mission_name} - Quality assurance validated" && git push
```

---

## 📊 **WRAPUP SUCCESS CRITERIA**

### **✅ ALL CRITERIA MUST BE MET:**

1. **Work Completion:** 100% of assigned tasks documented as complete
2. **Duplication Prevention:** Zero duplicate implementations found
3. **Coding Standards:** 100% V2 compliance achieved
4. **Technical Debt:** Zero new technical debt introduced
5. **Documentation:** Complete wrapup report submitted to Captain
6. **Status Update:** status.json updated with wrapup completion
7. **Devlog Entry:** Activity logged to Discord devlog system
8. **Repository Commit:** All changes committed and pushed

---

## 🚨 **FAILURE CONSEQUENCES**

### **⚠️ INCOMPLETE WRAPUP RESULTS IN:**
- **Session not marked as complete**
- **Quality assurance failure report**
- **Required rework and validation**
- **Potential role reassignment for repeated failures**
- **Suspension from contract claim system access**

---

## 📋 **WRAPUP REPORT TEMPLATE**

### **REQUIRED SECTIONS:**
1. **Work Completion Audit** - Task status and deliverables
2. **Duplication Audit Results** - SSOT compliance verification
3. **Coding Standards Compliance** - V2 compliance status
4. **Technical Debt Cleanup** - Cleanup actions taken
5. **Quality Assurance Summary** - Overall compliance status
6. **Next Steps** - Recommendations for future sessions

---

## 🎖️ **CAPTAIN'S MANDATORY NEXT ACTIONS**

**AFTER SENDING THIS WRAPUP MESSAGE, YOU MUST:**

1. **EXECUTE ALL WRAPUP ACTIONS** in the exact order specified
2. **DOCUMENT EVERY ACTION** in the wrapup report
3. **SUBMIT COMPLETE REPORT** to Captain's inbox
4. **UPDATE YOUR STATUS** to reflect wrapup completion
5. **COMMIT ALL CHANGES** to the repository
6. **LOG ACTIVITY** to the Discord devlog system

**FAILURE TO COMPLETE WRAPUP SEQUENCE = QUALITY ASSURANCE VIOLATION**

---

**Captain Agent-4 - Strategic Oversight & Emergency Intervention Manager** ✅
