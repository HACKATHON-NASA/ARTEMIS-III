import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
const LeaderboardController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LeaderboardController.url(options),
    method: 'get',
})

LeaderboardController.definition = {
    methods: ["get","head"],
    url: '/api/v1/gamification/leaderboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
LeaderboardController.url = (options?: RouteQueryOptions) => {
    return LeaderboardController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
LeaderboardController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LeaderboardController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
LeaderboardController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: LeaderboardController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
const LeaderboardControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LeaderboardController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
LeaderboardControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LeaderboardController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Gamificacion\Http\Controllers\Api\LeaderboardController::__invoke
* @see app/Modules/Gamificacion/Http/Controllers/Api/LeaderboardController.php:18
* @route '/api/v1/gamification/leaderboard'
*/
LeaderboardControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LeaderboardController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

LeaderboardController.form = LeaderboardControllerForm

export default LeaderboardController