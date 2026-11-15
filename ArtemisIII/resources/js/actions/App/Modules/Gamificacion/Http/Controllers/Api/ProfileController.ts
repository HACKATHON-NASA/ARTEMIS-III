import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
const ProfileController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ProfileController.url(args, options),
    method: 'get',
})

ProfileController.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/gamification',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
ProfileController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ProfileController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
ProfileController.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ProfileController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
ProfileController.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ProfileController.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
const ProfileControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ProfileController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
ProfileControllerForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ProfileController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
ProfileControllerForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ProfileController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ProfileController.form = ProfileControllerForm

export default ProfileController