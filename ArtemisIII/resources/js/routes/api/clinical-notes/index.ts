import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
export const index = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/clinical-notes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
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
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
index.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
index.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
const indexForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
indexForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
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

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
export const store = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/astronauts/{astronaut}/clinical-notes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
store.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
store.post = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
const storeForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
storeForm.post = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
export const resolve = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: resolve.url(args, options),
    method: 'patch',
})

resolve.definition = {
    methods: ["patch"],
    url: '/api/v1/clinical-notes/{note}/resolve',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
resolve.url = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { note: args }
    }

    if (Array.isArray(args)) {
        args = {
            note: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        note: args.note,
    }

    return resolve.definition.url
            .replace('{note}', parsedArgs.note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
resolve.patch = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: resolve.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
const resolveForm = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
resolveForm.patch = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: resolve.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

resolve.form = resolveForm

const clinicalNotes = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    resolve: Object.assign(resolve, resolve),
}

export default clinicalNotes