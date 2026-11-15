import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
const CurrentMissionController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CurrentMissionController.url(options),
    method: 'get',
})

CurrentMissionController.definition = {
    methods: ["get","head"],
    url: '/api/v1/missions/current',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
CurrentMissionController.url = (options?: RouteQueryOptions) => {
    return CurrentMissionController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
CurrentMissionController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CurrentMissionController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
CurrentMissionController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CurrentMissionController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
const CurrentMissionControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CurrentMissionController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
CurrentMissionControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CurrentMissionController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\CurrentMissionController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/CurrentMissionController.php:17
* @route '/api/v1/missions/current'
*/
CurrentMissionControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CurrentMissionController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CurrentMissionController.form = CurrentMissionControllerForm

export default CurrentMissionController