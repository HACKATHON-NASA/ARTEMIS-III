import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/users',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\ListUsersController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/ListUsersController.php:17
* @route '/api/v1/users'
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
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/users',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\StoreUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/StoreUserController.php:18
* @route '/api/v1/users'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
export const update = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/v1/users/{user}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
update.url = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: args.user,
    }

    return update.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
update.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
const updateForm = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\UpdateUserController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/UpdateUserController.php:18
* @route '/api/v1/users/{user}'
*/
updateForm.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
export const active = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: active.url(args, options),
    method: 'patch',
})

active.definition = {
    methods: ["patch"],
    url: '/api/v1/users/{user}/active',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
active.url = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

    if (Array.isArray(args)) {
        args = {
            user: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user: args.user,
    }

    return active.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
active.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: active.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
const activeForm = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: active.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Identidad\Http\Controllers\Api\SetUserActiveController::__invoke
* @see app/Modules/Identidad/Http/Controllers/Api/SetUserActiveController.php:18
* @route '/api/v1/users/{user}/active'
*/
activeForm.patch = (args: { user: string | number } | [user: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: active.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

active.form = activeForm

const users = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    active: Object.assign(active, active),
}

export default users