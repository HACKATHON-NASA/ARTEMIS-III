import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
const ResolveClinicalNoteController = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: ResolveClinicalNoteController.url(args, options),
    method: 'patch',
})

ResolveClinicalNoteController.definition = {
    methods: ["patch"],
    url: '/api/v1/clinical-notes/{note}/resolve',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
ResolveClinicalNoteController.url = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ResolveClinicalNoteController.definition.url
            .replace('{note}', parsedArgs.note.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
ResolveClinicalNoteController.patch = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: ResolveClinicalNoteController.url(args, options),
    method: 'patch',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ResolveClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ResolveClinicalNoteController.php:17
* @route '/api/v1/clinical-notes/{note}/resolve'
*/
const ResolveClinicalNoteControllerForm = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ResolveClinicalNoteController.url(args, {
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
ResolveClinicalNoteControllerForm.patch = (args: { note: string | number } | [note: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ResolveClinicalNoteController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

ResolveClinicalNoteController.form = ResolveClinicalNoteControllerForm

export default ResolveClinicalNoteController