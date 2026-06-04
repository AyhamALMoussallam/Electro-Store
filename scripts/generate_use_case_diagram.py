#!/usr/bin/env python3
"""
Generate Electro-V2 UML use case diagram PDF (reference style:
system boundary, actors, use cases, <<include>> / <<extend>>).
"""

import math
import subprocess
import sys
from pathlib import Path

try:
    from reportlab.lib import colors
    from reportlab.lib.pagesizes import landscape, A3
    from reportlab.lib.units import mm
    from reportlab.pdfgen import canvas
except ImportError:
    subprocess.check_call([sys.executable, "-m", "pip", "install", "reportlab", "-q"])
    from reportlab.lib import colors
    from reportlab.lib.pagesizes import landscape, A3
    from reportlab.lib.units import mm
    from reportlab.pdfgen import canvas

OUTPUT = Path(__file__).resolve().parent.parent / "docs" / "Electro-V2-Use-Case-Diagram.pdf"

FONT = "Helvetica"
FONT_BOLD = "Helvetica-Bold"
FONT_ITALIC = "Helvetica-Oblique"

# System boundary (mm): x, y, width, height
SYS = (58 * mm, 22 * mm, 304 * mm, 252 * mm)
SYS_TITLE = "An electronic platform for consumer electronics e-commerce"

# Actors: (x, y) — feet anchor
ACTORS = {
    "Admin": (20 * mm, 118 * mm),
    "Customer": (398 * mm, 148 * mm),
    "Visitor": (398 * mm, 48 * mm),
}

# Use cases: name -> (cx, cy, rx, ry) in mm — spaced ≥28mm vertically per column
USE_CASES = {
    # --- shared auth (top center) ---
    "Verify credentials": (178, 248, 34, 10),
    "Sign in with Google": (108, 232, 36, 10),
    "Display login error": (258, 232, 34, 10),
    "log in": (168, 212, 20, 10),
    "log out": (248, 212, 22, 10),
    "profile management": (208, 188, 32, 10),
    # --- admin (left column) ---
    "Update order status": (52, 168, 34, 10),
    "Orders management": (108, 168, 34, 10),
    "CRUD Categories": (108, 142, 32, 10),
    "CRUD Brands": (108, 116, 28, 10),
    "Manage product image": (52, 92, 34, 10),
    "CRUD Products": (108, 90, 30, 10),
    "CRUD Cities": (108, 64, 28, 10),
    "CRUD Areas": (108, 38, 28, 10),
    "Manage exchange rate": (148, 14, 36, 10),
    # --- customer (right column) ---
    "Reset password": (328, 188, 32, 10),
    "Browse store": (278, 168, 28, 10),
    "View product details": (278, 142, 36, 10),
    "Manage cart": (278, 116, 28, 10),
    "Place order": (278, 100, 28, 10),
    "Select city and area": (336, 82, 36, 10),
    "View my orders": (278, 76, 32, 10),
    "Submit product review": (278, 38, 38, 10),
    # --- visitor (bottom center-right) ---
    "Verify email": (248, 22, 26, 10),
    "Sign up": (208, 22, 22, 10),
    "Browse site": (318, 55, 28, 10),
    "View products": (252, 55, 30, 10),
}

ACTOR_LINKS = {
    "Admin": [
        "log in", "log out", "profile management",
        "Orders management", "CRUD Categories", "CRUD Brands",
        "CRUD Products", "CRUD Cities", "CRUD Areas", "Manage exchange rate",
    ],
    "Customer": [
        "log in", "log out", "profile management",
        "Browse store", "View product details", "Manage cart",
        "Place order", "View my orders", "Submit product review", "Reset password",
    ],
    "Visitor": ["Browse site", "View products", "Sign up", "Reset password"],
}

INCLUDES = [
    ("log in", "Verify credentials"),
    ("Place order", "Select city and area"),
    ("Orders management", "Update order status"),
    ("CRUD Products", "Manage product image"),
    ("CRUD Cities", "CRUD Areas"),
]

EXTENDS = [
    ("Display login error", "log in"),
    ("Verify email", "Sign up"),
    ("Sign in with Google", "log in"),
]


def uc_center(name):
    cx, cy, rx, ry = USE_CASES[name]
    return cx * mm, cy * mm, rx * mm, ry * mm


def ellipse_point(cx, cy, rx, ry, tx, ty):
    dx, dy = tx - cx, ty - cy
    if abs(dx) < 0.01 and abs(dy) < 0.01:
        return cx + rx, cy
    scale = 1.0 / math.sqrt((dx / rx) ** 2 + (dy / ry) ** 2)
    return cx + dx * scale, cy + dy * scale


