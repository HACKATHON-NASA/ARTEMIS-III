import ReminderController from './ReminderController'
import CompleteReminderController from './CompleteReminderController'

const Api = {
    ReminderController: Object.assign(ReminderController, ReminderController),
    CompleteReminderController: Object.assign(CompleteReminderController, CompleteReminderController),
}

export default Api