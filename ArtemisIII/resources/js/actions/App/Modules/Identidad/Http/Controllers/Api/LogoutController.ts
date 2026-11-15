import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
const LogoutController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: LogoutController.url(options),
    method: 'post',
})

LogoutController.definition = {
    methods: ["post"],
    url: '/api/v1/auth/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
LogoutController.url = (options?: RouteQueryOptions) => {
    return LogoutController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
LogoutController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: LogoutController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
const LogoutControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: LogoutController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
LogoutControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: LogoutController.url(options),
    method: 'post',
})

LogoutController.form = LogoutControllerForm

export default LogoutController