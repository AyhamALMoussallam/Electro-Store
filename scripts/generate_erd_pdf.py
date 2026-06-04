#!/usr/bin/env python3
"""Generate Electro-V2 ERD diagram as PDF."""

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

OUTPUT = Path(__file__).resolve().parent.parent / "docs" / "Electro-V2-ERD-Diagram.pdf"

BRAND = colors.HexColor("#D10024")
DARK = colors.HexColor("#2B2D42")
GRAY = colors.HexColor("#8D99AE")
LINE = colors.HexColor("#4A4E69")

ENTITIES = {
    "users": (25 * mm, 155 * mm, 42 * mm, 32 * mm, "users", [
        "PK  id",
        "     email, name",
        "     role (0=user, 1=admin)",
        "     phone, password",
    ]),
    "categories": (78 * mm, 248 * mm, 40 * mm, 24 * mm, "categories", [
        "PK  id",
        "     name (unique)",
    ]),
    "brands": (25 * mm, 248 * mm, 40 * mm, 24 * mm, "brands", [
        "PK  id",
        "     name (unique)",
    ]),
    "products": (115 * mm, 220 * mm, 52 * mm, 40 * mm, "products", [
        "PK  id",
        "FK  category_id",
        "FK  brand_id",
        "     name, price, stock",
    ]),
    "reviews": (185 * mm, 220 * mm, 42 * mm, 28 * mm, "reviews", [
        "PK  id",
        "FK  user_id",
        "FK  product_id",
        "     rating, comment",
    ]),
    "carts": (25 * mm, 105 * mm, 40 * mm, 22 * mm, "carts", [
        "PK  id",
        "FK  user_id",
    ]),
    "cart_items": (78 * mm, 105 * mm, 48 * mm, 26 * mm, "cart_items", [
        "PK  id",
        "FK  cart_id",
        "FK  product_id",
        "     quantity",
    ]),
    "orders": (145 * mm, 105 * mm, 48 * mm, 34 * mm, "orders", [
        "PK  id",
        "FK  user_id",
        "FK  area_id",
        "     total_price, status",
    ]),
    "order_items": (210 * mm, 105 * mm, 52 * mm, 28 * mm, "order_items", [
        "PK  id",
        "FK  order_id",
        "FK  product_id",
        "     price, quantity",
    ]),
    "order_logs": (275 * mm, 105 * mm, 48 * mm, 30 * mm, "order_logs", [
        "PK  id",
        "FK  order_id",
        "FK  admin_id",
        "     action, statuses",
    ]),
    "cities": (145 * mm, 25 * mm, 40 * mm, 22 * mm, "cities", [
        "PK  id",
        "     name",
    ]),
    "areas": (210 * mm, 25 * mm, 44 * mm, 26 * mm, "areas", [
        "PK  id",
        "FK  city_id",
        "     name, fee",
    ]),
    "settings": (25 * mm, 25 * mm, 44 * mm, 24 * mm, "settings", [
        "PK  id",
        "     key, value",
    ]),
}

# (parent ONE side, child MANY side with FK) — cardinality on child: "N" or "1"
RELATIONS = [
    ("categories", "products", "N"),
    ("brands", "products", "N"),
    ("products", "reviews", "N"),
    ("users", "carts", "1"),
    ("carts", "cart_items", "N"),
    ("products", "cart_items", "N"),
    ("users", "orders", "N"),
    ("areas", "orders", "N"),
    ("cities", "areas", "N"),
    ("orders", "order_items", "N"),
    ("products", "order_items", "N"),
    ("orders", "order_logs", "N"),
    ("users", "order_logs", "N"),
    ("users", "reviews", "N"),
]


def entity_center(name):
    x, y, w, h, _, _ = ENTITIES[name]
    return x + w / 2, y + h / 2


def border_point(name, target_x, target_y):
    """Point on entity border facing the other entity."""
    x, y, w, h, _, _ = ENTITIES[name]
    cx, cy = x + w / 2, y + h / 2
    dx = target_x - cx
    dy = target_y - cy

    if abs(dx) < 0.01 and abs(dy) < 0.01:
        return cx, y + h

    if abs(dx) / w > abs(dy) / h:
        if dx > 0:
            return x + w, cy
        return x, cy

    if dy > 0:
        return cx, y + h
    return cx, y


def point_on_line(x1, y1, x2, y2, t):
    return x1 + (x2 - x1) * t, y1 + (y2 - y1) * t


