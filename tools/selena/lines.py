import pdfplumber, sys
with pdfplumber.open(sys.argv[1]) as pdf:
    for pno,p in enumerate(pdf.pages,1):
        if pno in (1,17): continue
        lines=p.extract_text_lines()
        print(f"--- p{pno}")
        for l in lines[:9]:
            print(f"  y{l['top']:5.0f}-{l['bottom']:4.0f} x{l['x0']:4.0f}-{l['x1']:4.0f} {l['text'][:70]}")
