#!/usr/bin/env python3
"""
Generate Electro-V2 UML class diagram PDF — reference style:
3 compartments (name / attributes / methods) + association lines with +0 / +1.
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

OUTPUT = Path(__file__).resolve().parent.parent / "docs" / "Electro-V2-Class-Diagram.pdf"

BOX_W = 58 * mm
TITLE_H = 10 * mm
LINE_H = 4.5 * mm
PAD = 2.5 * mm
FONT = "Helvetica"
FONT_BOLD = "Helvetica-Bold"

CLASS_DEFS = {
    "Order": {
        "pos": (22, 195),
        "attrs": ["+ user_id", "+ area_id", "+ total_price", "+ status", "+ note"],
        "methods": [
            "+ index()", "+ create()", "+ store()", "+ show()",
            "+ edit()", "+ update()", "+ destroy()",
        ],
    },
    "User": {
        "pos": (155, 178),
        "attrs": ["+ name", "+ email", "+ password", "+ phone", "+ role"],
        "methods": ["+ update()", "+ login()", "+ register()", "+ delete()"],
    },
    "Product": {
        "pos": (288, 198),
        "attrs": [
            "+ name", "+ description_en", "+ description_ar",
            "+ price", "+ stock", "+ category_id", "+ brand_id",
        ],
        "methods": [
            "+ index()", "+ create()", "+ store()", "+ show()",
            "+ edit()", "+ update()", "+ destroy()",
        ],
    },
    "Review": {
        "pos": (22, 72),
        "attrs": ["+ user_id", "+ product_id", "+ rating", "+ comment"],
        "methods": [
            "+ index()", "+ create()", "+ store()", "+ show()",
            "+ edit()", "+ update()", "+ destroy()",
        ],
    },
    "Category": {
        "pos": (268, 78),
        "attrs": ["+ name"],
        "methods": [
            "+ index()", "+ create()", "+ store()", "+ show()",
            "+ edit()", "+ update()", "+ destroy()",
        ],
    },
    "CartItem": {
        "pos": (95, 28),
        "attrs": ["+ cart_id", "+ product_id", "+ quantity"],
        "methods": [
            "+ index()", "+ create()", "+ store()", "+ show()",
            "+ edit()", "+ update()", "+ destroy()",
        ],
    },
    "City": {
        "pos": (305, 22),
        "attrs": ["+ name"],
        "methods": [
            "+ index()", "+ create()", "+ store()", "+ show()",
            "+ edit()", "+ update()", "+ destroy()",
        ],
    },
    "Area": {
        "pos": (175, 22),
        "attrs": ["+ name", "+ city_id", "+ fee"],
        "methods": [
            "+ index()", "+ create()", "+ store()", "+ show()",
            "+ edit()", "+ update()", "+ destroy()",
        ],
    },
}

ASSOCIATIONS = [
    ("Order", "User", "+1", "+0"),
    ("Order", "Area", "+1", "+0"),
    ("User", "CartItem", "+0", "+1"),
    ("User", "Review", "+0", "+1"),
    ("Product", "CartItem", "+0", "+1"),
    ("Product", "Review", "+0", "+1"),
    ("Product", "Category", "+1", "+0"),
    ("Area", "City", "+1", "+0"),
]


def compartment_heights(attrs, methods):
    attrs_h = len(attrs) * LINE_H + PAD * 2
    methods_h = len(methods) * LINE_H + PAD * 2
    total = TITLE_H + attrs_h + methods_h
    return attrs_h, methods_h, total


def draw_class_box(c, x, y, w, h, name, attrs, methods):
    attrs_h, methods_h, _ = compartment_heights(attrs, methods)

    c.setStrokeColor(colors.black)
    c.setFillColor(colors.white)
    c.setLineWidth(0.9)
    c.rect(x, y, w, h, fill=1, stroke=1)

    y_title = y + h - TITLE_H
    y_attrs_line = y_title - attrs_h
    c.line(x, y_title, x + w, y_title)
    c.line(x, y_attrs_line, x + w, y_attrs_line)

    c.setFont(FONT_BOLD, 11)
    c.setFillColor(colors.black)
    c.drawCentredString(x + w / 2, y_title + 2.8 * mm, name)

    c.setFont(FONT, 8.5)
    ty = y_attrs_line + attrs_h - PAD - LINE_H
    for attr in attrs:
        c.drawString(x + 3 * mm, ty, attr)
        ty -= LINE_H

    ty = y_attrs_line - PAD - LINE_H
    for method in methods:
        c.drawString(x + 3 * mm, ty, method)
        ty -= LINE_H


def box_center(bounds):
    x, y, w, h = bounds
    return (x + w / 2, y + h / 2)


def border_point(bounds, tx, ty):
    x, y, w, h = bounds
    cx, cy = x + w / 2, y + h / 2
    dx, dy = tx - cx, ty - cy
    if abs(dx) < 0.01 and abs(dy) < 0.01:
        return cx, cy
    scale = (w / 2) / abs(dx) if abs(dx) * h > abs(dy) * w else (h / 2) / abs(dy)
    return cx + dx * scale, cy + dy * scale


def draw_association(c, b1, b2, l1, l2):
    c1 = box_center(b1)
    c2 = box_center(b2)
    x1, y1 = border_point(b1, c2[0], c2[1])
    x2, y2 = border_point(b2, c1[0], c1[1])

    c.setStrokeColor(colors.black)
    c.setLineWidth(0.9)
    c.line(x1, y1, x2, y2)

    dx, dy = x2 - x1, y2 - y1
    length = math.hypot(dx, dy) or 1
    ux, uy = dx / length, dy / length
    c.setFont(FONT_BOLD, 9)
    c.setFillColor(colors.black)
    c.drawString(x1 + ux * 5 * mm, y1 + uy * 5 * mm, l1)
    c.drawString(x2 - ux * 12 * mm, y2 - uy * 12 * mm, l2)


def main():
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    page_w, page_h = landscape(A3)
    c = canvas.Canvas(str(OUTPUT), pagesize=landscape(A3))

    bounds = {}
    for name, spec in CLASS_DEFS.items():
        x, y = spec["pos"][0] * mm, spec["pos"][1] * mm
        _, _, h = compartment_heights(spec["attrs"], spec["methods"])
        bounds[name] = (x, y, BOX_W, h)

    for name, spec in CLASS_DEFS.items():
        x, y, w, h = bounds[name]
        draw_class_box(c, x, y, w, h, name, spec["attrs"], spec["methods"])

    for from_c, to_c, l1, l2 in ASSOCIATIONS:
        draw_association(c, bounds[from_c], bounds[to_c], l1, l2)

    c.setFont(FONT_BOLD, 15)
    c.drawCentredString(page_w / 2, page_h - 12 * mm, "Electro-V2 — Class Diagram")

    c.setFont(FONT, 8)
    c.setFillColor(colors.HexColor("#4A4E69"))
    c.drawCentredString(
        page_w / 2,
        page_h - 18 * mm,
        "Associations: +1 = one, +0 = zero or many (reference notation)",
    )

    c.save()
    print(f"Created: {OUTPUT}")


if __name__ == "__main__":
    main()
