import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
const StoreDeviceController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreDeviceController.url(options),
    method: 'post',
})

StoreDeviceController.definition = {
    methods: ["post"],
    url: '/api/v1/devices',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
StoreDeviceController.url = (options?: RouteQueryOptions) => {
    return StoreDeviceController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
StoreDeviceController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreDeviceController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
const StoreDeviceControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreDeviceController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
StoreDeviceControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreDeviceController.url(options),
    method: 'post',
})

StoreDeviceController.form = StoreDeviceControllerForm

export default StoreDeviceController