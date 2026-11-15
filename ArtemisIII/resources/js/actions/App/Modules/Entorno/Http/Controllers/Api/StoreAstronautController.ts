import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
const StoreAstronautController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreAstronautController.url(options),
    method: 'post',
})

StoreAstronautController.definition = {
    methods: ["post"],
    url: '/api/v1/astronauts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
StoreAstronautController.url = (options?: RouteQueryOptions) => {
    return StoreAstronautController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
StoreAstronautController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: StoreAstronautController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
const StoreAstronautControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreAstronautController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Entorno\Http\Controllers\Api\StoreAstronautController::__invoke
* @see app/Modules/Entorno/Http/Controllers/Api/StoreAstronautController.php:18
* @route '/api/v1/astronauts'
*/
StoreAstronautControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: StoreAstronautController.url(options),
    method: 'post',
})

StoreAstronautController.form = StoreAstronautControllerForm

export default StoreAstronautController