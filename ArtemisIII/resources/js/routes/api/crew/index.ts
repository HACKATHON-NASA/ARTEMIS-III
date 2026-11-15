import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
export const overview = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

overview.definition = {
    methods: ["get","head"],
    url: '/api/v1/crew/overview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
overview.url = (options?: RouteQueryOptions) => {
    return overview.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
overview.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
overview.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overview.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
const overviewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
overviewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewOverviewController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewOverviewController.php:18
* @route '/api/v1/crew/overview'
*/
overviewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

overview.form = overviewForm

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
export const series = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: series.url(options),
    method: 'get',
})

series.definition = {
    methods: ["get","head"],
    url: '/api/v1/crew/series',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
series.url = (options?: RouteQueryOptions) => {
    return series.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
series.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: series.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
series.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: series.url(options),
    method: 'head',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
const seriesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: series.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
seriesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: series.url(options),
    method: 'get',
})

/**
* @see \App\Modules\Dashboard\Http\Controllers\Api\CrewSeriesController::__invoke
* @see app/Modules/Dashboard/Http/Controllers/Api/CrewSeriesController.php:19
* @route '/api/v1/crew/series'
*/
seriesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: series.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

series.form = seriesForm

const crew = {
    overview: Object.assign(overview, overview),
    series: Object.assign(series, series),
}

export default crew