import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/alerts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListAlertsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListAlertsController.php:18
* @route '/api/v1/alerts'
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
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/alerts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
export const acknowledge = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: acknowledge.url(args, options),
    method: 'patch',
})

acknowledge.definition = {
    methods: ["patch"],
    url: '/api/v1/alerts/{alert}/acknowledge',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
acknowledge.url = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { alert: args }
    }

    if (Array.isArray(args)) {
        args = {
            alert: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        alert: args.alert,
    }

    return acknowledge.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
acknowledge.patch = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: acknowledge.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
const acknowledgeForm = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: acknowledge.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
acknowledgeForm.patch = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: acknowledge.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

acknowledge.form = acknowledgeForm

const alerts = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    acknowledge: Object.assign(acknowledge, acknowledge),
}

export default alerts