import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/devices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListDevicesController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListDevicesController.php:17
* @route '/api/v1/devices'
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

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/devices',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreDeviceController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreDeviceController.php:18
* @route '/api/v1/devices'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
export const active = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: active.url(args, options),
    method: 'patch',
})

active.definition = {
    methods: ["patch"],
    url: '/api/v1/devices/{device}/active',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
active.url = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return active.definition.url
            .replace('{device}', parsedArgs.device.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
active.patch = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: active.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetDeviceActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetDeviceActiveController.php:18
* @route '/api/v1/devices/{device}/active'
*/
const activeForm = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: active.url(args, {
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
activeForm.patch = (args: { device: string | number } | [device: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: active.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

active.form = activeForm

const devices = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    active: Object.assign(active, active),
}

export default devices