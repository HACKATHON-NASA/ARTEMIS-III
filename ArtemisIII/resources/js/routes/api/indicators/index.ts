import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/indicators',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListIndicatorsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListIndicatorsController.php:17
* @route '/api/v1/indicators'
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

const indicators = {
    index: Object.assign(index, index),
}

export default indicators