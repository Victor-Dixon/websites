/* DreamOS premium Spark hero cards v1 */
(function () {
  "use strict";

  const savedKey = "dreamos.savedSparkCharacters.v1";
  const currentKey = "dreamos.currentSparkCharacter.v1";
  const tiers = ["Common", "Rare", "Epic", "Legendary", "Mythic", "Transcendent"];
  const palettes = {
    titan: ["#ffb86c", "#ff4f6d", 23],
    velocity: ["#70f7ff", "#7dffbe", 178],
    energy: ["#ffe66d", "#ff4fbd", 315],
    specter: ["#c9a7ff", "#5de4ff", 254],
    duality: ["#ffffff", "#8d5cff", 273],
    omni: ["#9da7ff", "#7dffbe", 215],
    primal: ["#7dff7a", "#ffcf5a", 112],
    mind: ["#ff8df3", "#7bb7ff", 292],
    default: ["#7dffbe", "#9da7ff", 188]
  };
  const titleWords = {
    Titan: "Aegis",
    Velocity: "Comet",
    Energy: "Nova",
    Specter: "Wraith",
    Duality: "Eclipse",
    Omni: "Paragon",
    Primal: "Apex",
    Mind: "Oracle"
  };

  function esc(value) {
    return String(value || "").replace(/[&<>"']/g, function (char) {
      return {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[char];
    });
  }

  function attr(value) {
    return esc(value).replace(/`/g, "&#96;");
  }

  function slug(value) {
    return String(value || "spark").toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "") || "spark";
  }

  function hash(value) {
    let out = 2166136261;
    const text = String(value || "");
    for (let i = 0; i < text.length; i += 1) {
      out ^= text.charCodeAt(i);
      out += (out << 1) + (out << 4) + (out << 7) + (out << 8) + (out << 24);
    }
    return Math.abs(out >>> 0);
  }

  function listFrom(value) {
    if (Array.isArray(value)) return value.map(normalizePowerName).filter(Boolean);
    if (!value) return [];
    return String(value).split(/[,|/]+/).map(function (item) { return item.trim(); }).filter(Boolean);
  }

  function normalizePowerName(value) {
    if (!value) return "";
    if (typeof value === "object") return value.name || value.title || value.power || value.label || "";
    return String(value);
  }

  function firstValue(payload, keys) {
    for (let i = 0; i < keys.length; i += 1) {
      const key = keys[i];
      if (payload && payload[key]) return payload[key];
    }
    return "";
  }

  function nestedImage(payload) {
    const image = payload && (payload.image || payload.artwork || payload.portrait || payload.character_art);
    if (!image) return "";
    if (typeof image === "string") return image;
    return image.url || image.src || image.data || "";
  }

  function ratingValue(value) {
    const text = String(value || "");
    const direct = text.match(/\d+(?:\.\d+)?/);
    if (direct) {
      const num = Number(direct[0]);
      return num <= 10 ? num * 10 : Math.min(num, 100);
    }
    const upper = text.toUpperCase();
    if (upper.indexOf("OMEGA") !== -1 || upper.indexOf("S") !== -1) return 96;
    if (upper.indexOf("A") !== -1) return 88;
    if (upper.indexOf("B") !== -1) return 76;
    if (upper.indexOf("C") !== -1) return 62;
    if (upper.indexOf("D") !== -1) return 48;
    return 50;
  }

  function rarityFor(power, combat, domains, payload) {
    const explicit = firstValue(payload, ["rarity_tier", "rarity", "tier"]);
    const normalized = tiers.find(function (tier) {
      return tier.toLowerCase() === String(explicit || "").toLowerCase();
    });
    if (normalized) return normalized;
    const score = ratingValue(power) + ratingValue(combat) + Math.min(domains.length, 5) * 8;
    if (score >= 196) return "Transcendent";
    if (score >= 174) return "Mythic";
    if (score >= 150) return "Legendary";
    if (score >= 124) return "Epic";
    if (score >= 94) return "Rare";
    return "Common";
  }

  function paletteFor(domains, seed) {
    const key = slug(domains[0] || "default");
    const base = palettes[key] || palettes.default;
    const hue = (base[2] + (seed % 38) - 19 + 360) % 360;
    return { accent: base[0], accent2: base[1], hue: hue };
  }

  function nameFor(payload, domains, seed) {
    const explicit = firstValue(payload, ["character_name", "hero_name", "hero", "alias", "name", "title"]);
    if (explicit) return String(explicit);
    const lead = payload.lead_domain || domains[0] || "Spark";
    const suffix = titleWords[lead] || titleWords[String(lead).replace(/\s+.*/, "")] || "Vanguard";
    const variant = ["Prime", "Zero", "Arc", "Vow", "Flux", "Crown"][seed % 6];
    return "The " + lead + " " + suffix + " " + variant;
  }

  function artworkSvg(card) {
    const domains = card.domains.length ? card.domains : ["Spark"];
    const initials = domains.slice(0, 3).map(function (domain) {
      return String(domain).trim().slice(0, 2).toUpperCase();
    }).join(" ");
    const hue = card.palette.hue;
    const svg = [
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 1260" role="img" aria-label="',
      esc(card.character_name),
      ' artwork"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop stop-color="hsl(',
      hue,
      ',95%,58%)"/><stop offset=".52" stop-color="#11172b"/><stop offset="1" stop-color="hsl(',
      (hue + 88) % 360,
      ',95%,62%)"/></linearGradient><radialGradient id="r" cx=".5" cy=".32" r=".58"><stop stop-color="#fff" stop-opacity=".72"/><stop offset=".32" stop-color="hsl(',
      hue,
      ',100%,72%)" stop-opacity=".38"/><stop offset="1" stop-color="#03040a" stop-opacity="0"/></radialGradient><filter id="glow"><feGaussianBlur stdDeviation="12" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><rect width="900" height="1260" fill="url(#g)"/><rect width="900" height="1260" fill="url(#r)"/><g opacity=".18">',
      '<path d="M0 240h900M0 360h900M0 480h900M0 600h900M0 720h900M0 840h900M0 960h900"/>',
      '<path d="M120 0v1260M300 0v1260M480 0v1260M660 0v1260M840 0v1260"/></g>',
      '<g filter="url(#glow)"><path d="M450 176l78 178 193 20-145 130 42 190-168-98-168 98 42-190-145-130 193-20z" fill="#fff" opacity=".17"/>',
      '<path d="M446 338c-122 0-212 101-212 238 0 114 50 189 129 226l-56 205h286l-55-205c80-37 130-112 130-226 0-137-92-238-222-238z" fill="#050713" opacity=".78"/>',
      '<path d="M282 669c84 53 251 53 336 0" fill="none" stroke="#fff" stroke-width="20" opacity=".22"/>',
      '<path d="M382 503h136l-68 121z" fill="hsl(',
      hue,
      ',100%,65%)" opacity=".9"/></g>',
      '<text x="450" y="1085" text-anchor="middle" fill="#fff" font-family="Arial, sans-serif" font-size="76" font-weight="900" letter-spacing="8">',
      esc(initials),
      '</text><text x="450" y="1150" text-anchor="middle" fill="#ffffffcc" font-family="Arial, sans-serif" font-size="27" font-weight="800" letter-spacing="5">',
      esc(card.rarity_tier.toUpperCase()),
      '</text></svg>'
    ].join("");
    return "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg);
  }

  function buildCard(payload, options) {
    const data = payload || {};
    const domains = listFrom(data.manifested || data.domains || data.domain || data.lead_domain);
    if (!domains.length && data.lead_domain) domains.push(String(data.lead_domain));
    const powers = listFrom(data.powers || data.selected_powers || data.abilities || data.flavor_vectors);
    const seed = hash(JSON.stringify({
      name: firstValue(data, ["character_name", "hero_name", "name"]),
      lead: data.lead_domain,
      cast: data.cast,
      domains: domains,
      powers: powers
    }));
    const name = nameFor(data, domains, seed);
    const power = firstValue(data, ["power_rating", "spark_signature", "provisional_spark_signature", "signature"]) || "Pending";
    const combat = firstValue(data, ["combat_rating", "combat_capability", "provisional_combat_capability", "combat"]) || "Pending";
    const rarity = rarityFor(power, combat, domains, data);
    const palette = paletteFor(domains, seed);
    const id = (options && options.id) || data.id || "spark_" + Date.now() + "_" + seed.toString(36);
    const card = {
      id: id,
      created_at: data.created_at || new Date().toISOString(),
      source: (options && options.source) || data.source || "spark-generator",
      character_name: name,
      lead_domain: data.lead_domain || domains[0] || "Spark",
      spark_classification: data.spark_classification || data.cast || data.classification || "Unclassified Spark",
      power_rating: power,
      combat_rating: combat,
      rarity_tier: rarity,
      domains: domains,
      manifested: domains,
      powers: powers,
      description: data.character_description || data.description || data.profile_shape || "A newly awakened Spark profile generated by the protocol.",
      lore: data.character_lore || data.lore || loreFor(name, domains, rarity, data.profile_shape || data.description),
      artwork_url: (options && options.artworkUrl) || firstValue(data, ["character_image", "image_url", "premium_image_url", "portrait_url", "artwork_url"]) || nestedImage(data),
      palette: palette,
      card_seed: seed,
      raw: data
    };
    if (!card.artwork_url) card.artwork_url = artworkSvg(card);
    card.stats = statsFor(card);
    card.cast = card.spark_classification;
    card.spark_signature = card.power_rating;
    card.combat_capability = card.combat_rating;
    card.profile_shape = card.description;
    return card;
  }

  function loreFor(name, domains, rarity, description) {
    const domainText = domains.length ? domains.join(", ") : "unmapped Spark energy";
    const hook = description ? " Witness reports describe " + description : "";
    return name + " emerged as a " + rarity + " class Spark with command over " + domainText + "." + hook + " Their legend is still being written through missions, battles, and every choice made after awakening.";
  }

  function statsFor(card) {
    const power = ratingValue(card.power_rating);
    const combat = ratingValue(card.combat_rating);
    const domainBonus = Math.min(card.domains.length * 7, 28);
    const seed = card.card_seed || hash(card.character_name);
    return {
      aura: Math.min(100, Math.round((power + domainBonus + (seed % 13)) / 1.25)),
      control: Math.min(100, Math.round((power + combat) / 2)),
      resilience: Math.min(100, Math.round((combat * .72) + domainBonus)),
      legend: Math.min(100, Math.round((power + combat + domainBonus) / 2.25))
    };
  }

  function readSaved() {
    try { return JSON.parse(localStorage.getItem(savedKey) || "[]") || []; }
    catch (e) { return []; }
  }

  function writeSaved(chars) {
    try { localStorage.setItem(savedKey, JSON.stringify(chars.slice(0, 25))); }
    catch (e) {}
  }

  function upsertRecord(record) {
    const chars = readSaved();
    const index = chars.findIndex(function (item) { return item && item.id === record.id; });
    if (index >= 0) chars[index] = Object.assign({}, chars[index], record);
    else chars.unshift(record);
    writeSaved(chars);
    try { localStorage.setItem(currentKey, JSON.stringify(record)); }
    catch (e) {}
    return record;
  }

  function ensureRecord(payload, options) {
    const existing = options && options.existingRecord;
    const card = buildCard(payload, {
      id: existing && existing.id,
      source: options && options.source,
      artworkUrl: (options && options.artworkUrl) || (existing && existing.artwork_url)
    });
    return upsertRecord(Object.assign({}, existing || {}, card));
  }

  function setArtwork(recordOrId, artworkUrl) {
    const id = typeof recordOrId === "string" ? recordOrId : recordOrId && recordOrId.id;
    if (!id || !artworkUrl) return null;
    const chars = readSaved();
    let updated = null;
    for (let i = 0; i < chars.length; i += 1) {
      if (chars[i] && chars[i].id === id) {
        chars[i].artwork_url = artworkUrl;
        updated = chars[i];
        break;
      }
    }
    if (updated) {
      writeSaved(chars);
      try { localStorage.setItem(currentKey, JSON.stringify(updated)); }
      catch (e) {}
    }
    return updated;
  }

  function domainIcons(domains) {
    return (domains.length ? domains : ["Spark"]).slice(0, 5).map(function (domain) {
      const label = String(domain || "SP").slice(0, 2).toUpperCase();
      return '<span class="spark-domain-icon" title="' + attr(domain) + '">' + esc(label) + '</span>';
    }).join("");
  }

  function styleVars(card) {
    return "--card-accent:" + card.palette.accent + ";--card-accent-2:" + card.palette.accent2 + ";--card-hue:" + card.palette.hue + "deg;";
  }

  function renderCard(card, options) {
    const actionAttr = options && options.onclick
      ? ' onclick="' + attr(options.onclick) + '"'
      : ' data-action="' + attr((options && options.action) || "open-profile") + '"';
    const tierClass = "tier-" + slug(card.rarity_tier);
    return [
      '<section class="spark-card-zone" aria-label="Premium superhero trading card">',
      '<div class="spark-card-stage">',
      '<button class="spark-hero-card-shell ' + tierClass + '" type="button" style="' + attr(styleVars(card)) + '"' + actionAttr + ' aria-label="Open hero profile for ' + attr(card.character_name) + '">',
      '<span class="spark-hero-card-inner">',
      '<span class="spark-hero-card-face spark-hero-card-front">',
      '<span class="spark-card-top"><span><b>' + esc(card.character_name) + '</b><small>' + esc(card.spark_classification) + '</small></span><em>' + esc(card.rarity_tier) + '</em></span>',
      '<span class="spark-card-art"><img alt="' + attr(card.character_name) + ' character artwork" src="' + attr(card.artwork_url) + '"><i></i></span>',
      '<span class="spark-card-bottom"><span class="spark-rating-row"><span><small>Power</small><b>' + esc(card.power_rating) + '</b></span><span><small>Combat</small><b>' + esc(card.combat_rating) + '</b></span></span>',
      '<span class="spark-domain-row">' + domainIcons(card.domains) + '<strong>' + esc(card.rarity_tier) + '</strong></span></span>',
      '</span>',
      '<span class="spark-hero-card-face spark-hero-card-back">',
      '<span class="spark-card-back-title">' + esc(card.character_name) + '</span>',
      '<span class="spark-card-back-copy">' + esc(card.description) + '</span>',
      '<span class="spark-card-back-stats"><b>Aura ' + esc(card.stats.aura) + '</b><b>Control ' + esc(card.stats.control) + '</b><b>Legend ' + esc(card.stats.legend) + '</b></span>',
      '<span class="spark-card-open">Open full hero profile</span>',
      '</span>',
      '</span>',
      '</button>',
      '<p class="spark-card-hint">Tap the card to open the full hero profile. Hover or focus to flip.</p>',
      '</div>',
      '</section>'
    ].join("");
  }

  function statBars(stats) {
    return Object.keys(stats).map(function (key) {
      const value = stats[key];
      return '<div class="spark-stat"><span><b>' + esc(key) + '</b><em>' + esc(value) + '</em></span><i style="width:' + attr(value) + '%"></i></div>';
    }).join("");
  }

  function renderProfile(card, options) {
    const backAttr = options && options.backOnclick
      ? ' onclick="' + attr(options.backOnclick) + '"'
      : ' data-action="' + attr((options && options.backAction) || "profile-back") + '"';
    const missionHref = (options && options.missionHref) || ("/missions/?character_id=" + encodeURIComponent(card.id));
    const battleHref = (options && options.battleHref) || "/battles/";
    return [
      '<section class="panel spark-profile-page" style="' + attr(styleVars(card)) + '">',
      '<div class="spark-profile-hero">',
      '<div class="spark-profile-art"><img alt="' + attr(card.character_name) + ' character artwork" src="' + attr(card.artwork_url) + '"></div>',
      '<div class="spark-profile-copy">',
      '<div class="kicker">Hero Profile</div>',
      '<h2>' + esc(card.character_name) + '</h2>',
      '<p class="spark-profile-tier">' + esc(card.rarity_tier) + ' rarity &middot; ' + esc(card.spark_classification) + '</p>',
      '<p>' + esc(card.description) + '</p>',
      '<div class="result-grid spark-profile-ratings"><div><strong>Power Rating</strong><span>' + esc(card.power_rating) + '</span></div><div><strong>Combat Rating</strong><span>' + esc(card.combat_rating) + '</span></div><div><strong>Domains</strong><span>' + esc(card.domains.join(", ") || "Pending") + '</span></div><div><strong>Rarity Tier</strong><span>' + esc(card.rarity_tier) + '</span></div></div>',
      '<div class="spark-profile-icons">' + domainIcons(card.domains) + '</div>',
      '</div>',
      '</div>',
      '<div class="spark-profile-grid">',
      '<article><h3>Hero Statistics</h3>' + statBars(card.stats) + '</article>',
      '<article><h3>Character Lore</h3><p>' + esc(card.lore) + '</p>' + (card.powers.length ? '<p><strong>Powers:</strong> ' + esc(card.powers.join(", ")) + '</p>' : '') + '</article>',
      '</div>',
      '<div class="actions"><button class="primary" type="button"' + backAttr + '>Back to Card</button><a class="btn secondary" href="' + attr(missionHref) + '">Open Missions</a><a class="btn secondary" href="' + attr(battleHref) + '">Enter Battles</a></div>',
      '</section>'
    ].join("");
  }

  function injectStyles() {
    if (document.getElementById("spark-hero-card-styles")) return;
    const css = `
.spark-card-zone{margin:1.25rem 0;display:grid;place-items:center;perspective:1600px}
.spark-card-stage{width:min(100%,390px);display:grid;gap:.8rem;justify-items:center}
.spark-hero-card-shell{width:min(100%,360px);aspect-ratio:5/7;border:0;background:transparent;color:#fff;padding:0;position:relative;cursor:pointer;filter:drop-shadow(0 24px 42px rgba(0,0,0,.42));transform:translateZ(0);transition:transform .28s ease,filter .28s ease}
.spark-hero-card-shell:hover,.spark-hero-card-shell:focus-visible{transform:translateY(-8px) scale(1.015);filter:drop-shadow(0 32px 56px color-mix(in srgb,var(--card-accent) 34%,#000))}
.spark-hero-card-inner{position:absolute;inset:0;transform-style:preserve-3d;transition:transform .72s cubic-bezier(.2,.8,.2,1)}
.spark-hero-card-shell:hover .spark-hero-card-inner,.spark-hero-card-shell:focus-visible .spark-hero-card-inner{transform:rotateY(180deg)}
.spark-hero-card-face{position:absolute;inset:0;display:grid;grid-template-rows:auto minmax(0,1fr) auto;gap:.75rem;overflow:hidden;border-radius:28px;padding:1rem;backface-visibility:hidden;background:linear-gradient(145deg,rgba(255,255,255,.18),rgba(255,255,255,.035) 36%,rgba(0,0,0,.34)),linear-gradient(135deg,hsl(var(--card-hue) 82% 18%),#090b17 44%,hsl(calc(var(--card-hue) + 70deg) 86% 18%));border:2px solid color-mix(in srgb,var(--card-accent) 72%,#fff 12%);box-shadow:inset 0 0 0 6px rgba(255,255,255,.055),inset 0 0 32px color-mix(in srgb,var(--card-accent) 25%,transparent),0 0 34px color-mix(in srgb,var(--card-accent) 38%,transparent)}
.spark-hero-card-face:before{content:"";position:absolute;inset:-35%;background:linear-gradient(115deg,transparent 22%,rgba(255,255,255,.16),transparent 38%,rgba(255,255,255,.24),transparent 56%);transform:translateX(-38%) rotate(10deg);animation:spark-card-shine 3.8s linear infinite;mix-blend-mode:screen;pointer-events:none}
.spark-hero-card-face:after{content:"";position:absolute;inset:0;background:radial-gradient(circle at 18% 20%,rgba(255,255,255,.2),transparent 12%),radial-gradient(circle at 82% 18%,rgba(255,255,255,.14),transparent 10%),repeating-radial-gradient(circle at 50% 35%,rgba(255,255,255,.08) 0 1px,transparent 1px 6px);opacity:.42;pointer-events:none}
.spark-hero-card-back{transform:rotateY(180deg);grid-template-rows:auto 1fr auto auto;align-content:center;text-align:left}
.spark-card-top,.spark-card-bottom,.spark-card-art,.spark-card-back-title,.spark-card-back-copy,.spark-card-back-stats,.spark-card-open{position:relative;z-index:1}
.spark-card-top{display:flex;justify-content:space-between;gap:.7rem;align-items:flex-start;text-transform:uppercase;letter-spacing:.08em}
.spark-card-top b{display:block;font-size:clamp(1.08rem,5vw,1.45rem);line-height:1.02;text-shadow:0 2px 0 #000}
.spark-card-top small{display:block;margin-top:.28rem;color:rgba(255,255,255,.72);font-size:.62rem;font-weight:900}
.spark-card-top em,.spark-domain-row strong{font-style:normal;border:1px solid rgba(255,255,255,.35);border-radius:999px;background:color-mix(in srgb,var(--card-accent) 28%,rgba(0,0,0,.44));padding:.34rem .55rem;font-size:.68rem;font-weight:950;white-space:nowrap}
.spark-card-art{min-height:0;border-radius:22px;overflow:hidden;border:1px solid rgba(255,255,255,.28);background:#050713;box-shadow:inset 0 0 0 4px rgba(255,255,255,.055)}
.spark-card-art img{width:100%;height:100%;object-fit:cover;display:block;transform:scale(1.02);filter:saturate(1.16) contrast(1.06)}
.spark-card-art i{position:absolute;inset:0;background:linear-gradient(180deg,transparent 54%,rgba(0,0,0,.72)),radial-gradient(circle at 50% 12%,rgba(255,255,255,.18),transparent 38%);pointer-events:none}
.spark-card-bottom{display:grid;gap:.7rem;padding:.75rem;border:1px solid rgba(255,255,255,.16);border-radius:20px;background:rgba(0,0,0,.24);box-shadow:inset 0 0 28px rgba(255,255,255,.04)}
.spark-rating-row{display:grid;grid-template-columns:1fr 1fr;gap:.65rem}
.spark-rating-row span{border-radius:15px;background:rgba(255,255,255,.08);padding:.62rem;text-align:left}
.spark-rating-row small{display:block;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.64);font-size:.62rem;font-weight:900}
.spark-rating-row b{display:block;margin-top:.15rem;font-size:1.15rem;line-height:1.05}
.spark-domain-row{display:flex;align-items:center;gap:.4rem;flex-wrap:wrap}
.spark-domain-icon{width:2rem;height:2rem;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--card-accent),var(--card-accent-2));color:#050713;font-size:.72rem;font-weight:1000;border:1px solid rgba(255,255,255,.72);box-shadow:0 0 14px color-mix(in srgb,var(--card-accent) 45%,transparent)}
.spark-card-back-title{font-size:1.7rem;font-weight:1000;text-transform:uppercase;letter-spacing:.08em;text-shadow:0 2px 0 #000}
.spark-card-back-copy{color:rgba(255,255,255,.82);line-height:1.55;font-weight:760}
.spark-card-back-stats{display:grid;grid-template-columns:1fr;gap:.48rem}
.spark-card-back-stats b,.spark-card-open{border:1px solid rgba(255,255,255,.18);border-radius:14px;background:rgba(255,255,255,.08);padding:.65rem;font-weight:950}
.spark-card-open{text-align:center;background:linear-gradient(90deg,var(--card-accent),var(--card-accent-2));color:#071015}
.spark-card-hint{margin:0;color:rgba(255,255,255,.68);font-size:.9rem;text-align:center}
.spark-profile-page{border-color:color-mix(in srgb,var(--card-accent) 48%,rgba(255,255,255,.16));box-shadow:0 30px 90px rgba(0,0,0,.34),0 0 44px color-mix(in srgb,var(--card-accent) 22%,transparent)}
.spark-profile-hero{display:grid;grid-template-columns:minmax(220px,.82fr) minmax(0,1.18fr);gap:1.25rem;align-items:center}
.spark-profile-art{border-radius:30px;overflow:hidden;border:2px solid color-mix(in srgb,var(--card-accent) 68%,#fff 8%);background:#050713;box-shadow:0 0 32px color-mix(in srgb,var(--card-accent) 28%,transparent)}
.spark-profile-art img{display:block;width:100%;aspect-ratio:4/5;object-fit:cover}
.spark-profile-tier{color:var(--card-accent);font-weight:950;text-transform:uppercase;letter-spacing:.09em}
.spark-profile-icons{display:flex;gap:.45rem;flex-wrap:wrap;margin-top:.8rem}
.spark-profile-grid{display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);gap:1rem;margin-top:1.25rem}
.spark-profile-grid article{border:1px solid rgba(255,255,255,.14);border-radius:22px;background:rgba(255,255,255,.045);padding:1rem}
.spark-stat{display:grid;gap:.35rem;margin:.75rem 0}
.spark-stat span{display:flex;justify-content:space-between;gap:1rem;text-transform:capitalize}
.spark-stat em{font-style:normal;color:var(--card-accent);font-weight:950}
.spark-stat>i{height:10px;border-radius:999px;background:linear-gradient(90deg,var(--card-accent),var(--card-accent-2));box-shadow:0 0 14px color-mix(in srgb,var(--card-accent) 45%,transparent)}
.tier-common{--tier-glow:#d7dde8}.tier-rare{--tier-glow:#54d7ff}.tier-epic{--tier-glow:#bf7dff}.tier-legendary{--tier-glow:#ffd166}.tier-mythic{--tier-glow:#ff5fd2}.tier-transcendent{--tier-glow:#ffffff}
.tier-legendary .spark-hero-card-face,.tier-mythic .spark-hero-card-face,.tier-transcendent .spark-hero-card-face{box-shadow:inset 0 0 0 6px rgba(255,255,255,.07),inset 0 0 42px color-mix(in srgb,var(--tier-glow) 34%,transparent),0 0 44px color-mix(in srgb,var(--tier-glow) 48%,transparent)}
@keyframes spark-card-shine{0%{transform:translateX(-46%) rotate(10deg)}100%{transform:translateX(46%) rotate(10deg)}}
@media(max-width:760px){.spark-card-stage{width:100%}.spark-hero-card-shell{width:min(100%,330px)}.spark-profile-hero,.spark-profile-grid{grid-template-columns:1fr}.spark-profile-art img{aspect-ratio:16/13}.spark-card-hint{font-size:.82rem}}
@media(hover:none){.spark-hero-card-shell:hover .spark-hero-card-inner{transform:none}.spark-hero-card-shell:hover{transform:none}}
@media(prefers-reduced-motion:reduce){.spark-hero-card-shell,.spark-hero-card-inner{transition:none}.spark-hero-card-face:before{animation:none;opacity:.26}}
`;
    const style = document.createElement("style");
    style.id = "spark-hero-card-styles";
    style.textContent = css;
    document.head.appendChild(style);
  }

  injectStyles();
  window.SparkHeroCards = {
    escapeHTML: esc,
    buildCard: buildCard,
    ensureRecord: ensureRecord,
    upsertRecord: upsertRecord,
    setArtwork: setArtwork,
    renderCard: renderCard,
    renderProfile: renderProfile,
    domainIcons: domainIcons,
    ratingValue: ratingValue
  };
})();
