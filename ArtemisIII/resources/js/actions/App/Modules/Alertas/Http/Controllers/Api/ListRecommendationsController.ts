import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
const ListRecommendationsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListRecommendationsController.url(options),
    method: 'get',
})

ListRecommendationsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
ListRecommendationsController.url = (options?: RouteQueryOptions) => {
    return ListRecommendationsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
ListRecommendationsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListRecommendationsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
ListRecommendationsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListRecommendationsController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
const ListRecommendationsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListRecommendationsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
ListRecommendationsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListRecommendationsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
ListRecommendationsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListRecommendationsController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListRecommendationsController.form = ListRecommendationsControllerForm

export default ListRecommendationsController