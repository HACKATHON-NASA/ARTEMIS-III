import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/astronauts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
export const update = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/v1/astronauts/{astronaut}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
update.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
update.patch = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\UpdateAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/UpdateAstronautController.php:18
* @route '/api/v1/astronauts/{astronaut}'
*/
const updateForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
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
updateForm.patch = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const astronauts = {
    store: Object.assign(store, store),
    update: Object.assign(update, update),
}

export default astronauts