import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
const CrewOverviewController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CrewOverviewController.url(options),
    method: 'get',
})

CrewOverviewController.definition = {
    methods: ["get","head"],
    url: '/api/v1/crew/overview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
CrewOverviewController.url = (options?: RouteQueryOptions) => {
    return CrewOverviewController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
CrewOverviewController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CrewOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
CrewOverviewController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CrewOverviewController.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
const CrewOverviewControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CrewOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
CrewOverviewControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CrewOverviewController.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
CrewOverviewControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CrewOverviewController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CrewOverviewController.form = CrewOverviewControllerForm

export default CrewOverviewController