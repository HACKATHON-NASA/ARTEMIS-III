import ListThresholdsController from './ListThresholdsController'
import UpdateThresholdController from './UpdateThresholdController'
import ShowAssessmentController from './ShowAssessmentController'
import PsychTestController from './PsychTestController'
import PsychResultController from './PsychResultController'

const Api = {
    ListThresholdsController: Object.assign(ListThresholdsController, ListThresholdsController),
    UpdateThresholdController: Object.assign(UpdateThresholdController, UpdateThresholdController),
    ShowAssessmentController: Object.assign(ShowAssessmentController, ShowAssessmentController),
    PsychTestController: Object.assign(PsychTestController, PsychTestController),
    PsychResultController: Object.assign(PsychResultController, PsychResultController),
}

export default Api