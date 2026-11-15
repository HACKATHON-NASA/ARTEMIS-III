import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
const RecordMeasurementsController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RecordMeasurementsController.url(options),
    method: 'post',
})

RecordMeasurementsController.definition = {
    methods: ["post"],
    url: '/api/v1/measurements',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
RecordMeasurementsController.url = (options?: RouteQueryOptions) => {
    return RecordMeasurementsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
RecordMeasurementsController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RecordMeasurementsController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
const RecordMeasurementsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RecordMeasurementsController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
RecordMeasurementsControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RecordMeasurementsController.url(options),
    method: 'post',
})

RecordMeasurementsController.form = RecordMeasurementsControllerForm

export default RecordMeasurementsController