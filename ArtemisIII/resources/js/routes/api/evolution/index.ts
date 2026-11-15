import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
export const show = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/evolution',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
show.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
show.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
show.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
const showForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
showForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
showForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const evolution = {
    show: Object.assign(show, show),
}

export default evolution