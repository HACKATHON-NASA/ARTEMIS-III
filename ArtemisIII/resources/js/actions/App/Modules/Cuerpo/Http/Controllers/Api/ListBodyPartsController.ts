import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
const ListBodyPartsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListBodyPartsController.url(options),
    method: 'get',
})

ListBodyPartsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/body-parts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
ListBodyPartsController.url = (options?: RouteQueryOptions) => {
    return ListBodyPartsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
ListBodyPartsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListBodyPartsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
ListBodyPartsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListBodyPartsController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
const ListBodyPartsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBodyPartsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
ListBodyPartsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBodyPartsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
ListBodyPartsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBodyPartsController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListBodyPartsController.form = ListBodyPartsControllerForm

export default ListBodyPartsController