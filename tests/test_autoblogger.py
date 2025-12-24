import pytest
from src.autoblogger.validator import validate_markdown, ValidationResult
from src.autoblogger.models import BacklogItem, BrandProfile

def test_validate_markdown_valid():
    md = """
## Problem
We have a problem.

## Fix
Here is the fix.

## Steps
1. Do this.
2. Do that.

## Example
See this example.

## CTA
Click here for AUDIT.
"""
    result = validate_markdown(md, word_count_min=10, word_count_max=1000, cta_type="audit")
    assert result.ok
    assert len(result.errors) == 0
    assert result.word_count > 10

def test_validate_markdown_missing_section():
    md = """
## Problem
We have a problem.
"""
    result = validate_markdown(md, word_count_min=1, word_count_max=1000, cta_type="audit")
    assert not result.ok
    assert "missing required section heading: ## Fix" in result.errors

def test_validate_markdown_word_count_low():
    md = """
## Problem
## Fix
## Steps
## Example
## CTA
audit
"""
    result = validate_markdown(md, word_count_min=100, word_count_max=1000, cta_type="audit")
    assert not result.ok
    assert any("word_count" in e for e in result.errors)

def test_backlog_item_from_dict():
    data = {
        "id": "123",
        "pillar": "Tech",
        "audience": "Devs",
        "title": "How to Code",
        "angle": "Fast",
        "keywords": ["python", "code"],
        "cta": "audit",
        "status": "pending"
    }
    item = BacklogItem.from_dict(data)
    assert item.id == "123"
    assert item.pillar == "Tech"
    assert item.keywords == ["python", "code"]

def test_brand_profile_from_dict():
    data = {
        "content_rules": {
            "word_count": [500, 800]
        },
        "seo": {
            "internal_links": {"foo": "bar"}
        }
    }
    profile = BrandProfile.from_dict(data)
    assert profile.word_count_min == 500
    assert profile.word_count_max == 800
    assert profile.internal_links["foo"] == "bar"
