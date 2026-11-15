import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/measurements',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\RecordMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/RecordMeasurementsController.php:18
* @route '/api/v1/measurements'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
export const index = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/measurements',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
index.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
index.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
index.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
const indexForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
indexForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Registro\Http\Controllers\Api\ListMeasurementsController::__invoke
* @see app/Modules/Registro/Http/Controllers/Api/ListMeasurementsController.php:18
* @route '/api/v1/astronauts/{astronaut}/measurements'
*/
indexForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const measurements = {
    store: Object.assign(store, store),
    index: Object.assign(index, index),
}

export default measurements