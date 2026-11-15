import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
const UpdateAstronautController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: UpdateAstronautController.url(args, options),
    method: 'patch',
})

UpdateAstronautController.definition = {
    methods: ["patch"],
    url: '/api/v1/astronauts/{astronaut}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
UpdateAstronautController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { astronaut: args }
    }

    if (Array.isArray(args)) {
        args = {
            astronaut: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        astronaut: args.astronaut,
    }

    return UpdateAstronautController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
UpdateAstronautController.patch = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: UpdateAstronautController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
const UpdateAstronautControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: UpdateAstronautController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
UpdateAstronautControllerForm.patch = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: UpdateAstronautController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

UpdateAstronautController.form = UpdateAstronautControllerForm

export default UpdateAstronautController