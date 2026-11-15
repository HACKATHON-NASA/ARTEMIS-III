import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
const ListAdviceController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListAdviceController.url(options),
    method: 'get',
})

ListAdviceController.definition = {
    methods: ["get","head"],
    url: '/api/v1/advice',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
ListAdviceController.url = (options?: RouteQueryOptions) => {
    return ListAdviceController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
ListAdviceController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListAdviceController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
ListAdviceController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListAdviceController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
const ListAdviceControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListAdviceController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
ListAdviceControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListAdviceController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
ListAdviceControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListAdviceController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListAdviceController.form = ListAdviceControllerForm

export default ListAdviceController