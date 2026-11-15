import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
const ListMeasurementsController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMeasurementsController.url(args, options),
    method: 'get',
})

ListMeasurementsController.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/measurements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
ListMeasurementsController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ListMeasurementsController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
ListMeasurementsController.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMeasurementsController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
ListMeasurementsController.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListMeasurementsController.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
const ListMeasurementsControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMeasurementsController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
ListMeasurementsControllerForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMeasurementsController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
ListMeasurementsControllerForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListMeasurementsController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListMeasurementsController.form = ListMeasurementsControllerForm

export default ListMeasurementsController