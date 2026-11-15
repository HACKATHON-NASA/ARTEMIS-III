import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
const ListUsersController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListUsersController.url(options),
    method: 'get',
})

ListUsersController.definition = {
    methods: ["get","head"],
    url: '/api/v1/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
ListUsersController.url = (options?: RouteQueryOptions) => {
    return ListUsersController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
ListUsersController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListUsersController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
ListUsersController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListUsersController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
const ListUsersControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListUsersController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
ListUsersControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListUsersController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
ListUsersControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListUsersController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListUsersController.form = ListUsersControllerForm

export default ListUsersController