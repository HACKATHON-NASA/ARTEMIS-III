import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
const ShowBodyStatusController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowBodyStatusController.url(args, options),
    method: 'get',
})

ShowBodyStatusController.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/body-status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
ShowBodyStatusController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ShowBodyStatusController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
ShowBodyStatusController.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ShowBodyStatusController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
ShowBodyStatusController.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ShowBodyStatusController.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
const ShowBodyStatusControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ShowBodyStatusController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
ShowBodyStatusControllerForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ShowBodyStatusController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ShowBodyStatusController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ShowBodyStatusController.php:19
* @route '/api/v1/astronauts/{astronaut}/body-status'
*/
ShowBodyStatusControllerForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ShowBodyStatusController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ShowBodyStatusController.form = ShowBodyStatusControllerForm

export default ShowBodyStatusController