import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
const MeController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: MeController.url(options),
    method: 'get',
})

MeController.definition = {
    methods: ["get","head"],
    url: '/api/v1/auth/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
MeController.url = (options?: RouteQueryOptions) => {
    return MeController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
MeController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: MeController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
MeController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: MeController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
const MeControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MeController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
MeControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MeController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
MeControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: MeController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

MeController.form = MeControllerForm

export default MeController