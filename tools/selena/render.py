import json
from PIL import Image, ImageDraw
d=json.load(open("boards.json")); k=200/72
for pno in range(2,17):
    im=Image.open(f"r200-{pno:02d}.png").convert("RGB")
    dr=ImageDraw.Draw(im)
    for r in d[str(pno)]["white"]:
        dr.rectangle([v*k for v in r],fill=(255,255,255))
    # trim: bbox de lo que no es casi-blanco
    g=im.convert("L").point(lambda v: 255 if v<246 else 0)
    bb=g.getbbox(); pad=int(6*k)
    bb=(max(0,bb[0]-pad),max(0,bb[1]-pad),min(im.width,bb[2]+pad),min(im.height,bb[3]+pad))
    c=im.crop(bb)
    c.save(f"out/p{pno:02d}.webp",quality=84,method=6)
    w=min(1200,c.width); c.resize((w,int(c.height*w/c.width)),Image.LANCZOS).save(f"out/p{pno:02d}-m.webp",quality=80,method=6)
    print(pno,c.size)
