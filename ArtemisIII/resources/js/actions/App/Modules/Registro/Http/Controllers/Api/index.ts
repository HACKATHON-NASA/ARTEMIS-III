import ListIndicatorsController from './ListIndicatorsController'
import RecordMeasurementsController from './RecordMeasurementsController'
import IngestReadingsController from './IngestReadingsController'
import ListMeasurementsController from './ListMeasurementsController'

const Api = {
    ListIndicatorsController: Object.assign(ListIndicatorsController, ListIndicatorsController),
    RecordMeasurementsController: Object.assign(RecordMeasurementsController, RecordMeasurementsController),
    IngestReadingsController: Object.assign(IngestReadingsController, IngestReadingsController),
    ListMeasurementsController: Object.assign(ListMeasurementsController, ListMeasurementsController),
}

export default Api