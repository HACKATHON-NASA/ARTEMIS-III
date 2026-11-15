import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
const IngestReadingsController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: IngestReadingsController.url(options),
    method: 'post',
})

IngestReadingsController.definition = {
    methods: ["post"],
    url: '/api/v1/ingest/readings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
IngestReadingsController.url = (options?: RouteQueryOptions) => {
    return IngestReadingsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
IngestReadingsController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: IngestReadingsController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
const IngestReadingsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: IngestReadingsController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\IngestReadingsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/IngestReadingsController.php:19
* @route '/api/v1/ingest/readings'
*/
IngestReadingsControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: IngestReadingsController.url(options),
    method: 'post',
})

IngestReadingsController.form = IngestReadingsControllerForm

export default IngestReadingsController