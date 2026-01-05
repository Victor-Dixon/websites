#!/usr/bin/env python3
"""
Publish the Green Pine Lore Pack to Digital Dreamscape
"""

import requests
from requests.auth import HTTPBasicAuth
import os

def publish_green_pine():
    # Load environment variables
    wp_url = os.environ.get('DREAM_WP_URL', 'https://digitaldreamscape.site')
    wp_user = os.environ.get('DREAM_WP_USER', 'dadudekc@gmail.com')
    wp_pass = os.environ.get('DREAM_WP_APP_PASS', 'DuFX5WsrzkMPqJC0czhiaZCh')

    print(f'🌲 Publishing Green Pine Lore Pack to Digital Dreamscape')
    print(f'📍 Target: {wp_url}')
    print(f'👤 User: {wp_user}')
    print()

    # Green Pine content
    title = 'Green Pine Lore Pack: A City That Grows Without Breaking'
    content = '''# 🌲 Green Pine Lore Pack (Drop-in Expansion)

## 🌫️ The Founding: "The Pine Oath"

Before there was a city, there was a **camp of cutters and river-menders**—people who lived off timber, traded resin, and learned fast that the water here doesn't just *flow*… it *decides*. The first settlers called the place **Green Pine** because the pines stayed green even when everything else went harsh—storms, shortages, bad seasons.

They made an oath early on:

**"We grow without choking the land.
We build without breaking the water.
We move without trapping each other."**

That oath became the city's quiet backbone—spoken at groundbreakings, remembered during crisis, and tested every time the streets turn orange-red.

---

## 🗺️ The Land: Rivers That Remember

Green Pine sits on a map that feels alive: **winding rivers**, soft marsh edges, and dark water pockets that look still until you build too close and the current teaches you respect.

Locals say the rivers are **old routes**—paths that existed long before roads—and that's why the city's layout naturally bends and coils around them. Bridges aren't just infrastructure here. They're **promises**.

---

## 🏙️ Districts of Green Pine (So Far)

### 🌲 **Pinecoil Hills** (North)

That curving neighborhood tucked into the green—quiet streets, winding lanes, the kind of place where porch lights come on early. It's known for **long walks**, **short drives**, and residents who fight hard to keep heavy traffic out.

**Vibe:** calm, proud, protective.

---

### 🚦 **The Red Spine** (Central Arterial)

The main east–west road that everything leans on. It started as a simple connector… and became the city's first true problem.

People don't say "traffic" here.
They say: **"The Spine is angry."**

**Lore:** If the Red Spine clogs, the whole city feels it—deliveries slip, workers run late, patience drops.

---

### 🏭 **Sawmill Flats** (South)

Where the city actually *works*: freight, service roads, utilities, expansion lots. It's not pretty, but it's honest. This district keeps the lights on and the shelves stocked.

**Vibe:** industrial grit, steady momentum, future growth.

---

### 🛣️ **Green Junction** (East / Highway Gate)

That highway interchange is Green Pine's open door—and its loudest heartbeat. Newcomers enter here. Commerce gathers here. And when it bottlenecks, the city learns humility.

**Lore:** The Junction is where ambition meets reality.

---

## 🧭 The Civic Creed: "Slow is Smooth"

Green Pine isn't a city that rushes blindly. It's a city that **wants to last**.

City planners carry an unofficial rule:

* **One road for speed**
* **One road for access**
* **No neighborhood should become a shortcut**
* **Industry gets room to breathe**
* **Nature always gets a vote**

---

## 🕯️ City Symbols (Optional Flavor You Can Reuse)

**City Motto:** *"Rooted. Flowing. Growing."*
**City Emblem:** a pine branch over a river bend
**City Bird:** the marsh heron (quiet, patient, always watching)
**City Festival:** **Resinlight Week** (lanterns along the river, celebrating the founding camp)

---

## 📜 Chronicle Hook: The First Real Challenge

Green Pine is still early—small, promising, and already facing its first "big city" trial:

**the pressure of growth funneling through a few key roads.**

That's the story arc:

* you start with a peaceful town
* you succeed
* the success creates friction
* the city evolves or it chokes

And Green Pine is the kind of place that **evolves**.

---

## 🔥 Closing Paragraph (Drop-in Ending)

This blog isn't just a build log—it's the living record of a city learning itself. We'll track every district that rises, every bottleneck we outgrow, every bridge we earn, and every moment Green Pine proves it can expand without losing its soul.

**Welcome to Green Pine.** 🌲'''

    excerpt = 'Discover Green Pine: a city built on ancient rivers and timeless oaths, where growth respects both land and water.'

    # Publish to WordPress
    api_url = f'{wp_url}/wp-json/wp/v2/posts'
    auth = HTTPBasicAuth(wp_user, wp_pass)

    data = {
        'title': title,
        'content': content,
        'excerpt': excerpt,
        'status': 'publish'
    }

    print('📝 Publishing Green Pine Lore Pack...')
    try:
        response = requests.post(api_url, json=data, auth=auth, timeout=30)

        if response.status_code == 201:
            post_data = response.json()
            post_url = post_data.get('link', 'URL not available')
            print('✅ Successfully published!')
            print(f'📝 Title: {title}')
            print(f'🔗 URL: {post_url}')
            print()
            print('🌲 Green Pine is now part of Digital Dreamscape!')
            return True
        else:
            print(f'❌ Failed to publish: HTTP {response.status_code}')
            print(f'Response: {response.text}')
            return False
    except Exception as e:
        print(f'❌ Error publishing: {e}')
        return False

if __name__ == "__main__":
    success = publish_green_pine()
    exit(0 if success else 1)