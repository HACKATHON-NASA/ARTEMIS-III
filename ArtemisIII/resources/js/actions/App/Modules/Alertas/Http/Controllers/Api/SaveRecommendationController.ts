import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
const SaveRecommendationController = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: SaveRecommendationController.url(args, options),
    method: 'put',
})

SaveRecommendationController.definition = {
    methods: ["put"],
    url: '/api/v1/recommendations/{indicator}/{status}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
SaveRecommendationController.url = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            indicator: args[0],
            status: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        indicator: args.indicator,
        status: args.status,
    }

    return SaveRecommendationController.definition.url
            .replace('{indicator}', parsedArgs.indicator.toString())
            .replace('{status}', parsedArgs.status.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
SaveRecommendationController.put = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: SaveRecommendationController.url(args, options),
    method: 'put',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
const SaveRecommendationControllerForm = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SaveRecommendationController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
SaveRecommendationControllerForm.put = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SaveRecommendationController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

SaveRecommendationController.form = SaveRecommendationControllerForm

export default SaveRecommendationController