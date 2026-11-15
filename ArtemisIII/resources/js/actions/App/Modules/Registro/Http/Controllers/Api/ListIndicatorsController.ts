import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
const ListIndicatorsController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListIndicatorsController.url(options),
    method: 'get',
})

ListIndicatorsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/indicators',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
ListIndicatorsController.url = (options?: RouteQueryOptions) => {
    return ListIndicatorsController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
ListIndicatorsController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListIndicatorsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
ListIndicatorsController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListIndicatorsController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
const ListIndicatorsControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListIndicatorsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
ListIndicatorsControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListIndicatorsController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
ListIndicatorsControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListIndicatorsController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListIndicatorsController.form = ListIndicatorsControllerForm

export default ListIndicatorsController