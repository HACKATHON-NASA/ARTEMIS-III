import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
const CrewSeriesController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CrewSeriesController.url(options),
    method: 'get',
})

CrewSeriesController.definition = {
    methods: ["get","head"],
    url: '/api/v1/crew/series',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
CrewSeriesController.url = (options?: RouteQueryOptions) => {
    return CrewSeriesController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
CrewSeriesController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CrewSeriesController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
CrewSeriesController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CrewSeriesController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
const CrewSeriesControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CrewSeriesController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
CrewSeriesControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CrewSeriesController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
CrewSeriesControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CrewSeriesController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CrewSeriesController.form = CrewSeriesControllerForm

export default CrewSeriesController