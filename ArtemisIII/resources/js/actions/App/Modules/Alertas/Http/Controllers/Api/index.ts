import ListAlertsController from './ListAlertsController'
import SendMessageController from './SendMessageController'
import AcknowledgeAlertController from './AcknowledgeAlertController'
import ListRecommendationsController from './ListRecommendationsController'
import SaveRecommendationController from './SaveRecommendationController'
import ListAdviceController from './ListAdviceController'
import SendAdviceController from './SendAdviceController'
import MarkAdviceReadController from './MarkAdviceReadController'

const Api = {
    ListAlertsController: Object.assign(ListAlertsController, ListAlertsController),
    SendMessageController: Object.assign(SendMessageController, SendMessageController),
    AcknowledgeAlertController: Object.assign(AcknowledgeAlertController, AcknowledgeAlertController),
    ListRecommendationsController: Object.assign(ListRecommendationsController, ListRecommendationsController),
    SaveRecommendationController: Object.assign(SaveRecommendationController, SaveRecommendationController),
    ListAdviceController: Object.assign(ListAdviceController, ListAdviceController),
    SendAdviceController: Object.assign(SendAdviceController, SendAdviceController),
    MarkAdviceReadController: Object.assign(MarkAdviceReadController, MarkAdviceReadController),
}

export default Api