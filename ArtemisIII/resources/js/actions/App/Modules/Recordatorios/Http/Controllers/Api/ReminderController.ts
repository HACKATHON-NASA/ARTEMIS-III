import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::index
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:23
* @route '/api/v1/reminders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/reminders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::index
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:23
* @route '/api/v1/reminders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::index
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:23
* @route '/api/v1/reminders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::index
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:23
* @route '/api/v1/reminders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::index
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:23
* @route '/api/v1/reminders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::index
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:23
* @route '/api/v1/reminders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::index
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:23
* @route '/api/v1/reminders'
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
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::store
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:28
* @route '/api/v1/reminders'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/reminders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::store
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:28
* @route '/api/v1/reminders'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::store
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:28
* @route '/api/v1/reminders'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::store
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:28
* @route '/api/v1/reminders'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::store
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:28
* @route '/api/v1/reminders'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::update
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:33
* @route '/api/v1/reminders/{reminder}'
*/
export const update = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/v1/reminders/{reminder}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::update
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:33
* @route '/api/v1/reminders/{reminder}'
*/
update.url = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reminder: args }
    }

    if (Array.isArray(args)) {
        args = {
            reminder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        reminder: args.reminder,
    }

    return update.definition.url
            .replace('{reminder}', parsedArgs.reminder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::update
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:33
* @route '/api/v1/reminders/{reminder}'
*/
update.patch = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::update
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:33
* @route '/api/v1/reminders/{reminder}'
*/
const updateForm = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::update
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:33
* @route '/api/v1/reminders/{reminder}'
*/
updateForm.patch = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::destroy
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:38
* @route '/api/v1/reminders/{reminder}'
*/
export const destroy = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/v1/reminders/{reminder}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::destroy
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:38
* @route '/api/v1/reminders/{reminder}'
*/
destroy.url = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reminder: args }
    }

    if (Array.isArray(args)) {
        args = {
            reminder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        reminder: args.reminder,
    }

    return destroy.definition.url
            .replace('{reminder}', parsedArgs.reminder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::destroy
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:38
* @route '/api/v1/reminders/{reminder}'
*/
destroy.delete = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::destroy
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:38
* @route '/api/v1/reminders/{reminder}'
*/
const destroyForm = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\ReminderController::destroy
* @see app/Modules/Recordatorios/Http/Controllers/Api/ReminderController.php:38
* @route '/api/v1/reminders/{reminder}'
*/
destroyForm.delete = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ReminderController = { index, store, update, destroy }

export default ReminderController