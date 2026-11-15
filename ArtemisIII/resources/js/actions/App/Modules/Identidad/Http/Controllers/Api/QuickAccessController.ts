import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::index
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:22
* @route '/api/v1/auth/quick-access'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/auth/quick-access',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::index
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:22
* @route '/api/v1/auth/quick-access'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::index
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:22
* @route '/api/v1/auth/quick-access'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::index
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:22
* @route '/api/v1/auth/quick-access'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::index
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:22
* @route '/api/v1/auth/quick-access'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::index
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:22
* @route '/api/v1/auth/quick-access'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::index
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:22
* @route '/api/v1/auth/quick-access'
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
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::store
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:32
* @route '/api/v1/auth/quick-access'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/auth/quick-access',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::store
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:32
* @route '/api/v1/auth/quick-access'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::store
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:32
* @route '/api/v1/auth/quick-access'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::store
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:32
* @route '/api/v1/auth/quick-access'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\QuickAccessController::store
* @see app/Modules/Identidad/Http/Controllers/Api/QuickAccessController.php:32
* @route '/api/v1/auth/quick-access'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const QuickAccessController = { index, store }

export default QuickAccessController