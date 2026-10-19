import os, re, sys, unicodedata, urllib.parse
root = sys.argv[1]
def slug(h):
    h = h.strip().lower()
    h = re.sub(r'<[^>]+>', '', h)
    h = re.sub(r'[*_`]', '', h)
    h = re.sub(r'\[([^\]]*)\]\([^)]*\)', r'\1', h)
    out = ''.join(c for c in h if c.isalnum() or c in ' -' or unicodedata.category(c).startswith('L') or unicodedata.category(c).startswith('N'))
    return out.replace(' ', '-')
anchors, files = {}, []
for d, _, fs in os.walk(root):
    for f in fs:
        if f.lower().endswith('.md'):
            p = os.path.normpath(os.path.join(d, f)); files.append(p)
            seen = {}; s = set(); incode = False
            for line in open(p, encoding='utf-8'):
                if line.startswith('```'): incode = not incode
                if incode: continue
                m = re.match(r'^(#{1,6})\s+(.*)', line)
                if m:
                    b = slug(m.group(2)); n = seen.get(b, 0); seen[b] = n + 1
                    s.add(b if n == 0 else f'{b}-{n}')
            anchors[p] = s
bad = 0
for p in files:
    txt = open(p, encoding='utf-8').read()
    txt = re.sub(r'```.*?```', '', txt, flags=re.S)
    for m in re.finditer(r'\]\((<[^>]+>|[^)\s]+)\)', txt):
        t = m.group(1).strip('<>')
        if t.startswith(('http', 'mailto')): continue
        path, _, frag = t.partition('#')
        tgt = os.path.normpath(os.path.join(os.path.dirname(p), urllib.parse.unquote(path))) if path else p
        rel = os.path.relpath(p, root)
        if not os.path.exists(tgt):
            print(f'ARCHIVO   {rel}: {t}'); bad += 1; continue
        if frag and tgt in anchors and urllib.parse.unquote(frag) not in anchors[tgt]:
            print(f'ANCLA     {rel}: {t}'); bad += 1
print('total rotos:', bad, 'de', len(files), 'archivos')
