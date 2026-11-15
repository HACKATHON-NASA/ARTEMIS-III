import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
const ChangeEnvironmentController = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: ChangeEnvironmentController.url(args, options),
    method: 'patch',
})

ChangeEnvironmentController.definition = {
    methods: ["patch"],
    url: '/api/v1/missions/{mission}/environment',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
ChangeEnvironmentController.url = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { mission: args }
    }

    if (Array.isArray(args)) {
        args = {
            mission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        mission: args.mission,
    }

    return ChangeEnvironmentController.definition.url
            .replace('{mission}', parsedArgs.mission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
ChangeEnvironmentController.patch = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: ChangeEnvironmentController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
const ChangeEnvironmentControllerForm = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ChangeEnvironmentController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
ChangeEnvironmentControllerForm.patch = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ChangeEnvironmentController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

ChangeEnvironmentController.form = ChangeEnvironmentControllerForm

export default ChangeEnvironmentController