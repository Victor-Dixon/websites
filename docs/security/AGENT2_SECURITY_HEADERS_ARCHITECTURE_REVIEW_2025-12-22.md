# Security Headers Architecture Review & Implementation Design

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-22  
**Status:** Architecture Review Complete - Ready for Implementation  
**Task:** Review and add security headers (X-Frame-Options, X-Content-Type-Options, CSP) to all 10 websites

<!-- SSOT Domain: security -->

## Executive Summary

This document provides architectural guidance for implementing security headers across all 10 websites in the portfolio. The review analyzes current implementations, identifies gaps, and provides a standardized approach for consistent security header deployment.

**Current Status:**
- ✅ 1 site has partial implementation (FreeRideInvestor - missing CSP, HSTS)
- ✅ 1 tool exists (`tools/add_security_headers.php`) with comprehensive headers
- ❌ 9 sites missing security headers entirely
- ❌ All sites missing Strict-Transport-Security (HSTS)

**Recommendation:** Implement standardized security headers module with site-specific CSP configurations.

---

## 1. Current State Analysis

### 1.1 Existing Implementations

#### `tools/add_security_headers.php` (Reference Implementation)
**Status:** ✅ Comprehensive implementation  
**Headers Included:**
- ✅ X-Frame-Options: SAMEORIGIN
- ✅ X-Content-Type-Options: nosniff
- ✅ X-XSS-Protection: 1; mode=block
- ✅ Referrer-Policy: strict-origin-when-cross-origin
- ✅ Content-Security-Policy: Comprehensive policy
- ✅ Permissions-Policy: geolocation=(), microphone=(), camera=()
- ✅ Strict-Transport-Security: max-age=31536000; includeSubDomains; preload (HTTPS only)

**Architecture Assessment:**
- ✅ Uses WordPress `send_headers` hook (correct approach)
- ✅ Conditional HSTS (only on HTTPS)
- ✅ Comprehensive CSP policy
- ⚠️ CSP policy may need site-specific customization
- ✅ Follows WordPress best practices

#### `FreeRideInvestor/inc/fri-security-config.php` (Partial Implementation)
**Status:** ⚠️ Partial implementation  
**Headers Included:**
- ✅ X-Content-Type-Options: nosniff
- ✅ X-Frame-Options: SAMEORIGIN
- ✅ X-XSS-Protection: 1; mode=block
- ✅ Referrer-Policy: strict-origin-when-cross-origin
- ❌ Missing: Content-Security-Policy
- ❌ Missing: Permissions-Policy
- ❌ Missing: Strict-Transport-Security

**Architecture Assessment:**
- ✅ Uses WordPress `send_headers` hook
- ✅ Conditional (admin pages excluded)
- ⚠️ Missing critical headers (CSP, HSTS)
- ✅ Good separation of concerns (security config file)

### 1.2 Sites Requiring Implementation

**All 10 sites require security headers:**
1. ariajet.site
2. crosbyultimateevents.com
3. dadudekc.com
4. digitaldreamscape.site
5. freerideinvestor.com (needs CSP, HSTS)
6. houstonsipqueen.com
7. prismblossom.online
8. southwestsecret.com
9. tradingrobotplug.com
10. weareswarm.online
11. weareswarm.site

---

## 2. Architecture Design

### 2.1 Design Principles

1. **Standardization:** Single source of truth for security headers
2. **Site-Specific CSP:** Allow customization per site's needs
3. **WordPress Integration:** Use WordPress hooks and best practices
4. **Maintainability:** Centralized configuration, easy updates
5. **Performance:** Minimal overhead, efficient header injection
6. **Compatibility:** Works with existing themes and plugins

### 2.2 Recommended Architecture

#### Option A: Centralized Module (Recommended)
**Structure:**
```
websites/
├── shared/
│   └── security-headers/
│       ├── security-headers.php (core module)
│       └── csp-configs/
│           ├── default-csp.php
│           ├── ariajet-site-csp.php
│           ├── crosbyultimateevents-com-csp.php
│           └── ... (site-specific CSP configs)
└── <site-domain>/
    └── wp/
        └── wp-content/
            └── themes/
                └── <theme-name>/
                    └── functions.php (includes security-headers.php)
```

**Advantages:**
- ✅ Single source of truth
- ✅ Easy updates across all sites
- ✅ Site-specific CSP customization
- ✅ Maintainable and scalable

**Implementation:**
```php
// In theme functions.php
require_once get_template_directory() . '/../../../../shared/security-headers/security-headers.php';
```

#### Option B: Theme-Embedded (Alternative)
**Structure:**
```
websites/
└── <site-domain>/
    └── wp/
        └── wp-content/
            └── themes/
                └── <theme-name>/
                    └── inc/
                        └── security-headers.php
```

