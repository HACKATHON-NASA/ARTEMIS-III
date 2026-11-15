import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

login.definition = {
    methods: ["post"],
    url: '/api/v1/auth/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
login.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
const loginForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: login.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LoginController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LoginController.php:20
* @route '/api/v1/auth/login'
*/
loginForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: login.url(options),
    method: 'post',
})

login.form = loginForm

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
export const refresh = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refresh.url(options),
    method: 'post',
})

refresh.definition = {
    methods: ["post"],
    url: '/api/v1/auth/refresh',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
refresh.url = (options?: RouteQueryOptions) => {
    return refresh.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
refresh.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: refresh.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
const refreshForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refresh.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\RefreshController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/RefreshController.php:20
* @route '/api/v1/auth/refresh'
*/
refreshForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: refresh.url(options),
    method: 'post',
})

refresh.form = refreshForm

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
export const deviceToken = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deviceToken.url(options),
    method: 'post',
})

deviceToken.definition = {
    methods: ["post"],
    url: '/api/v1/auth/device-token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
deviceToken.url = (options?: RouteQueryOptions) => {
    return deviceToken.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
deviceToken.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deviceToken.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
const deviceTokenForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deviceToken.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
deviceTokenForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deviceToken.url(options),
    method: 'post',
})

deviceToken.form = deviceTokenForm

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/api/v1/auth/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
const logoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\LogoutController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/LogoutController.php:18
* @route '/api/v1/auth/logout'
*/
logoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

logout.form = logoutForm

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
export const me = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

me.definition = {
    methods: ["get","head"],
    url: '/api/v1/auth/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
me.url = (options?: RouteQueryOptions) => {
    return me.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
me.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
me.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: me.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
const meForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
meForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\MeController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/MeController.php:17
* @route '/api/v1/auth/me'
*/
meForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: me.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

me.form = meForm

const auth = {
    login: Object.assign(login, login),
    refresh: Object.assign(refresh, refresh),
    deviceToken: Object.assign(deviceToken, deviceToken),
    logout: Object.assign(logout, logout),
    me: Object.assign(me, me),
}

export default auth