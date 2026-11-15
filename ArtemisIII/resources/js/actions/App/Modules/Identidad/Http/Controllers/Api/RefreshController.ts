import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
const RefreshController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RefreshController.url(options),
    method: 'post',
})

RefreshController.definition = {
    methods: ["post"],
    url: '/api/v1/auth/refresh',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
RefreshController.url = (options?: RouteQueryOptions) => {
    return RefreshController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
RefreshController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RefreshController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
const RefreshControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RefreshController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
RefreshControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: RefreshController.url(options),
    method: 'post',
})

RefreshController.form = RefreshControllerForm

export default RefreshController