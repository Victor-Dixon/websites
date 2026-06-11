#!/usr/bin/env python3
"""Debug overlay for Dream Explorer presentation crop regions."""
from PIL import Image, ImageDraw

SRC = (
    r"C:\Users\USER\.cursor\projects\d\assets"
    r"\c__Users_USER_AppData_Roaming_Cursor_User_workspaceStorage_ff78ca843b3c2963e7523cece9a8312f"
    r"_images_3a8e4eb4-1382-4523-b53b-d85cff0c0efc-dcdf38de-044b-4f96-ab11-15f1c38ec257.png"
)
OUT = r"D:\websites\tools\sprite-engine\scripts\_debug\layout_overlay.png"

img = Image.open(SRC).convert("RGBA")
draw = ImageDraw.Draw(img)
W, H = img.size

regions = [
    ("idle", 48, 118, W - 48, 218, "cyan"),
    ("walk", 48, 255, W - 48, 445, "lime"),
    ("run", 48, 478, W - 48, 668, "yellow"),
    ("act-top-L", 48, 548, 320, 608, "red"),
    ("act-top-R", 360, 548, 632, 608, "red"),
    ("act-bot-L", 48, 618, 320, 678, "orange"),
    ("act-bot-R", 360, 618, 632, 678, "orange"),
]

for name, x0, y0, x1, y1, color in regions:
    draw.rectangle([x0, y0, x1, y1], outline=color, width=2)
    draw.text((x0 + 4, y0 + 4), name, fill=color)

img.save(OUT)
print("Saved", OUT, "size", W, H)
