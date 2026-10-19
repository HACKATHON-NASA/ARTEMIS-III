import numpy as np
rng = np.random.default_rng(3)
def mision(e7=False):
    vals = []
    for sexo in "MFMF":
        base = 14.5 if sexo == "M" else 13.2
        v = base - 0.05*np.arange(5) + rng.normal(0, 0.2, 5)
        vals.append(v)
    if e7:  # E7 en T (hombre): 14,1 -> 13,4 -> 12,7 -> 12,0 en 4 lunes, con el mismo ruido
        vals[0] = np.array([14.5, 14.1, 13.4, 12.7, 12.0]) + rng.normal(0, 0.2, 5)
    return vals
def regla(v, minpct, paso=0.0, n=3):
    for i in range(n-1, len(v)):
        w = v[i-n+1:i+1]
        d = np.diff(w)
        if np.all(d < -paso) and (w[0]-w[-1])/w[0]*100 > minpct: return True
    return False
def evaluar(nombre, f, N=20000):
    fp = sum(any(f(v) for v in mision()) for _ in range(N)) / N
    tp = sum(f(mision(True)[0]) for _ in range(N)) / N
    print(f"{nombre:52s} E0 falso: {fp:6.1%}   E7 detectado: {tp:6.1%}")
evaluar("Actual: 3 tomas que bajan y cambio > 5 %", lambda v: regla(v, 5))
evaluar("3 tomas que bajan y cambio > 7,5 %", lambda v: regla(v, 7.5))
evaluar("3 tomas, cada paso baja > 0,3 g/dL y cambio > 5 %", lambda v: regla(v, 5, 0.3))
evaluar("4 tomas que bajan y cambio > 5 %", lambda v: regla(v, 5, 0, 4))