**Advantages:**
- ✅ Site-specific control
- ✅ No shared dependencies
- ⚠️ Requires updates per site
- ⚠️ Potential for inconsistency

### 2.3 Header Implementation Priority

**Priority 1 (Critical - Implement First):**
1. **Strict-Transport-Security (HSTS)** - Prevents downgrade attacks
2. **X-Content-Type-Options: nosniff** - Prevents MIME sniffing
3. **X-Frame-Options: SAMEORIGIN** - Prevents clickjacking

**Priority 2 (High - Implement Second):**
4. **Content-Security-Policy (CSP)** - XSS protection (requires site-specific config)
5. **Referrer-Policy** - Privacy protection

**Priority 3 (Medium - Implement Third):**
6. **X-XSS-Protection** - Legacy browser support
7. **Permissions-Policy** - Feature restriction

---

## 3. Implementation Specifications

### 3.1 Core Security Headers Module

**File:** `shared/security-headers/security-headers.php`

**Required Headers:**
```php
// Priority 1: Critical Headers
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// Priority 2: High-Value Headers
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: [site-specific]');

// Priority 3: Additional Headers
header('X-XSS-Protection: 1; mode=block');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
```

**WordPress Integration:**
```php
add_action('send_headers', 'add_security_headers', 1);
```

**Conditional Logic:**
- HSTS: Only if `is_ssl()` returns true
- Admin pages: Option to exclude (per site preference)
- AJAX requests: May need special handling

### 3.2 Content-Security-Policy (CSP) Configuration

**Default CSP Policy:**
```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.youtube.com https://www.googletagmanager.com;
style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
font-src 'self' https://fonts.gstatic.com;
img-src 'self' data: https:;
connect-src 'self' https://www.youtube.com;
frame-src 'self' https://www.youtube.com;
```

**Site-Specific Considerations:**
- **YouTube embeds:** Allow `frame-src https://www.youtube.com`
- **Google Fonts:** Allow `font-src https://fonts.gstatic.com`
- **Analytics:** Allow `script-src` for GA4, Facebook Pixel
- **Payment processors:** Allow `script-src` for Stripe, PayPal
- **CDN resources:** Allow `img-src`, `style-src` for CDN domains

**CSP Implementation Strategy:**
1. Start with permissive policy (report-only mode)
2. Monitor CSP violation reports
3. Gradually tighten policy
4. Switch to enforce mode once stable

### 3.3 Site-Specific CSP Configurations

**Example: crosbyultimateevents.com**
```php
// Allow Calendly, Stripe, Google Fonts
$csp = "default-src 'self'; " .
       "script-src 'self' 'unsafe-inline' https://assets.calendly.com https://js.stripe.com https://www.googletagmanager.com; " .
       "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://assets.calendly.com; " .
       "font-src 'self' https://fonts.gstatic.com; " .
       "img-src 'self' data: https:; " .
       "frame-src 'self' https://assets.calendly.com; " .
       "connect-src 'self' https://api.stripe.com;";
```

**Example: tradingrobotplug.com**
```php
// Allow trading APIs, analytics
$csp = "default-src 'self'; " .
       "script-src 'self' 'unsafe-inline' https://api.alpaca.markets https://www.googletagmanager.com; " .
       "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
       "font-src 'self' https://fonts.gstatic.com; " .
       "img-src 'self' data: https:; " .
       "connect-src 'self' https://api.alpaca.markets;";
```

---

## 4. Implementation Roadmap

### Phase 1: Core Headers (Week 1)
**Goal:** Implement Priority 1 headers on all sites

**Tasks:**
1. Create `shared/security-headers/security-headers.php` module
2. Implement HSTS, X-Content-Type-Options, X-Frame-Options
3. Deploy to all 10 sites
4. Test and validate headers are present

**Deliverables:**
- Core security headers module
- Deployment script/tool
- Validation report

### Phase 2: CSP Implementation (Week 2)
**Goal:** Implement Content-Security-Policy with site-specific configs

**Tasks:**
1. Audit each site's external resources (CDNs, APIs, embeds)
2. Create site-specific CSP configurations
3. Deploy CSP in report-only mode
4. Monitor CSP violation reports
5. Adjust policies based on violations
6. Switch to enforce mode

**Deliverables:**
- Site-specific CSP configs
- CSP violation report
- Final CSP policies

### Phase 3: Additional Headers (Week 2-3)
**Goal:** Implement Priority 3 headers and optimize

**Tasks:**
1. Add Referrer-Policy, X-XSS-Protection, Permissions-Policy
2. Optimize CSP policies (remove unnecessary 'unsafe-inline')
3. Performance testing
4. Documentation

