import ListEnvironmentsController from './ListEnvironmentsController'
import ListMissionsController from './ListMissionsController'
import CurrentMissionController from './CurrentMissionController'
import StoreMissionController from './StoreMissionController'
import ChangeEnvironmentController from './ChangeEnvironmentController'
import StoreAstronautController from './StoreAstronautController'
import UpdateAstronautController from './UpdateAstronautController'

const Api = {
    ListEnvironmentsController: Object.assign(ListEnvironmentsController, ListEnvironmentsController),
    ListMissionsController: Object.assign(ListMissionsController, ListMissionsController),
    CurrentMissionController: Object.assign(CurrentMissionController, CurrentMissionController),
    StoreMissionController: Object.assign(StoreMissionController, StoreMissionController),
    ChangeEnvironmentController: Object.assign(ChangeEnvironmentController, ChangeEnvironmentController),
    StoreAstronautController: Object.assign(StoreAstronautController, StoreAstronautController),
    UpdateAstronautController: Object.assign(UpdateAstronautController, UpdateAstronautController),
}

export default Api