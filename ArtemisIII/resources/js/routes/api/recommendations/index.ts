import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\ListRecommendationsController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/ListRecommendationsController.php:17
* @route '/api/v1/recommendations'
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
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
export const update = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/v1/recommendations/{indicator}/{status}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
update.url = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{indicator}', parsedArgs.indicator.toString())
            .replace('{status}', parsedArgs.status.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
update.put = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SaveRecommendationController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SaveRecommendationController.php:18
* @route '/api/v1/recommendations/{indicator}/{status}'
*/
const updateForm = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
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
updateForm.put = (args: { indicator: string | number, status: string | number } | [indicator: string | number, status: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const recommendations = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
}

export default recommendations