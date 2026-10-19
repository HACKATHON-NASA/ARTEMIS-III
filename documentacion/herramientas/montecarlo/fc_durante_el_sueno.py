import numpy as np
rng = np.random.default_rng(1)
minutos = np.arange(30*1440)
hora = (minutos % 1440) / 60
dia = minutos // 1440
sueno = (hora >= 23) | (hora < 7)
ejer = ((hora >= 10) & (hora < 11)) | ((hora >= 16) & (hora < 17))
recup = ((hora >= 11) & (hora < 11+10/60)) | ((hora >= 17) & (hora < 17+10/60))
reposo = ~sueno & ~ejer & ~recup
circ = 5*np.sin(2*np.pi*(hora-10)/24)
def mision():
    for t in range(4):
        base = rng.uniform(55, 70)
        fc = base + circ + rng.normal(0, 2, minutos.size)   # sin efecto extra del sueno
        mu = fc[reposo & (dia < 7)].mean()                   # linea base: reposo, dias 1-7
        cumple = (np.abs(fc - mu)/mu > 0.20) & (sueno | reposo) & (dia >= 7)
        c = 0
        for v in cumple:
            c = c + 1 if v else 0
            if c >= 5: return True
    return False
N = 400
print(f"FC_REPOSO_CAMBIO durante el sueno: P(>=1 aviso falso en E0) = {sum(mision() for _ in range(N))/N:.1%}")
