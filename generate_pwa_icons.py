import os
from PIL import Image, ImageDraw, ImageFont

pwa_dir = os.path.join("public", "assets", "images", "pwa")
os.makedirs(pwa_dir, exist_ok=True)

def create_pwa_icon(size, is_maskable=False, bg_color=(79, 70, 229)): # Indigo #4F46E5
    img = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    
    # Background
    if is_maskable:
        # Full solid background
        draw.rectangle([0, 0, size, size], fill=(15, 23, 42)) # Deep Navy #0F172A
        # Inner rounded badge
        margin = int(size * 0.1)
        draw.rounded_rectangle([margin, margin, size - margin, size - margin], radius=int(size * 0.2), fill=bg_color)
    else:
        # Rounded squircle badge
        draw.rounded_rectangle([0, 0, size, size], radius=int(size * 0.22), fill=bg_color)
    
    # Draw Ministry Icon & Monogram 'LCM'
    # Draw geometric emblem:
    center = size / 2
    
    # Draw 3 layered stylized chevron crests
    # Top chevron
    w = size * 0.55
    h = size * 0.25
    y_offset = size * 0.24
    
    # Draw 'LCM' Text in bold
    try:
        font_size = int(size * 0.26)
        font = ImageFont.truetype("arialbd.ttf", font_size)
    except:
        font = ImageFont.load_default()
        
    text = "LCM"
    bbox = draw.textbbox((0, 0), text, font=font)
    text_w = bbox[2] - bbox[0]
    text_h = bbox[3] - bbox[1]
    
    # Draw stylized church / crown crest above text
    crest_y = int(size * 0.25)
    crest_h = int(size * 0.18)
    crest_w = int(size * 0.35)
    
    # Golden cross / beam
    cross_w = max(4, int(size * 0.04))
    cross_h = int(size * 0.22)
    cross_x = int(center - cross_w / 2)
    draw.rectangle([cross_x, crest_y, cross_x + cross_w, crest_y + cross_h], fill=(255, 255, 255))
    
    beam_w = int(size * 0.18)
    beam_h = cross_w
    beam_y = crest_y + int(cross_h * 0.3)
    draw.rectangle([int(center - beam_w/2), beam_y, int(center + beam_w/2), beam_y + beam_h], fill=(255, 255, 255))
    
    # Draw "LCM" text below cross
    text_y = int(size * 0.55)
    draw.text((int(center - text_w / 2), text_y), text, fill=(255, 255, 255), font=font)
    
    # Draw subtitle "PORTAL"
    try:
        sub_font_size = int(size * 0.09)
        sub_font = ImageFont.truetype("arial.ttf", sub_font_size)
        sub_text = "PORTAL"
        s_bbox = draw.textbbox((0, 0), sub_text, font=sub_font)
        s_w = s_bbox[2] - s_bbox[0]
        draw.text((int(center - s_w / 2), int(size * 0.82)), sub_text, fill=(224, 231, 255), font=sub_font)
    except:
        pass
        
    return img

sizes = [
    ("icon-192x192.png", 192, False),
    ("icon-512x512.png", 512, False),
    ("icon-maskable-192x192.png", 192, True),
    ("icon-maskable-512x512.png", 512, True),
    ("apple-touch-icon.png", 180, False),
    ("badge-72x72.png", 72, False)
]

for name, sz, maskable in sizes:
    icon_img = create_pwa_icon(sz, maskable)
    out_path = os.path.join(pwa_dir, name)
    icon_img.save(out_path, "PNG")
    print(f"Generated {out_path} ({sz}x{sz})")

print("All PWA icons generated successfully!")
