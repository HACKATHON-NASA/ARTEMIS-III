import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
const EvolutionController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EvolutionController.url(args, options),
    method: 'get',
})

EvolutionController.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/evolution',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
EvolutionController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EvolutionController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
EvolutionController.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EvolutionController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
EvolutionController.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EvolutionController.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
const EvolutionControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EvolutionController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
EvolutionControllerForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EvolutionController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\EvolutionController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/EvolutionController.php:18
* @route '/api/v1/astronauts/{astronaut}/evolution'
*/
EvolutionControllerForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EvolutionController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

EvolutionController.form = EvolutionControllerForm

export default EvolutionController