def draw_entity(c, spec):
    x, y, w, h, title, fields = spec
    header_h = 8 * mm

    c.setStrokeColor(DARK)
    c.setLineWidth(1)
    c.setFillColor(BRAND)
    c.rect(x, y + h - header_h, w, header_h, fill=1, stroke=0)

    c.setFillColor(colors.white)
    c.rect(x, y, w, h - header_h, fill=1, stroke=1)

    c.setFillColor(colors.white)
    c.setFont("Helvetica-Bold", 11)
    c.drawCentredString(x + w / 2, y + h - header_h + 2.5 * mm, title)

    c.setFillColor(DARK)
    c.setFont("Helvetica", 8)
    line_y = y + h - header_h - 4 * mm
    for line in fields:
        c.drawString(x + 3 * mm, line_y, line)
        line_y -= 4.2 * mm


def draw_one_marker(c, x, y, angle_rad):
    """Perpendicular bar = exactly one on parent side."""
    length = 4 * mm
    nx = -math.sin(angle_rad)
    ny = math.cos(angle_rad)
    c.setStrokeColor(DARK)
    c.setLineWidth(1.5)
    c.line(
        x - nx * length / 2, y - ny * length / 2,
        x + nx * length / 2, y + ny * length / 2,
    )


def draw_crows_foot(c, x, y, angle_rad):
    """Crow's foot = many on child (FK) side."""
    size = 5 * mm
    ux = math.cos(angle_rad)
    uy = math.sin(angle_rad)
    px = -uy
    py = ux

    c.setStrokeColor(DARK)
    c.setLineWidth(1.2)
    for spread in (-1, 0, 1):
        ox = px * spread * size * 0.45
        oy = py * spread * size * 0.45
        c.line(x, y, x - ux * size + ox, y - uy * size + oy)


def draw_cardinality_label(c, x, y, text):
    c.setFillColor(colors.white)
    c.circle(x, y, 4.5 * mm, fill=1, stroke=0)
    c.setFillColor(BRAND)
    c.setFont("Helvetica-Bold", 10)
    c.drawCentredString(x, y - 1.2 * mm, text)


def draw_relation(c, parent, child, child_card):
    tcx, tcy = entity_center(child)
    pcx, pcy = entity_center(parent)

    x1, y1 = border_point(parent, tcx, tcy)
    x2, y2 = border_point(child, pcx, pcy)

    angle = math.atan2(y2 - y1, x2 - x1)

    c.setStrokeColor(LINE)
    c.setLineWidth(1.2)
    c.line(x1, y1, x2, y2)

    # Parent end: one (bar + label "1")
    draw_one_marker(c, x1, y1, angle)
    lx1, ly1 = point_on_line(x1, y1, x2, y2, 0.18)
    draw_cardinality_label(c, lx1, ly1, "1")

    # Child end: N = crow's foot; 1 = one-to-one (bar only, no crow's foot)
    if child_card == "N":
        draw_crows_foot(c, x2, y2, angle + math.pi)
    else:
        draw_one_marker(c, x2, y2, angle + math.pi)

    lx2, ly2 = point_on_line(x1, y1, x2, y2, 0.82)
    draw_cardinality_label(c, lx2, ly2, child_card)


def draw_legend(c):
    lx, ly = 12 * mm, 12 * mm
    c.setFillColor(colors.HexColor("#F5F5F5"))
    c.setStrokeColor(GRAY)
    c.rect(lx, ly, 108 * mm, 32 * mm, fill=1, stroke=1)
    c.setFillColor(DARK)
    c.setFont("Helvetica-Bold", 9)
    c.drawString(lx + 4 * mm, ly + 26 * mm, "Legend")
    c.setFont("Helvetica", 8)
    for i, line in enumerate([
        "Parent side:  |  and  1  = exactly ONE record",
        "Child side (FK): crow's foot and N = MANY records",
        "Arrow reads: ONE  ----->  MANY  (e.g. one category has many products)",
        "FK column lives on the child (many) table",
    ]):
        c.drawString(lx + 4 * mm, ly + 18 * mm - i * 4.5 * mm, line)


def main():
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    page_size = landscape(A3)
    page_w, page_h = page_size

    c = canvas.Canvas(str(OUTPUT), pagesize=page_size)
    c.setTitle("Electro-V2 ERD Diagram")

    c.setFillColor(DARK)
    c.setFont("Helvetica-Bold", 20)
    c.drawString(20 * mm, page_h - 18 * mm, "Electro-V2 — Entity Relationship Diagram")

    c.setFont("Helvetica", 10)
    c.setFillColor(GRAY)
    c.drawString(20 * mm, page_h - 25 * mm, "Cardinality: 1 (one) on parent  →  N (many) on child with foreign key")

    for spec in ENTITIES.values():
        draw_entity(c, spec)

    for parent, child, card in RELATIONS:
        draw_relation(c, parent, child, card)

    draw_legend(c)

    c.setFillColor(GRAY)
    c.setFont("Helvetica", 8)
    c.drawRightString(page_w - 12 * mm, 8 * mm, "Generated from database migrations")

    c.showPage()
    c.save()
    print(f"Created: {OUTPUT}")


if __name__ == "__main__":
    main()