def draw_use_case(c, name):
    cx, cy, rx, ry = uc_center(name)
    c.setStrokeColor(colors.black)
    c.setFillColor(colors.white)
    c.setLineWidth(0.8)
    c.ellipse(cx - rx, cy - ry, cx + rx, cy + ry, fill=1, stroke=1)
    c.setFont(FONT, 7.5 if len(name) > 22 else 8)
    c.setFillColor(colors.black)
    c.drawCentredString(cx, cy - 2 * mm, name)


def draw_actor(c, x, y, label):
    head_r = 4 * mm
    c.setStrokeColor(colors.black)
    c.setLineWidth(0.9)
    c.circle(x, y + 14 * mm, head_r, stroke=1, fill=0)
    c.line(x, y + 10 * mm, x, y + 2 * mm)
    c.line(x - 8 * mm, y + 7 * mm, x + 8 * mm, y + 7 * mm)
    c.line(x, y + 2 * mm, x - 6 * mm, y - 6 * mm)
    c.line(x, y + 2 * mm, x + 6 * mm, y - 6 * mm)
    c.setFont(FONT_BOLD, 10)
    c.drawCentredString(x, y - 12 * mm, label)


def actor_anchor(name):
    x, y = ACTORS[name]
    if name == "Admin":
        return x + 12 * mm, y + 8 * mm
    return x - 12 * mm, y + 8 * mm


def draw_solid_link(c, ax, ay, name):
    cx, cy, rx, ry = uc_center(name)
    ex, ey = ellipse_point(cx, cy, rx, ry, ax, ay)
    c.setStrokeColor(colors.black)
    c.setLineWidth(0.7)
    c.line(ax, ay, ex, ey)


def draw_dashed_arrow(c, from_name, to_name, stereotype):
    fx, fy, frx, fry = uc_center(from_name)
    tx, ty, trx, try_ = uc_center(to_name)
    x1, y1 = ellipse_point(fx, fy, frx, fry, tx, ty)
    x2, y2 = ellipse_point(tx, ty, trx, try_, fx, fy)
    c.setStrokeColor(colors.black)
    c.setLineWidth(0.65)
    c.setDash(4, 3)
    c.line(x1, y1, x2, y2)
    c.setDash()
    dx, dy = x2 - x1, y2 - y1
    length = math.hypot(dx, dy) or 1
    ux, uy = dx / length, dy / length
    tip = 3 * mm
    side = 1.8 * mm
    px, py = -uy, ux
    c.line(x2, y2, x2 - ux * tip + px * side, y2 - uy * tip + py * side)
    c.line(x2, y2, x2 - ux * tip - px * side, y2 - uy * tip - py * side)
    mx, my = (x1 + x2) / 2, (y1 + y2) / 2
    c.setFont(FONT_ITALIC, 7)
    c.drawCentredString(mx + px * 4 * mm, my + py * 4 * mm, f"«{stereotype}»")


def main():
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    page_w, page_h = landscape(A3)
    c = canvas.Canvas(str(OUTPUT), pagesize=landscape(A3))

    sx, sy, sw, sh = SYS
    c.setStrokeColor(colors.black)
    c.setLineWidth(1.2)
    c.rect(sx, sy, sw, sh, fill=0, stroke=1)

    c.setFont(FONT_BOLD, 10.5)
    c.drawCentredString(sx + sw / 2, sy + sh - 8 * mm, SYS_TITLE)

    for name, pos in ACTORS.items():
        draw_actor(c, pos[0], pos[1], name)

    for name in USE_CASES:
        draw_use_case(c, name)

    for actor, cases in ACTOR_LINKS.items():
        ax, ay = actor_anchor(actor)
        for case in cases:
            draw_solid_link(c, ax, ay, case)

    for a, b in INCLUDES:
        draw_dashed_arrow(c, a, b, "include")

    for a, b in EXTENDS:
        draw_dashed_arrow(c, a, b, "extend")

    c.setFont(FONT_BOLD, 14)
    c.drawCentredString(page_w / 2, page_h - 10 * mm, "Electro-V2 — Use Case Diagram")

    c.setFont(FONT, 8)
    c.setFillColor(colors.HexColor("#4A4E69"))
    c.drawCentredString(
        page_w / 2,
        page_h - 16 * mm,
        "Actors: Admin · Customer · Visitor  |  Dashed: «include» / «extend»",
    )

    c.save()
    print(f"Created: {OUTPUT}")


if __name__ == "__main__":
    main()
