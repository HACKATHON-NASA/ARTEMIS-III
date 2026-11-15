import ListBodyPartsController from './ListBodyPartsController'
import ShowBodyStatusController from './ShowBodyStatusController'
import ListClinicalNotesController from './ListClinicalNotesController'
import StoreClinicalNoteController from './StoreClinicalNoteController'
import ResolveClinicalNoteController from './ResolveClinicalNoteController'

const Api = {
    ListBodyPartsController: Object.assign(ListBodyPartsController, ListBodyPartsController),
    ShowBodyStatusController: Object.assign(ShowBodyStatusController, ShowBodyStatusController),
    ListClinicalNotesController: Object.assign(ListClinicalNotesController, ListClinicalNotesController),
    StoreClinicalNoteController: Object.assign(StoreClinicalNoteController, StoreClinicalNoteController),
    ResolveClinicalNoteController: Object.assign(ResolveClinicalNoteController, ResolveClinicalNoteController),
}

export default Api