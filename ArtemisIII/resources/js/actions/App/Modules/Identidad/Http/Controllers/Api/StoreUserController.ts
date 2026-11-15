import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
const StoreUserController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreUserController.url(options),
    method: 'post',
})

StoreUserController.definition = {
    methods: ["post"],
    url: '/api/v1/users',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
StoreUserController.url = (options?: RouteQueryOptions) => {
    return StoreUserController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
StoreUserController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreUserController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
const StoreUserControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreUserController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
StoreUserControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreUserController.url(options),
    method: 'post',
})

StoreUserController.form = StoreUserControllerForm

export default StoreUserController