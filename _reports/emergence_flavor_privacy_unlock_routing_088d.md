# Emergence Flavor Privacy + Unlock Routing 088d

## Result

- Public UI no longer reveals which domains unlocked.
- Public UI shows generic unlocked flavor blocks.
- Internal domain-to-flavor routing is preserved.
- Single-domain fixtures pass for all eight domains.
- Co-primary fixtures pass for:
  - Titan + Velocity
  - Energy + Specter
  - Duality + Mind
  - Omni + Primal

## Co-primary Test Definition

A valid co-primary test is generated from the actual scoring key, not from fixed letter assumptions.

The test passes when:

1. both target domains manifest,
2. both target domains cross the manifest threshold,
3. both target domains resolve to the same tier,
4. Pass 1 returns no powers,
5. public UI does not reveal which domains unlocked.

## Smoke Output

```text
DOMAIN_KEY_NORMALIZED=PASS
== PUBLIC PRIVACY STATIC CHECK ==
PUBLIC_DOMAIN_ROUTING_PRIVACY=PASS
== SINGLE DOMAIN FIXTURES ==
DOMAIN_FIXTURE_Titan=PASS manifested=Titan scores={'Titan': 31, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 5, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
DOMAIN_FIXTURE_Velocity=PASS manifested=Velocity scores={'Titan': 0, 'Velocity': 31, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 5, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
DOMAIN_FIXTURE_Energy=PASS manifested=Energy scores={'Titan': 0, 'Velocity': 0, 'Energy': 31, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 5, 'Specter': 1, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
DOMAIN_FIXTURE_Specter=PASS manifested=Specter scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 31, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 5, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
DOMAIN_FIXTURE_Duality=PASS manifested=Duality scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 31, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 5, 'Omni': 1, 'Primal': 1, 'Mind': 1}
DOMAIN_FIXTURE_Omni=PASS manifested=Omni scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 31, 'Primal': 0, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 5, 'Primal': 1, 'Mind': 1}
DOMAIN_FIXTURE_Primal=PASS manifested=Primal scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 31, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 1, 'Primal': 5, 'Mind': 1}
DOMAIN_FIXTURE_Mind=PASS manifested=Mind scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 31} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 5}
== CO-PRIMARY FIXTURES ==
COPRIMARY_FIXTURE_FOUND_Titan_Velocity=PASS local={'Titan': 17, 'Velocity': 17, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} remote_scores={'Titan': 17, 'Velocity': 17, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 3, 'Velocity': 3, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
COPRIMARY_FIXTURE_Titan_Velocity=PASS manifested=Titan,Velocity scores={'Titan': 17, 'Velocity': 17, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 3, 'Velocity': 3, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
COPRIMARY_ASSERT_Titan_Velocity=PASS
COPRIMARY_FIXTURE_FOUND_Energy_Specter=PASS local={'Titan': 0, 'Velocity': 0, 'Energy': 17, 'Specter': 17, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} remote_scores={'Titan': 0, 'Velocity': 0, 'Energy': 17, 'Specter': 17, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 3, 'Specter': 3, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
COPRIMARY_FIXTURE_Energy_Specter=PASS manifested=Energy,Specter scores={'Titan': 0, 'Velocity': 0, 'Energy': 17, 'Specter': 17, 'Duality': 0, 'Omni': 0, 'Primal': 0, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 3, 'Specter': 3, 'Duality': 1, 'Omni': 1, 'Primal': 1, 'Mind': 1}
COPRIMARY_ASSERT_Energy_Specter=PASS
COPRIMARY_FIXTURE_FOUND_Duality_Mind=PASS local={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 16, 'Omni': 0, 'Primal': 0, 'Mind': 16} remote_scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 16, 'Omni': 0, 'Primal': 0, 'Mind': 16} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 3, 'Omni': 1, 'Primal': 1, 'Mind': 3}
COPRIMARY_FIXTURE_Duality_Mind=PASS manifested=Duality,Mind scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 16, 'Omni': 0, 'Primal': 0, 'Mind': 16} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 3, 'Omni': 1, 'Primal': 1, 'Mind': 3}
COPRIMARY_ASSERT_Duality_Mind=PASS
COPRIMARY_FIXTURE_FOUND_Omni_Primal=PASS local={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 17, 'Primal': 17, 'Mind': 0} remote_scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 17, 'Primal': 17, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 3, 'Primal': 3, 'Mind': 1}
COPRIMARY_FIXTURE_Omni_Primal=PASS manifested=Omni,Primal scores={'Titan': 0, 'Velocity': 0, 'Energy': 0, 'Specter': 0, 'Duality': 0, 'Omni': 17, 'Primal': 17, 'Mind': 0} tiers={'Titan': 1, 'Velocity': 1, 'Energy': 1, 'Specter': 1, 'Duality': 1, 'Omni': 3, 'Primal': 3, 'Mind': 1}
COPRIMARY_ASSERT_Omni_Primal=PASS
FLAVOR_BLOCK_NAMES_HIDDEN=PASS
EMERGENCE_FLAVOR_PRIVACY_UNLOCK_ROUTING=PASS
```

STATUS=PASS
