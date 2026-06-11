#!/usr/bin/env python3
"""Repack Dream Explorer presentation sheet into Grid Schema V1 (4×13 @ 64px)."""

from __future__ import annotations

import json
import os
import shutil
import sys
from pathlib import Path

from PIL import Image, ImageDraw

# Default source (override via argv[1])
DEFAULT_SRC = (
    r"C:\Users\USER\.cursor\projects\d\assets"
    r"\c__Users_USER_AppData_Roaming_Cursor_User_workspaceStorage_ff78ca843b3c2963e7523cece9a8312f"
    r"_images_3a8e4eb4-1382-4523-b53b-d85cff0c0efc-dcdf38de-044b-4f96-ab11-15f1c38ec257.png"
)

CELL = 64
COLS = 4
ROWS = 13
SHEET_W = COLS * CELL
SHEET_H = ROWS * CELL

# Presentation layout on 1024×768 reference art.
# Source is a labeled showcase (not a raw grid): crop sections, skip left row labels.
# Actions are one horizontal strip: interact|attack|magic|hurt × 2 frames each.
X0 = 112  # skip "UP/DOWN/…" label column
X1 = 976
LAYOUT = {
    "idle": {"y0": 98, "y1": 188, "rows": 1, "cols": 4, "x0": 48, "x1": X1},
    "walk": {"y0": 228, "y1": 398, "rows": 4, "cols": 4, "x0": X0, "x1": X1},
    "run": {"y0": 438, "y1": 608, "rows": 4, "cols": 4, "x0": X0, "x1": X1},
    "actions": {"y0": 648, "y1": 738, "rows": 1, "cols": 8, "x0": 48, "x1": X1},
}

ACTION_SLICE = {
    "interact": (0, 2),
    "attack": (2, 4),
    "magic": (4, 6),
    "hurt": (6, 8),
}

ACTION_ROWS = {
    "interact": 9,
    "attack": 10,
    "magic": 11,
    "hurt": 12,
}

ROOT = Path(__file__).resolve().parents[1]
OUTPUTS = [
    ROOT / "assets" / "dream-explorer.png",
    Path(r"D:\websites\sites\production\websites\digitaldreamscape.site")
    / "assets"
    / "sprites"
    / "player"
    / "dream-explorer.png",
]


def extract_section(img: Image.Image, spec: dict) -> list[Image.Image]:
    """Crop sprites from a rectangular section (equal grid within spec)."""
    w, _ = img.size
    x0 = spec.get("x0", 48)
    x1 = spec.get("x1", w - 48)
    y0, y1 = spec["y0"], spec["y1"]
    rows, cols = spec["rows"], spec["cols"]
    usable_w = x1 - x0
    usable_h = y1 - y0
    cell_w = usable_w // cols
    cell_h = usable_h // rows
    sprites: list[Image.Image] = []
    for r in range(rows):
        for c in range(cols):
            sx = x0 + c * cell_w
            sy = y0 + r * cell_h
            sprites.append(img.crop((sx, sy, sx + cell_w, sy + cell_h)))
    return sprites


def fit_cell(src: Image.Image, cell: int = CELL) -> Image.Image:
    """Scale sprite into cell, feet-center aligned (pivot y=1.0)."""
    canvas = Image.new("RGBA", (cell, cell), (0, 0, 0, 0))
    sw, sh = src.size
    scale = min(cell / sw, cell / sh) * 0.92
    nw, nh = max(1, int(sw * scale)), max(1, int(sh * scale))
    resized = src.resize((nw, nh), Image.Resampling.LANCZOS)
    # feet-center: horizontal center, feet at bottom with small padding
    ox = (cell - nw) // 2
    oy = cell - nh - 2
    canvas.paste(resized, (ox, oy), resized)
    return canvas


def build_sheet(src_path: str) -> Image.Image:
    img = Image.open(src_path).convert("RGBA")
    sheet = Image.new("RGBA", (SHEET_W, SHEET_H), (0, 0, 0, 0))

    def paste_row(row: int, sprites: list[Image.Image], count: int | None = None):
        n = count if count is not None else len(sprites)
        for col in range(min(n, COLS)):
            cell = fit_cell(sprites[col])
            sheet.paste(cell, (col * CELL, row * CELL), cell)

    # Row 0: idle (up, down, left, right)
    idle = extract_section(img, LAYOUT["idle"])
    paste_row(0, idle)

    # Rows 1–4: walk directions
    walk = extract_section(img, LAYOUT["walk"])
    for i in range(4):
        paste_row(1 + i, walk[i * 4 : (i + 1) * 4])

    # Rows 5–8: run directions
    run = extract_section(img, LAYOUT["run"])
    for i in range(4):
        paste_row(5 + i, run[i * 4 : (i + 1) * 4])

    # Rows 9–12: contextual actions (2 frames each from bottom strip)
    action_strip = extract_section(img, LAYOUT["actions"])
    for action, row in ACTION_ROWS.items():
        a, b = ACTION_SLICE[action]
        paste_row(row, action_strip[a:b], 2)

    return sheet


def build_atlas_json() -> dict:
    """Mirror atlas-builder.js Grid Schema V1 output with pivot metadata."""
    animations = {}
    dirs = ["up", "down", "left", "right"]
    for i, d in enumerate(dirs):
        animations[f"idle_{d}"] = {
            "row": 0,
            "startFrame": i,
            "length": 1,
            "loop": True,
            "fps": 8,
        }
    for i, d in enumerate(dirs):
        animations[f"walk_{d}"] = {
            "row": 1 + i,
            "startFrame": 0,
            "length": 4,
            "loop": True,
            "fps": 10,
        }
    for i, d in enumerate(dirs):
        animations[f"run_{d}"] = {
            "row": 5 + i,
            "startFrame": 0,
            "length": 4,
            "loop": True,
            "fps": 12,
        }
    for key, row in ACTION_ROWS.items():
        animations[key] = {
            "row": row,
            "startFrame": 0,
            "length": 2,
            "loop": False,
            "fps": 10,
        }
    return {
        "asset": {
            "name": "dream-explorer",
            "version": "1.0",
            "texture": "dream-explorer.png",
            "gridSize": {"w": CELL, "h": CELL},
            "pivot": {"x": 0.5, "y": 1.0},
        },
        "animations": animations,
    }


def main() -> int:
    src = sys.argv[1] if len(sys.argv) > 1 else DEFAULT_SRC
    if not os.path.isfile(src):
        print(f"Source not found: {src}", file=sys.stderr)
        return 1

    sheet = build_sheet(src)
    atlas = build_atlas_json()

    for png_path in OUTPUTS:
        png_path.parent.mkdir(parents=True, exist_ok=True)
        sheet.save(png_path, "PNG")
        json_path = png_path.with_name("dream-explorer-atlas.json")
        json_path.write_text(json.dumps(atlas, indent=2) + "\n", encoding="utf-8")
        print(f"Wrote {png_path} ({SHEET_W}×{SHEET_H})")
        print(f"Wrote {json_path}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
