import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
const SendAdviceController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: SendAdviceController.url(options),
    method: 'post',
})

SendAdviceController.definition = {
    methods: ["post"],
    url: '/api/v1/advice',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
SendAdviceController.url = (options?: RouteQueryOptions) => {
    return SendAdviceController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
SendAdviceController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: SendAdviceController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
const SendAdviceControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SendAdviceController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendAdviceController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendAdviceController.php:18
* @route '/api/v1/advice'
*/
SendAdviceControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SendAdviceController.url(options),
    method: 'post',
})

SendAdviceController.form = SendAdviceControllerForm

export default SendAdviceController