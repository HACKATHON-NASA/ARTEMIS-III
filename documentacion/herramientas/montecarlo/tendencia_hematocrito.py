"""¿La regla EXAMEN_TENDENCIA da falsos en E0 cuando el hematocrito se deriva de la Hb?

Hb semanal: base (H 14,5 / M 13,2 g/dL) - 0,05 g/dL por semana + N(0; 0,2), 5 lunes en 30 dias.
Hto = 3 x Hb + N(0; ruido). Aviso si 3 tomas seguidas empeoran y el cambio total supera el 7,5 %.
Resultado: ruido 0,6 -> Hto 8,4 % de misiones con aviso falso; ruido 0,1 -> Hb o Hto ~0,6 %.
"""
import numpy as np

rng = np.random.default_rng(3)
N = 20000


def tendencia(v):
    for i in range(2, len(v)):
        a, b, c = v[i - 2], v[i - 1], v[i]
        if a > b > c and (a - c) / a * 100 > 7.5:
            return True
    return False


for ruido in (0.6, 0.3, 0.2, 0.1, 0.0):
    hb_f = hto_f = alguno = 0
    for _ in range(N):
        h = x = False
        for sexo in "MFMF":
            base = 14.5 if sexo == "M" else 13.2
            hb = base - 0.05 * np.arange(5) + rng.normal(0, 0.2, 5)
            hto = 3 * hb + rng.normal(0, ruido, 5)
            h |= tendencia(hb)
            x |= tendencia(hto)
        hb_f += h; hto_f += x; alguno += (h or x)
    print(f"ruido Hto {ruido}: Hb {hb_f / N:.2%}  Hto {hto_f / N:.2%}  Hb o Hto {alguno / N:.2%}")
