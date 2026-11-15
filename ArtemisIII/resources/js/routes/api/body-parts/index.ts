import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/body-parts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListBodyPartsController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListBodyPartsController.php:17
* @route '/api/v1/body-parts'
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

const bodyParts = {
    index: Object.assign(index, index),
}

export default bodyParts