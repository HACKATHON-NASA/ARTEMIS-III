import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
const SendMessageController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: SendMessageController.url(options),
    method: 'post',
})

SendMessageController.definition = {
    methods: ["post"],
    url: '/api/v1/alerts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
SendMessageController.url = (options?: RouteQueryOptions) => {
    return SendMessageController.definition.url + queryParams(options)
}

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
SendMessageController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: SendMessageController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
const SendMessageControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SendMessageController.url(options),
    method: 'post',
})

/**
* @see \App\Modules\Alertas\Http\Controllers\Api\SendMessageController::__invoke
* @see app/Modules/Alertas/Http/Controllers/Api/SendMessageController.php:18
* @route '/api/v1/alerts'
*/
SendMessageControllerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: SendMessageController.url(options),
    method: 'post',
})

SendMessageController.form = SendMessageControllerForm

export default SendMessageController