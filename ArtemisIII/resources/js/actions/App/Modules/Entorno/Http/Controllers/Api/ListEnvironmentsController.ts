import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
const ListEnvironmentsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListEnvironmentsController.url(options),
    method: 'get',
})

ListEnvironmentsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/environments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
ListEnvironmentsController.url = (options?: RouteQueryOptions) => {
    return ListEnvironmentsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
ListEnvironmentsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListEnvironmentsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
ListEnvironmentsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListEnvironmentsController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
const ListEnvironmentsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListEnvironmentsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
ListEnvironmentsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListEnvironmentsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
ListEnvironmentsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListEnvironmentsController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListEnvironmentsController.form = ListEnvironmentsControllerForm

export default ListEnvironmentsController