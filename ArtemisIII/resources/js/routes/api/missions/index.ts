import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/missions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ListMissionsController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ListMissionsController.php:17
* @route '/api/v1/missions'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
export const current = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: current.url(options),
    method: 'get',
})

current.definition = {
    methods: ["get","head"],
    url: '/api/v1/missions/current',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
current.url = (options?: RouteQueryOptions) => {
    return current.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
current.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: current.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
current.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: current.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
const currentForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: current.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
currentForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: current.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
currentForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: current.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

current.form = currentForm

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/missions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
export const environment = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: environment.url(args, options),
    method: 'patch',
})

environment.definition = {
    methods: ["patch"],
    url: '/api/v1/missions/{mission}/environment',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
environment.url = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { mission: args }
    }

    if (Array.isArray(args)) {
        args = {
            mission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        mission: args.mission,
    }

    return environment.definition.url
            .replace('{mission}', parsedArgs.mission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
environment.patch = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: environment.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
const environmentForm = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: environment.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\ChangeEnvironmentController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/ChangeEnvironmentController.php:19
* @route '/api/v1/missions/{mission}/environment'
*/
environmentForm.patch = (args: { mission: string | number } | [mission: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: environment.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

environment.form = environmentForm

const missions = {
    index: Object.assign(index, index),
    current: Object.assign(current, current),
    store: Object.assign(store, store),
    environment: Object.assign(environment, environment),
}

export default missions