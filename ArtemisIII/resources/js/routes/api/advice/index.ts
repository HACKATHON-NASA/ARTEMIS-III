import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/advice',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAdviceController.php:18
* @route '/api/v1/advice'
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
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/advice',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
export const read = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: read.url(args, options),
    method: 'patch',
})

read.definition = {
    methods: ["patch"],
    url: '/api/v1/advice/{advice}/read',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
read.url = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { advice: args }
    }

    if (Array.isArray(args)) {
        args = {
            advice: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        advice: args.advice,
    }

    return read.definition.url
            .replace('{advice}', parsedArgs.advice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
read.patch = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: read.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
const readForm = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: read.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
readForm.patch = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: read.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

read.form = readForm

const advice = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    read: Object.assign(read, read),
}

export default advice