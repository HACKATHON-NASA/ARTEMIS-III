import numpy as np
rng = np.random.default_rng(0)
N = 20000  # misiones simuladas (4 tripulantes, 30 dias)

def racha3(z):
    c = 0
    for v in z:
        c = c + 1 if v >= 2 else 0
        if c >= 3: return True
    return False

# 1) Precision de aritmetica: 20 items binomiales, p ~ U(0.92,0.98), sigma_min = 2 pp
def arit(ddof):
    hits = 0
    for _ in range(N):
        alerta = False
        for t in range(4):
            p = rng.uniform(0.92, 0.98)
            prec = 100 * rng.binomial(20, p, size=30) / 20
            base = prec[2:7]
            mu, sd = base.mean(), base.std(ddof=ddof)
            z = (mu - prec[7:]) / max(sd, 2.0)   # baja = peor
            if racha3(z): alerta = True
        hits += alerta
    return hits / N

# 2) Hb semanal: -0.05 g/dL/sem + N(0,0.2); 3 tomas que empeoran y cambio total > 5 %
def hb():
    hits = 0
    for _ in range(N):
        alerta = False
        for sexo in "MFMF":
            base = 14.5 if sexo == "M" else 13.2
            semanas = np.arange(5)  # ~5 lunes en 30 dias
            v = base - 0.05 * semanas + rng.normal(0, 0.2, 5)
            for i in range(2, 5):
                a, b, c = v[i-2], v[i-1], v[i]
                if b < a and c < b and (a - c) / a > 0.05: alerta = True
        hits += alerta
    return hits / N

# 3) Sueno: base U(6.5,7.0), N(base,0.3); eficiencia N(91,2.5); 3 noches malas seguidas
def sueno():
    hits = 0
    for _ in range(N):
        alerta = False
        for t in range(4):
            dur = rng.normal(rng.uniform(6.5, 7.0), 0.3, 30)
            ef = np.clip(rng.normal(91, 2.5, 30), 60, 98)
            mala = (dur < 6) | (ef < 85)
            c = 0
            for m in mala:
                c = c + 1 if m else 0
                if c >= 3: alerta = True
        hits += alerta
    return hits / N

print(f"Aritmetica (sd muestral ddof=1): P(>=1 aviso falso en E0) = {arit(1):.1%}")
print(f"Aritmetica (sd poblacional ddof=0): P(>=1 aviso falso en E0) = {arit(0):.1%}")
print(f"Hb tendencia: P(>=1 aviso falso en E0) = {hb():.1%}")
print(f"Sueno SUENO_MAL: P(>=1 aviso falso en E0) = {sueno():.1%}")
