import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
const ListDevicesController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListDevicesController.url(options),
    method: 'get',
})

ListDevicesController.definition = {
    methods: ["get","head"],
    url: '/api/v1/devices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
ListDevicesController.url = (options?: RouteQueryOptions) => {
    return ListDevicesController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
ListDevicesController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListDevicesController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
ListDevicesController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListDevicesController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
const ListDevicesControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListDevicesController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
ListDevicesControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListDevicesController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
ListDevicesControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListDevicesController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListDevicesController.form = ListDevicesControllerForm

export default ListDevicesController