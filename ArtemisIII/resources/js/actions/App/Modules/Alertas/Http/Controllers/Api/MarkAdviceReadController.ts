import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
const MarkAdviceReadController = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: MarkAdviceReadController.url(args, options),
    method: 'patch',
})

MarkAdviceReadController.definition = {
    methods: ["patch"],
    url: '/api/v1/advice/{advice}/read',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
MarkAdviceReadController.url = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return MarkAdviceReadController.definition.url
            .replace('{advice}', parsedArgs.advice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
MarkAdviceReadController.patch = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: MarkAdviceReadController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\MarkAdviceReadController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/MarkAdviceReadController.php:17
* @route '/api/v1/advice/{advice}/read'
*/
const MarkAdviceReadControllerForm = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: MarkAdviceReadController.url(args, {
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
MarkAdviceReadControllerForm.patch = (args: { advice: string | number } | [advice: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: MarkAdviceReadController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

MarkAdviceReadController.form = MarkAdviceReadControllerForm

export default MarkAdviceReadController