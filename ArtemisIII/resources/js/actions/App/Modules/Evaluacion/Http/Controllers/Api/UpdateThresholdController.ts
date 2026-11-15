import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
const UpdateThresholdController = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: UpdateThresholdController.url(args, options),
    method: 'put',
})

UpdateThresholdController.definition = {
    methods: ["put"],
    url: '/api/v1/thresholds/{indicator}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
UpdateThresholdController.url = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return UpdateThresholdController.definition.url
            .replace('{indicator}', parsedArgs.indicator.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
UpdateThresholdController.put = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: UpdateThresholdController.url(args, options),
    method: 'put',
})

/**
* @see \App\Modules\Evaluacion\Http\Controllers\Api\UpdateThresholdController::__invoke
* @see app/Modules/Evaluacion/Http/Controllers/Api/UpdateThresholdController.php:18
* @route '/api/v1/thresholds/{indicator}'
*/
const UpdateThresholdControllerForm = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: UpdateThresholdController.url(args, {
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
UpdateThresholdControllerForm.put = (args: { indicator: string | number } | [indicator: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: UpdateThresholdController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

UpdateThresholdController.form = UpdateThresholdControllerForm

export default UpdateThresholdController