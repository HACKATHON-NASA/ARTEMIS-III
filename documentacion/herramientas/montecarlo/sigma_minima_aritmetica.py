import numpy as np
rng = np.random.default_rng(7)
def racha(z, k, n):
    c = 0
    for v in z:
        c = c + 1 if v >= k else 0
        if c >= n: return True
    return False
def sim(smin, N=20000, deterioro=0.0):
    av = 0
    for _ in range(N):
        a = False
        for t in range(4):
            p = rng.uniform(0.92, 0.98)
            prec = 100 * rng.binomial(20, p, 30) / 20
            if deterioro:  # E4: precision cae 'deterioro' puntos desde la sesion 20
                prec[19:] = 100 * rng.binomial(20, p - deterioro, 11) / 20
            b = prec[2:7]; mu, sd = b.mean(), b.std(ddof=1)
            z = (mu - prec[7:]) / max(sd, smin)
            if racha(z, 2, 3): a = True
        av += a
    return av / N
for s in (2, 5, 7.5, 10):
    print(f"sigma_min={s:>4} pp  E0 falso aviso: {sim(s):6.1%}   E4 (precision -15 pp) detectado: {sim(s, 5000, 0.15):6.1%}")
