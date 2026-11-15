import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
const StoreClinicalNoteController = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreClinicalNoteController.url(args, options),
    method: 'post',
})

StoreClinicalNoteController.definition = {
    methods: ["post"],
    url: '/api/v1/astronauts/{astronaut}/clinical-notes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
StoreClinicalNoteController.url = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return StoreClinicalNoteController.definition.url
            .replace('{astronaut}', parsedArgs.astronaut.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
StoreClinicalNoteController.post = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreClinicalNoteController.url(args, options),
    method: 'post',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
const StoreClinicalNoteControllerForm = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreClinicalNoteController.url(args, options),
    method: 'post',
})

/**
* @see \App\Modules\Cuerpo\Http\Controllers\Api\StoreClinicalNoteController::__invoke
* @see app/Modules/Cuerpo/Http/Controllers/Api/StoreClinicalNoteController.php:18
* @route '/api/v1/astronauts/{astronaut}/clinical-notes'
*/
StoreClinicalNoteControllerForm.post = (args: { astronaut: string | number } | [astronaut: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreClinicalNoteController.url(args, options),
    method: 'post',
})

StoreClinicalNoteController.form = StoreClinicalNoteControllerForm

export default StoreClinicalNoteController