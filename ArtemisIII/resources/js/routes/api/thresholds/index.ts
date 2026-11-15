import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/thresholds',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\ListThresholdsController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/ListThresholdsController.php:18
* @route '/api/v1/thresholds'
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
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
export const update = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/v1/thresholds/{indicator}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
update.url = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { indicator: args }
    }

    if (Array.isArray(args)) {
        args = {
            indicator: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        indicator: args.indicator,
    }

    return update.definition.url
            .replace('{indicator}', parsedArgs.indicator.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
update.put = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
const updateForm = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
updateForm.put = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const thresholds = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
}

export default thresholds