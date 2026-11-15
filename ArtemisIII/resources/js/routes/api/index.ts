import alerts from './alerts'
import recommendations from './recommendations'
import advice from './advice'
import bodyParts from './body-parts'
import bodyStatus from './body-status'
import clinicalNotes from './clinical-notes'
import evolution from './evolution'
import crew from './crew'
import environments from './environments'
import missions from './missions'
import astronauts from './astronauts'
import thresholds from './thresholds'
import assessment from './assessment'
import auth from './auth'
import users from './users'
import devices from './devices'
import indicators from './indicators'
import measurements from './measurements'
import ingest from './ingest'

const api = {
    alerts: Object.assign(alerts, alerts),
    recommendations: Object.assign(recommendations, recommendations),
    advice: Object.assign(advice, advice),
    bodyParts: Object.assign(bodyParts, bodyParts),
    bodyStatus: Object.assign(bodyStatus, bodyStatus),
    clinicalNotes: Object.assign(clinicalNotes, clinicalNotes),
    evolution: Object.assign(evolution, evolution),
    crew: Object.assign(crew, crew),
    environments: Object.assign(environments, environments),
    missions: Object.assign(missions, missions),
    astronauts: Object.assign(astronauts, astronauts),
    thresholds: Object.assign(thresholds, thresholds),
    assessment: Object.assign(assessment, assessment),
    auth: Object.assign(auth, auth),
    users: Object.assign(users, users),
    devices: Object.assign(devices, devices),
    indicators: Object.assign(indicators, indicators),
    measurements: Object.assign(measurements, measurements),
    ingest: Object.assign(ingest, ingest),
}

export default api