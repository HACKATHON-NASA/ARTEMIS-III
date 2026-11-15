import EvolutionController from './EvolutionController'
import CrewOverviewController from './CrewOverviewController'
import CrewSeriesController from './CrewSeriesController'

const Api = {
    EvolutionController: Object.assign(EvolutionController, EvolutionController),
    CrewOverviewController: Object.assign(CrewOverviewController, CrewOverviewController),
    CrewSeriesController: Object.assign(CrewSeriesController, CrewSeriesController),
}

export default Api