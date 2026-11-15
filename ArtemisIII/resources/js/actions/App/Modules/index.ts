import Alertas from './Alertas'
import Cuerpo from './Cuerpo'
import Dashboard from './Dashboard'
import Entorno from './Entorno'
import Evaluacion from './Evaluacion'
import Identidad from './Identidad'
import Registro from './Registro'

const Modules = {
    Alertas: Object.assign(Alertas, Alertas),
    Cuerpo: Object.assign(Cuerpo, Cuerpo),
    Dashboard: Object.assign(Dashboard, Dashboard),
    Entorno: Object.assign(Entorno, Entorno),
    Evaluacion: Object.assign(Evaluacion, Evaluacion),
    Identidad: Object.assign(Identidad, Identidad),
    Registro: Object.assign(Registro, Registro),
}

export default Modules