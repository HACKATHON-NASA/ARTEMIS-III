import ListThresholdsController from './ListThresholdsController'
import UpdateThresholdController from './UpdateThresholdController'
import ShowAssessmentController from './ShowAssessmentController'

const Api = {
    ListThresholdsController: Object.assign(ListThresholdsController, ListThresholdsController),
    UpdateThresholdController: Object.assign(UpdateThresholdController, UpdateThresholdController),
    ShowAssessmentController: Object.assign(ShowAssessmentController, ShowAssessmentController),
}

export default Api