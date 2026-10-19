import re, os, glob, sys
R = sys.argv[1]
rd = lambda p: open(os.path.join(R, p), encoding='utf-8').read()
scrum, plan = rd('docs/propuesta/05-estructura-scrum.md'), rd('scrum/plan-de-sprints.md')
# backlog: prioridad
prio = {m.group(1): m.group(2) for m in re.finditer(r'^\|\s*★?\s*(HU-\d\d\.\d\d)\s*\|.*\|\s*(Must|Should|Could|Won\'t)\s*\|\s*$', scrum, re.M)}
# plan: sprint, pts, deps, orden
sprint, pts, deps, orden = {}, {}, {}, {}
for sec in re.split(r'^## ', plan, flags=re.M):
    m = re.match(r'\d+\. Sprint (\d)', sec)
    if not m: continue
    s = int(m.group(1))
    for r in re.finditer(r'^\|\s*(\d+)\s*\|\s*(HU-\d\d\.\d\d)\s*(?:\|[^|]*){4}\|\s*(\d+)\s*\|([^|]*)\|', sec, re.M):
        h = r.group(2); sprint[h] = s; pts[h] = int(r.group(3)); orden[h] = int(r.group(1))
        deps[h] = re.findall(r'HU-\d\d\.\d\d', r.group(4))
print('Backlog:', len(prio), ' Plan:', len(sprint))
print('En backlog y no en plan:', sorted(set(prio) - set(sprint)))
print('En plan y no en backlog:', sorted(set(sprint) - set(prio)))
for h, ds in deps.items():
    for d in ds:
        if d not in sprint: print('Dependencia inexistente', h, '->', d)
        elif sprint[d] > sprint[h] or (sprint[d] == sprint[h] and orden[d] > orden[h]):
            print(f'ORDEN: {h} (S{sprint[h]}#{orden[h]}) depende de {d} (S{sprint[d]}#{orden[d]})')
# dependencias de 05 (historias desarrolladas)
for m in re.finditer(r'### (HU-\d\d\.\d\d) ·.*?\n\|.*?\n\|.*?\n\|\s*(\w+)\s*\|\s*(\d+)\s*\|[^|]*\|[^|]*\|([^|]*)\|', scrum, re.S):
    h, p, n, d = m.group(1), m.group(2), int(m.group(3)), set(re.findall(r'HU-\d\d\.\d\d', m.group(4)))
    if p != prio.get(h): print('Prioridad distinta', h, p, prio.get(h))
    if n != pts.get(h): print('Puntos distintos', h, n, pts.get(h))
    if d != set(deps.get(h, [])): print('Deps distintas en', h, '05:', sorted(d), 'plan:', sorted(deps.get(h, [])))
# contingencia vs prioridad
cont = re.search(r'## 11\..*', plan, re.S).group(0)
filas = re.findall(r'^\|\s*(\d)\s*\|[^|]*\|([^|]*)\|', cont, re.M)
for o, lst in filas:
    for h in re.findall(r'HU-\d\d\.\d\d', lst):
        esperado = 'Could' if o == '1' else 'Should'
        if prio.get(h) != esperado: print('Contingencia', o, h, 'es', prio.get(h))
    # dependientes que no se recortan
cortadas = {h for _, l in filas for h in re.findall(r'HU-\d\d\.\d\d', l)}
for h, ds in deps.items():
    for d in ds:
        if d in cortadas and h not in cortadas: print(f'Si se recorta {d}, queda huérfana {h} ({prio.get(h)})')
# fichas: sprint por historia
for f in glob.glob(os.path.join(R, 'modulos', 'M*', 'README.md')):
    t = open(f, encoding='utf-8').read()
    for m in re.finditer(r'^\|\s*(HU-\d\d\.\d\d)[^|]*\|\s*(\d)\s*\|', t, re.M):
        if sprint.get(m.group(1)) != int(m.group(2)):
            print('Ficha', os.path.basename(os.path.dirname(f)), m.group(1), 'sprint', m.group(2), 'plan', sprint.get(m.group(1)))
# totales por sprint
for s in range(5):
    hs = [h for h in sprint if sprint[h] == s]; print(f'S{s}: {len(hs)} historias, {sum(pts[h] for h in hs)} pts')
