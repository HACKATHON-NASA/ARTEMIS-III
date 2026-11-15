import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
export const readings = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readings.url(options),
    method: 'post',
})

readings.definition = {
    methods: ["post"],
    url: '/api/v1/ingest/readings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
readings.url = (options?: RouteQueryOptions) => {
    return readings.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
readings.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: readings.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
const readingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: readings.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
readingsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: readings.url(options),
    method: 'post',
})

readings.form = readingsForm

const ingest = {
    readings: Object.assign(readings, readings),
}

export default ingest