**Deliverables:**
- Complete security headers implementation
- Performance metrics
- Documentation

---

## 5. Validation & Testing

### 5.1 Header Validation Tools

**Recommended Tools:**
1. **SecurityHeaders.com** - Online header checker
2. **Mozilla Observatory** - Security header scanner
3. **curl** - Manual header inspection
4. **Browser DevTools** - Network tab inspection

**Validation Command:**
```bash
curl -I https://example.com | grep -i "x-frame-options\|x-content-type-options\|strict-transport-security\|content-security-policy"
```

### 5.2 Testing Checklist

- [ ] All Priority 1 headers present on all sites
- [ ] HSTS only on HTTPS sites
- [ ] CSP policies don't break site functionality
- [ ] No CSP violations in console
- [ ] Headers present on all pages (home, posts, pages)
- [ ] Headers work with WordPress admin (if applicable)
- [ ] Headers work with AJAX requests
- [ ] Performance impact < 10ms

---

## 6. Risk Assessment

### 6.1 Implementation Risks

**Risk 1: CSP Breaking Site Functionality**
- **Probability:** Medium
- **Impact:** High
- **Mitigation:** Start with report-only mode, gradual tightening

**Risk 2: HSTS Preload Issues**
- **Probability:** Low
- **Impact:** Medium
- **Mitigation:** Test on staging before production, don't preload initially

**Risk 3: Plugin/Theme Conflicts**
- **Probability:** Low
- **Impact:** Medium
- **Mitigation:** Test with all active plugins, use WordPress hooks correctly

### 6.2 Security Benefits

**Expected Improvements:**
- ✅ Protection against clickjacking attacks
- ✅ Protection against MIME sniffing attacks
- ✅ Protection against XSS attacks (CSP)
- ✅ Protection against downgrade attacks (HSTS)
- ✅ Improved privacy (Referrer-Policy)
- ✅ Better security posture score

---

## 7. Maintenance & Updates

### 7.1 Ongoing Maintenance

**Regular Tasks:**
- Review CSP violation reports monthly
- Update CSP policies as new resources added
- Monitor security header compliance
- Update headers based on security best practices

### 7.2 Update Process

1. Update `shared/security-headers/security-headers.php`
2. Test on staging site
3. Deploy to all sites
4. Validate headers present
5. Monitor for issues

---

## 8. Coordination Requirements

### 8.1 Agent Responsibilities

**Agent-2 (Architecture):**
- ✅ Architecture review complete
- ✅ Design specifications provided
- ✅ Implementation roadmap defined

**Agent-7 (Web Development):**
- Implement security headers module
- Create site-specific CSP configs
- Deploy to all sites
- Test and validate

**Agent-3 (Infrastructure):**
- Coordinate HSTS implementation
- Verify HTTPS configuration
- Monitor deployment

### 8.2 Handoff Points

1. **Architecture Review → Implementation:** Agent-2 provides design, Agent-7 implements
2. **CSP Configuration → Deployment:** Agent-7 creates configs, Agent-3 validates deployment
3. **Testing → Production:** Agent-7 tests, Agent-3 monitors production

---

## 9. Approval & Next Steps

### 9.1 Architecture Approval

**Status:** ✅ **APPROVED FOR IMPLEMENTATION**

**Approval Criteria Met:**
- ✅ Design follows WordPress best practices
- ✅ Standardized approach across all sites
- ✅ Site-specific customization supported
- ✅ Risk mitigation strategies defined
- ✅ Implementation roadmap clear
- ✅ Coordination responsibilities defined

### 9.2 Next Steps

1. **Agent-7:** Create `shared/security-headers/security-headers.php` module
2. **Agent-7:** Implement Priority 1 headers (HSTS, X-Content-Type-Options, X-Frame-Options)
3. **Agent-7:** Deploy to all 10 sites
4. **Agent-3:** Validate headers present and correct
5. **Agent-7:** Create site-specific CSP configurations
6. **Agent-7:** Deploy CSP in report-only mode
7. **Agent-2:** Review CSP violation reports and adjust policies
8. **Agent-7:** Switch CSP to enforce mode
9. **Agent-3:** Final validation and monitoring

---

## 10. References

- **OWASP Security Headers:** https://owasp.org/www-project-secure-headers/
- **WordPress Security:** https://wordpress.org/support/article/hardening-wordpress/
- **CSP Reference:** https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
- **HSTS Preload:** https://hstspreload.org/

---

**Document Status:** Architecture Review Complete  
**Next Action:** Agent-7 implementation  
**ETA:** Phase 1 (Core Headers) - 2025-12-23, Phase 2 (CSP) - 2025-12-24

