import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
const ListClinicalNotesController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListClinicalNotesController.url(args, options),
    method: 'get',
})

ListClinicalNotesController.definition = {
    methods: ["get","head"],
    url: '/api/v1/astronauts/{astronaut}/clinical-notes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
ListClinicalNotesController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return ListClinicalNotesController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
ListClinicalNotesController.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListClinicalNotesController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
ListClinicalNotesController.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListClinicalNotesController.url(args, options),
    method: 'head',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
const ListClinicalNotesControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListClinicalNotesController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
ListClinicalNotesControllerForm.get = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListClinicalNotesController.url(args, options),
    method: 'get',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\ListClinicalNotesController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/ListClinicalNotesController.php:17
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
ListClinicalNotesControllerForm.head = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListClinicalNotesController.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListClinicalNotesController.form = ListClinicalNotesControllerForm

export default ListClinicalNotesController