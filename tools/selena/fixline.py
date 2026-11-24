import json
d=json.load(open("boards.json"))
for p in ("2","4","7","9","12","16"): d[p]["white"].append([0,66,842,86])
json.dump(d,open("boards.json","w"))
