import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
const StoreMissionController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreMissionController.url(options),
    method: 'post',
})

StoreMissionController.definition = {
    methods: ["post"],
    url: '/api/v1/missions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
StoreMissionController.url = (options?: RouteQueryOptions) => {
    return StoreMissionController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
StoreMissionController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreMissionController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
const StoreMissionControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreMissionController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreMissionController.php:18
* @route '/api/v1/missions'
*/
StoreMissionControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreMissionController.url(options),
    method: 'post',
})

StoreMissionController.form = StoreMissionControllerForm

export default StoreMissionController