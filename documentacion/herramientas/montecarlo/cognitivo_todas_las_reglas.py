"""Monte Carlo de COG_DETERIORO con TODAS las reglas de 04 §5.5 y las siete métricas (no solo la precisión).

Mide, en N misiones de 4 tripulantes y 30 días (una sesión de cada test al día):
  - E0: probabilidad de al menos un aviso falso, separada por regla y por métrica;
  - E4 (--e4): probabilidad de detectar el deterioro de T2 en las sesiones 20 a 24.

Parámetros del simulador que este script FIJA (el simulador debe usar los mismos, †):
  - tiempo de reacción del PVT log-normal alrededor de la mediana del perfil, sigma_ln = --sigma-pvt (0,20);
  - tiempo por ítem de aritmética log-normal alrededor de la base del perfil, sigma_ln = 0,30;
  - intervalo del PVT U(2, 10) s; falsas salidas Poisson(0,2) por sesión;
  - memoria: base el 90 % de las sesiones y base ± 1 el 10 %.

Uso:
  python cognitivo_todas_las_reglas.py                         # E0 con las reglas corregidas
  python cognitivo_todas_las_reglas.py --pendiente             # E0 con la pendiente como disparador (regla original)
  python cognitivo_todas_las_reglas.py --rel 0 --e4f 0.05      # E0 con las σ mínimas originales
  python cognitivo_todas_las_reglas.py --e4                    # detección de E4
"""
import argparse
from collections import Counter

import numpy as np

ap = argparse.ArgumentParser()
ap.add_argument('--n', type=int, default=3000, help='misiones simuladas')
ap.add_argument('--sigma-pvt', type=float, default=0.20, help='sigma_ln del tiempo de reacción del PVT')
ap.add_argument('--rel', type=float, default=0.10, help='σ mínima relativa (fracción de μ) para la mediana del PVT y el tiempo de aritmética')
ap.add_argument('--pendiente', action='store_true', help='usar la pendiente de 7 días como disparador de aviso (regla original)')
ap.add_argument('--e4', action='store_true', help='inyectar E4 en T2 (sesiones 20 a 24)')
ap.add_argument('--e4f', type=float, default=0.08, help='aumento de la mediana del PVT por día de E4 (0,05 en la versión original)')
ap.add_argument('--semilla', type=int, default=11)
args = ap.parse_args()
rng = np.random.default_rng(args.semilla)

SMIN = {'med': 10, 'de': 10, 'lap': 1, 'fal': 1, 'at': 0.2, 'ap': 7.5, 'mem': 1}   # M16 sigma_min
PEOR_SUBE = {'med': 1, 'de': 1, 'lap': 1, 'fal': 1, 'at': 1, 'ap': -1, 'mem': -1}
TCRIT = {3: 3.182, 4: 2.776, 5: 2.571, 6: 2.447, 7: 2.365, 8: 2.306, 9: 2.262, 10: 2.228}  # t de Student, dos colas, 0,05


def sesion_pvt(mediana):
    t, rts = 0.0, []
    while True:
        t += rng.uniform(2, 10)
        rt = mediana * np.exp(rng.normal(0, args.sigma_pvt))
        t += rt / 1000
        if t >= 180:
            break
        rts.append(rt)
    rts = np.array(rts)
    return {'med': np.median(rts), 'de': rts.std(ddof=1), 'lap': int((rts > 500).sum()), 'fal': int(rng.poisson(0.2))}


def pendiente_desfavorable(y, mu, peor):
    n = len(y)
    if n < 5:
        return False
    x = np.arange(n, dtype=float)
    b, a = np.polyfit(x, y, 1)
    res = y - (a + b * x)
    se = np.sqrt(res @ res / (n - 2) / ((x - x.mean()) @ (x - x.mean())))
    if se == 0:
        return False
    return abs(b / se) > TCRIT[min(n - 2, 10)] and np.sign(b) == peor and b * 7 / mu * 100 * peor > 10


def mision():
    r = Counter()
    for trip in range(4):
        med0, prec, at0, mem0 = rng.uniform(250, 300), rng.uniform(0.92, 0.98), rng.uniform(2, 4), rng.integers(6, 9)
        S = []
        for s in range(30):
            practica = 1.05 - 0.05 * min(s, 2) / 2                       # mejora del 5 % en las sesiones 1 a 3
            dia_e4 = s - 19 if (args.e4 and trip == 1 and 20 <= s <= 24) else 0
            m = sesion_pvt(med0 * practica * (1 + args.e4f * dia_e4))
            if dia_e4:
                m['lap'] *= 3
            m['at'] = (at0 * np.exp(rng.normal(0, 0.30, 20))).mean()
            m['ap'] = 100 * rng.binomial(20, prec) / 20
            m['mem'] = mem0 if rng.random() < 0.9 else mem0 + rng.choice([-1, 1])
            S.append(m)
        base = {k: (np.mean([S[i][k] for i in range(2, 7)]), np.std([S[i][k] for i in range(2, 7)], ddof=1)) for k in SMIN}
        r2 = Counter(); r3 = Counter()
        for s in range(7, 30):
            for k in SMIN:
                mu, sd = base[k]
                smin = max(SMIN[k], args.rel * mu) if k in ('med', 'at') else SMIN[k]
                z = PEOR_SUBE[k] * (S[s][k] - mu) / max(sd, smin)
                r2[k] = r2[k] + 1 if z >= 2 else 0
                r3[k] = r3[k] + 1 if z >= 3 else 0
                if r2[k] >= 3:
                    r['aviso_racha_z2x3'] = 1; r['metrica_' + k] = 1
                if r3[k] >= 2:
                    r['aviso_racha_z3x2'] = 1; r['metrica_' + k] = 1
                    if k in ('med', 'de', 'lap', 'fal') and S[s]['lap'] > max(2 * base['lap'][0], 2):
                        r['critico'] = 1
            if args.pendiente:
                for k in ('med', 'at'):
                    y = np.array([S[i][k] for i in range(max(0, s - 6), s + 1)])
                    if pendiente_desfavorable(y, base[k][0], 1):
                        r['aviso_pendiente'] = 1; r['metrica_' + k] = 1
    r['cualquiera'] = int(any(r[k] for k in ('aviso_racha_z2x3', 'aviso_racha_z3x2', 'aviso_pendiente', 'critico')))
    return r


tot = Counter()
for _ in range(args.n):
    tot.update(mision())
que = 'Deteccion de E4 (T2)' if args.e4 else 'Falsa alarma en E0'
print(f"{que} | N={args.n} | sigma_pvt={args.sigma_pvt} | sigma_min_rel={args.rel} | pendiente={args.pendiente} | E4 +{args.e4f:.0%}/dia")
for k in sorted(tot):
    print(f"  P(al menos una) {k:20s} {tot[k] / args.n:7.2%}")
