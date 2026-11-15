import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../../wayfinder'
/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\CompleteReminderController::__invoke
* @see app/Modules/Recordatorios/Http/Controllers/Api/CompleteReminderController.php:18
* @route '/api/v1/reminders/{reminder}/done'
*/
const CompleteReminderController = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: CompleteReminderController.url(args, options),
    method: 'post',
})

CompleteReminderController.definition = {
    methods: ["post"],
    url: '/api/v1/reminders/{reminder}/done',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\CompleteReminderController::__invoke
* @see app/Modules/Recordatorios/Http/Controllers/Api/CompleteReminderController.php:18
* @route '/api/v1/reminders/{reminder}/done'
*/
CompleteReminderController.url = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { reminder: args }
    }

    if (Array.isArray(args)) {
        args = {
            reminder: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        reminder: args.reminder,
    }

    return CompleteReminderController.definition.url
            .replace('{reminder}', parsedArgs.reminder.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\CompleteReminderController::__invoke
* @see app/Modules/Recordatorios/Http/Controllers/Api/CompleteReminderController.php:18
* @route '/api/v1/reminders/{reminder}/done'
*/
CompleteReminderController.post = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: CompleteReminderController.url(args, options),
    method: 'post',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\CompleteReminderController::__invoke
* @see app/Modules/Recordatorios/Http/Controllers/Api/CompleteReminderController.php:18
* @route '/api/v1/reminders/{reminder}/done'
*/
const CompleteReminderControllerForm = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: CompleteReminderController.url(args, options),
    method: 'post',
})

/**
* @see \App\Modules\Recordatorios\Http\Controllers\Api\CompleteReminderController::__invoke
* @see app/Modules/Recordatorios/Http/Controllers/Api/CompleteReminderController.php:18
* @route '/api/v1/reminders/{reminder}/done'
*/
CompleteReminderControllerForm.post = (args: { reminder: string | number } | [reminder: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: CompleteReminderController.url(args, options),
    method: 'post',
})

CompleteReminderController.form = CompleteReminderControllerForm

export default CompleteReminderController