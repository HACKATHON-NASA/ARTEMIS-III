import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
export const show = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/body-status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
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
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
show.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
show.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
const showForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
showForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
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

const bodyStatus = {
    show: Object.assign(show, show),
}

export default bodyStatus