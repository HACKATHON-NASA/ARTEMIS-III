import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
const ListAlertsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListAlertsController.url(options),
    method: 'get',
})

ListAlertsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/alerts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
ListAlertsController.url = (options?: RouteQueryOptions) => {
    return ListAlertsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
ListAlertsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListAlertsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
ListAlertsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListAlertsController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
const ListAlertsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListAlertsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
ListAlertsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListAlertsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
ListAlertsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListAlertsController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListAlertsController.form = ListAlertsControllerForm

export default ListAlertsController