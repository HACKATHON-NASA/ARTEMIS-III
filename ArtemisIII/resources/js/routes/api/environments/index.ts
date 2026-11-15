import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/environments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListEnvironmentsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListEnvironmentsController.php:17
* @route '/api/v1/environments'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const environments = {
    index: Object.assign(index, index),
}

export default environments