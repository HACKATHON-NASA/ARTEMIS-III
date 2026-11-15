import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
const SetDeviceActiveController = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: SetDeviceActiveController.url(args, options),
    method: 'patch',
})

SetDeviceActiveController.definition = {
    methods: ["patch"],
    url: '/api/v1/devices/{device}/active',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
SetDeviceActiveController.url = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { device: args }
    }

    if (Array.isArray(args)) {
        args = {
            device: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        device: args.device,
    }

    return SetDeviceActiveController.definition.url
            .replace('{device}', parsedArgs.device.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
SetDeviceActiveController.patch = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: SetDeviceActiveController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
const SetDeviceActiveControllerForm = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SetDeviceActiveController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
SetDeviceActiveControllerForm.patch = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SetDeviceActiveController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

SetDeviceActiveController.form = SetDeviceActiveControllerForm

export default SetDeviceActiveController