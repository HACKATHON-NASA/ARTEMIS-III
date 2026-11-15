import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
export const profile = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(args, options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/gamification',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
profile.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return profile.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
profile.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
profile.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
const profileForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
profileForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\ProfileController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/ProfileController.php:18
* @route '/api/v1/astronauts/{astronaut}/gamification'
*/
profileForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

profile.form = profileForm

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
export const leaderboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: leaderboard.url(options),
    method: 'get',
})

leaderboard.definition = {
    methods: ["get","head"],
    url: '/api/v1/gamification/leaderboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
leaderboard.url = (options?: RouteQueryOptions) => {
    return leaderboard.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
leaderboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: leaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
leaderboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: leaderboard.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
const leaderboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: leaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
leaderboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: leaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
leaderboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: leaderboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

leaderboard.form = leaderboardForm

const gamification = {
    profile: Object.assign(profile, profile),
    leaderboard: Object.assign(leaderboard, leaderboard),
}

export default gamification