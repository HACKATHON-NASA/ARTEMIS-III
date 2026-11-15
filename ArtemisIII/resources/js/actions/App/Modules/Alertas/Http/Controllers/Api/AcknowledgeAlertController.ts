import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
const AcknowledgeAlertController = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: AcknowledgeAlertController.url(args, options),
    method: 'patch',
})

AcknowledgeAlertController.definition = {
    methods: ["patch"],
    url: '/api/v1/alerts/{alert}/acknowledge',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
AcknowledgeAlertController.url = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return AcknowledgeAlertController.definition.url
            .replace('{alert}', parsedArgs.alert.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
AcknowledgeAlertController.patch = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: AcknowledgeAlertController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\AcknowledgeAlertController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/AcknowledgeAlertController.php:18
* @route '/api/v1/alerts/{alert}/acknowledge'
*/
const AcknowledgeAlertControllerForm = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: AcknowledgeAlertController.url(args, {
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
AcknowledgeAlertControllerForm.patch = (args: { alert: string | number } | [alert: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: AcknowledgeAlertController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

AcknowledgeAlertController.form = AcknowledgeAlertControllerForm

export default AcknowledgeAlertController