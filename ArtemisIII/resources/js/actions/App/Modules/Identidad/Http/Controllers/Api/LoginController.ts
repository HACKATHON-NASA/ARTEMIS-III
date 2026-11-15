import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
const LoginController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: LoginController.url(options),
    method: 'post',
})

LoginController.definition = {
    methods: ["post"],
    url: '/api/v1/auth/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
LoginController.url = (options?: RouteQueryOptions) => {
    return LoginController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
LoginController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: LoginController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
const LoginControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: LoginController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
LoginControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: LoginController.url(options),
    method: 'post',
})

LoginController.form = LoginControllerForm

export default LoginController