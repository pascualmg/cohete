import sys, json, numpy as np
from PIL import Image
from scipy import ndimage as nd
src, dst = sys.argv[1], sys.argv[2]
res = {}
SKIP = {(15, 1)}   # p15-t1 es solo el texto del enlace: va como botón
for n in range(2,17):
    im = Image.open(f"{src}/p{n:02d}.webp").convert("RGB"); a = np.asarray(im.convert("L"))
    mask = a < 238
    mask = nd.binary_dilation(mask, structure=np.ones((3,3)), iterations={4:30,15:34}.get(n,14))  # une foto+pie (~14px)
    lab, k = nd.label(mask)
    boxes = [s for s in nd.find_objects(lab)]
    boxes = [(s[1].start, s[0].start, s[1].stop, s[0].stop) for s in boxes]
    boxes = [b for b in boxes if (b[2]-b[0])*(b[3]-b[1]) > 160*160]
    # fusionar solapes
    changed = True
    while changed:
        changed = False
        for i in range(len(boxes)):
            for j in range(i+1, len(boxes)):
                A, B = boxes[i], boxes[j]
                if A[0] < B[2] and B[0] < A[2] and A[1] < B[3] and B[1] < A[3]:
                    boxes[i] = (min(A[0],B[0]), min(A[1],B[1]), max(A[2],B[2]), max(A[3],B[3])); boxes.pop(j); changed = True; break
            if changed: break
    # pies sueltos (bajitos) -> se pegan a la pieza de encima que solape en x
    def ov(A,B): return A[0] < B[2] and B[0] < A[2]
    again = True
    while again:
        again = False
        for i,b in enumerate(boxes):
            if b[3]-b[1] < 140:
                up = [c for c in boxes if c is not b and ov(b,c) and c[3] <= b[1]+20]
                if up:
                    c = max(up, key=lambda c: c[3]); boxes.remove(c); boxes.remove(b)
                    boxes.append((min(b[0],c[0]),min(b[1],c[1]),max(b[2],c[2]),max(b[3],c[3]))); again = True; break
    # orden de lectura: por filas (centro y), luego x
    boxes.sort(key=lambda b: (round(((b[1]+b[3])/2)/250), b[0]))
    names = []
    for i, b in enumerate(boxes, 1):
        if (n, i) in SKIP: continue
        t = im.crop(b)
        if t.width / t.height <= 1.5:          # vertical o cuadrada: con 1000 px sobra en móvil
            w = min(1000, t.width); t = t.resize((w, int(t.height*w/t.width)), Image.LANCZOS)
        nm = f"p{n:02d}-t{i}"; t.save(f"{dst}/{nm}.webp", quality=80, method=6); names.append([nm, t.width, t.height])
    res[f"p{n:02d}"] = names
    print(n, len(boxes), [(b[2]-b[0], b[3]-b[1]) for b in boxes])
json.dump(res, open(sys.argv[3],"w"), indent=1)
