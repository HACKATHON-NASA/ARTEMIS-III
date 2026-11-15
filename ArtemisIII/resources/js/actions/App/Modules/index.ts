import Alertas from './Alertas'
import Cuerpo from './Cuerpo'
import Dashboard from './Dashboard'
import Entorno from './Entorno'
import Evaluacion from './Evaluacion'
import Gamificacion from './Gamificacion'
import Identidad from './Identidad'
import Recordatorios from './Recordatorios'
import Registro from './Registro'

const Modules = {
    Alertas: Object.assign(Alertas, Alertas),
    Cuerpo: Object.assign(Cuerpo, Cuerpo),
    Dashboard: Object.assign(Dashboard, Dashboard),
    Entorno: Object.assign(Entorno, Entorno),
    Evaluacion: Object.assign(Evaluacion, Evaluacion),
    Gamificacion: Object.assign(Gamificacion, Gamificacion),
    Identidad: Object.assign(Identidad, Identidad),
    Recordatorios: Object.assign(Recordatorios, Recordatorios),
    Registro: Object.assign(Registro, Registro),
}

export default Modules