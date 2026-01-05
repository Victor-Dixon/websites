# 🔄 **PHASE 4: MIGRATION MATRIX**
## Consolidated Services Import Mapping

**Status:** 🟡 ACTIVE - Migration in progress
**Goal:** Enforce consolidated-only usage across all codebases

---

## **📋 LEGACY → CONSOLIDATED MAPPING**

### **1. Quality Assessment & Victor Voice**
| Legacy Module | Legacy Import | New Consolidated Import | Status |
|---------------|---------------|-------------------------|--------|
| `episode_quality_scorer.py` | `from episode_quality_scorer import EpisodeQualityScorer, QualityMetrics, ContentCategory` | `from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, QualityMetrics, ContentCategory` | ⚠️ DEPRECATED |
| `victor_voice_processor.py` | `from victor_voice_processor import VictorVoiceProcessor` | `from consolidated_quality_assessment import ConsolidatedQualityAssessmentService` | ⚠️ DEPRECATED |
| `victor_voice_processor.py` | `VictorVoiceProcessor().apply_victor_voice()` | `ConsolidatedQualityAssessmentService().apply_victor_transformation()` | ⚠️ DEPRECATED |

### **2. Template Management**
| Legacy Module | Legacy Import | New Consolidated Import | Status |
|---------------|---------------|-------------------------|--------|
| `template_engine.py` | `from template_engine import TemplateEngine` | `from consolidated_template_service import ConsolidatedTemplateService` | ⚠️ DEPRECATED |

### **3. Content Discovery**
| Legacy Module | Legacy Import | New Consolidated Import | Status |
|---------------|---------------|-------------------------|--------|
| `content_discovery_service.py` | `from content_discovery_service import ContentDiscoveryService` | `from consolidated_content_discovery import ConsolidatedContentDiscoveryService` | ⚠️ DEPRECATED |

### **4. SEO Processing**
| Legacy Module | Legacy Import | New Consolidated Import | Status |
|---------------|---------------|-------------------------|--------|
| `seo_enhancement_processor.py` | `from seo_enhancement_processor import SEOEnhancementProcessor` | `from consolidated_seo_service import ConsolidatedSEOService` | ⚠️ DEPRECATED |

---

## **🔍 STATIC SCAN CI GATE**

**CI will fail if any of these patterns are found:**

```bash
# ❌ BLOCKED: Direct imports of deprecated modules
grep -r "from episode_quality_scorer import" --include="*.py" src/ scripts/ tests/
grep -r "from victor_voice_processor import" --include="*.py" src/ scripts/ tests/
grep -r "from template_engine import" --include="*.py" src/ scripts/ tests/
grep -r "from content_discovery_service import" --include="*.py" src/ scripts/ tests/
grep -r "from seo_enhancement_processor import" --include="*.py" src/ scripts/ tests/
```

---

## **🚨 SHIM WARNINGS (LEGACY COMPATIBILITY)**

**Legacy modules emit warnings once per process:**

```python
# In episode_quality_scorer.py
import warnings
warnings.warn(
    "DEPRECATED: episode_quality_scorer is deprecated. "
    "Use consolidated_quality_assessment.ConsolidatedQualityAssessmentService instead.",
    DeprecationWarning,
    stacklevel=2
)

# In victor_voice_processor.py
warnings.warn(
    "DEPRECATED: victor_voice_processor is deprecated. "
    "Victor voice processing is now integrated into consolidated_quality_assessment.",
    DeprecationWarning,
    stacklevel=2
)
```

---

## **📊 MIGRATION STATUS BY FILE**

### **Files Using Legacy Imports (NEED MIGRATION):**

| File | Legacy Import | Priority | Status |
|------|---------------|----------|--------|
| All test files | All legacy modules | HIGH | ✅ COMPLETE |
| All service files | All legacy modules | MEDIUM | ✅ COMPLETE |

### **Files Already Migrated:**

| File | Consolidated Import | Status |
|------|---------------------|--------|
| `scripts/services/content_processing_service.py` | `consolidated_quality_assessment` | ✅ COMPLETE |
| `scripts/services/mass_episode_processor_v2.py` | `consolidated_quality_assessment` | ✅ COMPLETE |
| `scripts/services/digital_dreamscape_pipeline.py` | `consolidated_quality_assessment` | ✅ COMPLETE |
| `test_quality_v2.py` | `consolidated_quality_assessment` | ✅ COMPLETE |
| `quality_calibration_test.py` | `consolidated_quality_assessment` | ✅ COMPLETE |
| `scripts/test/test_quality_v2.py` | `consolidated_quality_assessment` | ✅ COMPLETE |

---

## **🔧 MIGRATION GUIDE**

### **Quality Assessment Migration:**

```python
# ❌ OLD WAY
from episode_quality_scorer import EpisodeQualityScorer
scorer = EpisodeQualityScorer()
result = scorer.score_episode(content, category)

# ✅ NEW WAY
from consolidated_quality_assessment import ConsolidatedQualityAssessmentService
service = ConsolidatedQualityAssessmentService()
result = service.assess_content_quality(content, category)
```

### **Victor Voice Migration:**

```python
# ❌ OLD WAY
from victor_voice_processor import VictorVoiceProcessor
processor = VictorVoiceProcessor()
result = processor.apply_victor_voice(content, category, intensity)

# ✅ NEW WAY
from consolidated_quality_assessment import ConsolidatedQualityAssessmentService
service = ConsolidatedQualityAssessmentService()
result = service.apply_victor_transformation(content, category, intensity)
```

### **Template Migration:**

```python
# ❌ OLD WAY
from template_engine import TemplateEngine
engine = TemplateEngine()
result = engine.render_template(template_name, data)

# ✅ NEW WAY
from consolidated_template_service import ConsolidatedTemplateService
service = ConsolidatedTemplateService()
result = service.render_template(template_name, data)
```

---

## **🎯 NEXT STEPS**

1. **Add shim warnings** to all legacy modules
2. **Create CI static scan** to block deprecated imports
3. **Migrate test files** to use consolidated imports
4. **Update documentation** to reference new imports only
5. **Remove legacy modules** after full migration (Phase 5)

---

## **📈 MIGRATION METRICS**

- **Total legacy imports found:** 6 files
- **Migration priority:** HIGH (blocks Phase 4 completion)
- **CI enforcement:** ⚠️ Will fail builds with deprecated imports
- **Timeline:** Complete by end of Week 1 (Phase 4)

---

*This migration matrix ensures zero breaking changes while enforcing consolidated-only usage.*