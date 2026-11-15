import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
const DeviceTokenController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: DeviceTokenController.url(options),
    method: 'post',
})

DeviceTokenController.definition = {
    methods: ["post"],
    url: '/api/v1/auth/device-token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
DeviceTokenController.url = (options?: RouteQueryOptions) => {
    return DeviceTokenController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
DeviceTokenController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: DeviceTokenController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
const DeviceTokenControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: DeviceTokenController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\DeviceTokenController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/DeviceTokenController.php:18
* @route '/api/v1/auth/device-token'
*/
DeviceTokenControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: DeviceTokenController.url(options),
    method: 'post',
})

DeviceTokenController.form = DeviceTokenControllerForm

export default DeviceTokenController