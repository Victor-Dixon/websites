#!/usr/bin/env python3
"""
Verify All Sites - Nightly Verification Script

Checks deploy stamps for all sites in registry.
Fails if any site is not verified or commit doesn't match.

Author: Agent-2
"""

import argparse
import json
import sys
from pathlib import Path

# Add current directory to path
sys.path.insert(0, str(Path(__file__).parent))

from registry_deployer import SiteRegistry, DeployVerifier

def main():
    """Verify all sites."""
    parser = argparse.ArgumentParser(description="Verify all sites in registry")
    parser.add_argument("--sha", "--commit", dest="commit", help="Expected commit hash (for strict matching)")
    parser.add_argument("--strict", action="store_true", help="Require commit match (fails on mismatch)")
    
    args = parser.parse_args()
    
    registry = SiteRegistry()
    config = registry.config
    
    # Get verifier settings from config
    verifier = DeployVerifier(
        timeout=config.get('verification_timeout', 30),
        delay=config.get('verification_delay', 5),
        retries=config.get('retry_attempts', 3),
        backoff=config.get('retry_backoff', 2)
    )
    
    enabled_sites = registry.get_enabled_sites()
    print(f"🔍 Verifying {len(enabled_sites)} sites...\n")
    
    # Get expected commit
    expected_commit = args.commit or "unknown"
    require_match = args.strict or config.get('require_commit_match', True)
    
    if require_match and expected_commit == "unknown":
        print("⚠️  Warning: --strict requires --sha/--commit, but none provided")
        print("   Verification will check stamp existence only\n")
        require_match = False
    
    results = {}
    all_verified = True
    
    for site_key, site_config in enabled_sites.items():
        verify_url = site_config.get('verify_url')
        if not verify_url:
            print(f"⚠️  {site_key}: No verify_url configured")
            results[site_key] = {"verified": False, "error": "No verify_url"}
            all_verified = False
            continue
        
        print(f"Checking {site_key}...", end=" ")
        result = verifier.verify(site_key, expected_commit, verify_url, require_commit_match=require_match)
        results[site_key] = result
        
        if result.get("verified"):
            if require_match and result.get("matches"):
                print(f"✅ Verified (commit: {result.get('actual_commit', 'unknown')[:7]})")
            elif not require_match:
                print("✅ Verified (stamp exists)")
            else:
                print(f"❌ Commit mismatch")
                all_verified = False
        else:
            print(f"❌ Not verified: {result.get('error', 'Unknown error')}")
            all_verified = False
    
    verified_count = sum(1 for r in results.values() if r.get("verified"))
    print(f"\n📊 Summary: {verified_count}/{len(results)} sites verified")
    
    if not all_verified:
        print("\n❌ Some sites failed verification")
        sys.exit(1)
    else:
        print("\n✅ All sites verified")
        sys.exit(0)

if __name__ == "__main__":
    main()
