import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
const ListMissionsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMissionsController.url(options),
    method: 'get',
})

ListMissionsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/missions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
ListMissionsController.url = (options?: RouteQueryOptions) => {
    return ListMissionsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
ListMissionsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMissionsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
ListMissionsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListMissionsController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
const ListMissionsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMissionsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
ListMissionsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMissionsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
ListMissionsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMissionsController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListMissionsController.form = ListMissionsControllerForm

export default ListMissionsController