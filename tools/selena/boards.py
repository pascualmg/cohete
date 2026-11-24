import pdfplumber, sys, json
# por página: líneas de cabecera a tapar (las pasamos a HTML). regla: y_bottom<=Y y x0<X
RULES = {2:[(225,445)],3:[(65,900)],4:[(72,900),(217,460)],6:[(75,240)],7:[(259,700)],
         8:[(142,400)],9:[(232,700)],10:[(117,900)],11:[(128,900)],12:[(250,900)],
         14:[(106,900)],15:[(106,900),(156,260)],16:[(211,900)]}
out={}
with pdfplumber.open(sys.argv[1]) as pdf:
    for pno,p in enumerate(pdf.pages,1):
        rects=[]
        for l in p.extract_text_lines():
            for Y,X in RULES.get(pno,[]):
                if l['bottom']<=Y+0.5 and l['x0']<X:
                    x1 = l['x1'] if pno!=15 or X>800 else min(l['x1'],X)
                    rects.append([l['x0']-6,l['top']-9,x1+6,l['bottom']+5]); break
        # links
        links=[{"uri":a.get("uri"),"y":round(a["top"]),"x":round(a["x0"])} for a in (p.annots or []) if a.get("uri")]
        out[pno]={"white":rects,"links":links}
json.dump(out,open("boards.json","w"),indent=0)
for k,v in out.items(): print(k,len(v["white"]),[ (l['x'],l['y'],(l['uri'] or '')[:45]) for l in v["links"]])
