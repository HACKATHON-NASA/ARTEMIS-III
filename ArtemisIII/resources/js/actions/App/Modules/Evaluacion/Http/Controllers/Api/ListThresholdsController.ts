import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
const ListThresholdsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListThresholdsController.url(options),
    method: 'get',
})

ListThresholdsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/thresholds',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
ListThresholdsController.url = (options?: RouteQueryOptions) => {
    return ListThresholdsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
ListThresholdsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListThresholdsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
ListThresholdsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListThresholdsController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
const ListThresholdsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListThresholdsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
ListThresholdsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListThresholdsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
ListThresholdsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListThresholdsController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListThresholdsController.form = ListThresholdsControllerForm

export default ListThresholdsController