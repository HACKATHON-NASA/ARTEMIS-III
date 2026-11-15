import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
const Controllera8f31746d05cf7ee770b3bc9c8c9dce9 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllera8f31746d05cf7ee770b3bc9c8c9dce9.url(options),
    method: 'get',
})

Controllera8f31746d05cf7ee770b3bc9c8c9dce9.definition = {
    methods: ["get","head"],
    url: '/acceso',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
Controllera8f31746d05cf7ee770b3bc9c8c9dce9.url = (options?: RouteQueryOptions) => {
    return Controllera8f31746d05cf7ee770b3bc9c8c9dce9.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
Controllera8f31746d05cf7ee770b3bc9c8c9dce9.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllera8f31746d05cf7ee770b3bc9c8c9dce9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
Controllera8f31746d05cf7ee770b3bc9c8c9dce9.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllera8f31746d05cf7ee770b3bc9c8c9dce9.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
const Controllera8f31746d05cf7ee770b3bc9c8c9dce9Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera8f31746d05cf7ee770b3bc9c8c9dce9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
Controllera8f31746d05cf7ee770b3bc9c8c9dce9Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera8f31746d05cf7ee770b3bc9c8c9dce9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/acceso'
*/
Controllera8f31746d05cf7ee770b3bc9c8c9dce9Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera8f31746d05cf7ee770b3bc9c8c9dce9.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllera8f31746d05cf7ee770b3bc9c8c9dce9.form = Controllera8f31746d05cf7ee770b3bc9c8c9dce9Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
const Controller54336251d65a1835cc801220072543e1 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller54336251d65a1835cc801220072543e1.url(options),
    method: 'get',
})

Controller54336251d65a1835cc801220072543e1.definition = {
    methods: ["get","head"],
    url: '/admin/usuarios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
Controller54336251d65a1835cc801220072543e1.url = (options?: RouteQueryOptions) => {
    return Controller54336251d65a1835cc801220072543e1.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
Controller54336251d65a1835cc801220072543e1.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller54336251d65a1835cc801220072543e1.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
Controller54336251d65a1835cc801220072543e1.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller54336251d65a1835cc801220072543e1.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
const Controller54336251d65a1835cc801220072543e1Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller54336251d65a1835cc801220072543e1.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
Controller54336251d65a1835cc801220072543e1Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller54336251d65a1835cc801220072543e1.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/usuarios'
*/
Controller54336251d65a1835cc801220072543e1Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller54336251d65a1835cc801220072543e1.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller54336251d65a1835cc801220072543e1.form = Controller54336251d65a1835cc801220072543e1Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
const Controller42a740574ecbfbac32f8cc353fc32db9 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

Controller42a740574ecbfbac32f8cc353fc32db9.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
Controller42a740574ecbfbac32f8cc353fc32db9.url = (options?: RouteQueryOptions) => {
    return Controller42a740574ecbfbac32f8cc353fc32db9.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
Controller42a740574ecbfbac32f8cc353fc32db9.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
Controller42a740574ecbfbac32f8cc353fc32db9.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
const Controller42a740574ecbfbac32f8cc353fc32db9Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
Controller42a740574ecbfbac32f8cc353fc32db9Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/dashboard'
*/
Controller42a740574ecbfbac32f8cc353fc32db9Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller42a740574ecbfbac32f8cc353fc32db9.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller42a740574ecbfbac32f8cc353fc32db9.form = Controller42a740574ecbfbac32f8cc353fc32db9Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
const Controllerb5f643ebf80df5cd7fad77562c7d770b = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb5f643ebf80df5cd7fad77562c7d770b.url(options),
    method: 'get',
})

Controllerb5f643ebf80df5cd7fad77562c7d770b.definition = {
    methods: ["get","head"],
    url: '/inicio',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
Controllerb5f643ebf80df5cd7fad77562c7d770b.url = (options?: RouteQueryOptions) => {
    return Controllerb5f643ebf80df5cd7fad77562c7d770b.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
Controllerb5f643ebf80df5cd7fad77562c7d770b.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb5f643ebf80df5cd7fad77562c7d770b.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
Controllerb5f643ebf80df5cd7fad77562c7d770b.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerb5f643ebf80df5cd7fad77562c7d770b.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
const Controllerb5f643ebf80df5cd7fad77562c7d770bForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb5f643ebf80df5cd7fad77562c7d770b.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
Controllerb5f643ebf80df5cd7fad77562c7d770bForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb5f643ebf80df5cd7fad77562c7d770b.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/inicio'
*/
Controllerb5f643ebf80df5cd7fad77562c7d770bForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb5f643ebf80df5cd7fad77562c7d770b.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerb5f643ebf80df5cd7fad77562c7d770b.form = Controllerb5f643ebf80df5cd7fad77562c7d770bForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
const Controllerba44bdf21e17eb8c3507a3a2631a0549 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerba44bdf21e17eb8c3507a3a2631a0549.url(options),
    method: 'get',
})

Controllerba44bdf21e17eb8c3507a3a2631a0549.definition = {
    methods: ["get","head"],
    url: '/salud',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
Controllerba44bdf21e17eb8c3507a3a2631a0549.url = (options?: RouteQueryOptions) => {
    return Controllerba44bdf21e17eb8c3507a3a2631a0549.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
Controllerba44bdf21e17eb8c3507a3a2631a0549.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerba44bdf21e17eb8c3507a3a2631a0549.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
Controllerba44bdf21e17eb8c3507a3a2631a0549.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerba44bdf21e17eb8c3507a3a2631a0549.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
const Controllerba44bdf21e17eb8c3507a3a2631a0549Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerba44bdf21e17eb8c3507a3a2631a0549.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
Controllerba44bdf21e17eb8c3507a3a2631a0549Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerba44bdf21e17eb8c3507a3a2631a0549.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/salud'
*/
Controllerba44bdf21e17eb8c3507a3a2631a0549Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerba44bdf21e17eb8c3507a3a2631a0549.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerba44bdf21e17eb8c3507a3a2631a0549.form = Controllerba44bdf21e17eb8c3507a3a2631a0549Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
const Controller5b17b125dcd5264bf51c586bd858ca4d = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller5b17b125dcd5264bf51c586bd858ca4d.url(options),
    method: 'get',
})

Controller5b17b125dcd5264bf51c586bd858ca4d.definition = {
    methods: ["get","head"],
    url: '/tripulacion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
Controller5b17b125dcd5264bf51c586bd858ca4d.url = (options?: RouteQueryOptions) => {
    return Controller5b17b125dcd5264bf51c586bd858ca4d.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
Controller5b17b125dcd5264bf51c586bd858ca4d.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller5b17b125dcd5264bf51c586bd858ca4d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
Controller5b17b125dcd5264bf51c586bd858ca4d.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller5b17b125dcd5264bf51c586bd858ca4d.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
const Controller5b17b125dcd5264bf51c586bd858ca4dForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller5b17b125dcd5264bf51c586bd858ca4d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
Controller5b17b125dcd5264bf51c586bd858ca4dForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller5b17b125dcd5264bf51c586bd858ca4d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/tripulacion'
*/
Controller5b17b125dcd5264bf51c586bd858ca4dForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller5b17b125dcd5264bf51c586bd858ca4d.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller5b17b125dcd5264bf51c586bd858ca4d.form = Controller5b17b125dcd5264bf51c586bd858ca4dForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
const Controller08fbefe50c126e971bb77009b7ecca58 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller08fbefe50c126e971bb77009b7ecca58.url(options),
    method: 'get',
})

Controller08fbefe50c126e971bb77009b7ecca58.definition = {
    methods: ["get","head"],
    url: '/admin/misiones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
Controller08fbefe50c126e971bb77009b7ecca58.url = (options?: RouteQueryOptions) => {
    return Controller08fbefe50c126e971bb77009b7ecca58.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
Controller08fbefe50c126e971bb77009b7ecca58.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller08fbefe50c126e971bb77009b7ecca58.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
Controller08fbefe50c126e971bb77009b7ecca58.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller08fbefe50c126e971bb77009b7ecca58.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
const Controller08fbefe50c126e971bb77009b7ecca58Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller08fbefe50c126e971bb77009b7ecca58.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
Controller08fbefe50c126e971bb77009b7ecca58Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller08fbefe50c126e971bb77009b7ecca58.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/admin/misiones'
*/
Controller08fbefe50c126e971bb77009b7ecca58Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller08fbefe50c126e971bb77009b7ecca58.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller08fbefe50c126e971bb77009b7ecca58.form = Controller08fbefe50c126e971bb77009b7ecca58Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
const Controllere0bc9f9d0b03c8128e8fcd7201f88536 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllere0bc9f9d0b03c8128e8fcd7201f88536.url(options),
    method: 'get',
})

Controllere0bc9f9d0b03c8128e8fcd7201f88536.definition = {
    methods: ["get","head"],
    url: '/registrar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
Controllere0bc9f9d0b03c8128e8fcd7201f88536.url = (options?: RouteQueryOptions) => {
    return Controllere0bc9f9d0b03c8128e8fcd7201f88536.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
Controllere0bc9f9d0b03c8128e8fcd7201f88536.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllere0bc9f9d0b03c8128e8fcd7201f88536.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
Controllere0bc9f9d0b03c8128e8fcd7201f88536.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllere0bc9f9d0b03c8128e8fcd7201f88536.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
const Controllere0bc9f9d0b03c8128e8fcd7201f88536Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere0bc9f9d0b03c8128e8fcd7201f88536.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
Controllere0bc9f9d0b03c8128e8fcd7201f88536Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere0bc9f9d0b03c8128e8fcd7201f88536.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/registrar'
*/
Controllere0bc9f9d0b03c8128e8fcd7201f88536Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere0bc9f9d0b03c8128e8fcd7201f88536.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllere0bc9f9d0b03c8128e8fcd7201f88536.form = Controllere0bc9f9d0b03c8128e8fcd7201f88536Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
const Controller856d6ad912c01076b19ffd7374b33517 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller856d6ad912c01076b19ffd7374b33517.url(options),
    method: 'get',
})

Controller856d6ad912c01076b19ffd7374b33517.definition = {
    methods: ["get","head"],
    url: '/guia',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
Controller856d6ad912c01076b19ffd7374b33517.url = (options?: RouteQueryOptions) => {
    return Controller856d6ad912c01076b19ffd7374b33517.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
Controller856d6ad912c01076b19ffd7374b33517.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller856d6ad912c01076b19ffd7374b33517.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
Controller856d6ad912c01076b19ffd7374b33517.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller856d6ad912c01076b19ffd7374b33517.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
const Controller856d6ad912c01076b19ffd7374b33517Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller856d6ad912c01076b19ffd7374b33517.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
Controller856d6ad912c01076b19ffd7374b33517Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller856d6ad912c01076b19ffd7374b33517.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia'
*/
Controller856d6ad912c01076b19ffd7374b33517Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller856d6ad912c01076b19ffd7374b33517.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller856d6ad912c01076b19ffd7374b33517.form = Controller856d6ad912c01076b19ffd7374b33517Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
const Controller9581ca4baf00741c2faf54bb460e4eac = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller9581ca4baf00741c2faf54bb460e4eac.url(options),
    method: 'get',
})

Controller9581ca4baf00741c2faf54bb460e4eac.definition = {
    methods: ["get","head"],
    url: '/guia/psicologica',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
Controller9581ca4baf00741c2faf54bb460e4eac.url = (options?: RouteQueryOptions) => {
    return Controller9581ca4baf00741c2faf54bb460e4eac.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
Controller9581ca4baf00741c2faf54bb460e4eac.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller9581ca4baf00741c2faf54bb460e4eac.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
Controller9581ca4baf00741c2faf54bb460e4eac.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller9581ca4baf00741c2faf54bb460e4eac.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
const Controller9581ca4baf00741c2faf54bb460e4eacForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller9581ca4baf00741c2faf54bb460e4eac.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
Controller9581ca4baf00741c2faf54bb460e4eacForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller9581ca4baf00741c2faf54bb460e4eac.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/psicologica'
*/
Controller9581ca4baf00741c2faf54bb460e4eacForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller9581ca4baf00741c2faf54bb460e4eac.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller9581ca4baf00741c2faf54bb460e4eac.form = Controller9581ca4baf00741c2faf54bb460e4eacForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
const Controller3578f1cb5db723d411f528ce3f5450d9 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller3578f1cb5db723d411f528ce3f5450d9.url(options),
    method: 'get',
})

Controller3578f1cb5db723d411f528ce3f5450d9.definition = {
    methods: ["get","head"],
    url: '/guia/resultados',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
Controller3578f1cb5db723d411f528ce3f5450d9.url = (options?: RouteQueryOptions) => {
    return Controller3578f1cb5db723d411f528ce3f5450d9.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
Controller3578f1cb5db723d411f528ce3f5450d9.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller3578f1cb5db723d411f528ce3f5450d9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
Controller3578f1cb5db723d411f528ce3f5450d9.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller3578f1cb5db723d411f528ce3f5450d9.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
const Controller3578f1cb5db723d411f528ce3f5450d9Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller3578f1cb5db723d411f528ce3f5450d9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
Controller3578f1cb5db723d411f528ce3f5450d9Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller3578f1cb5db723d411f528ce3f5450d9.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/guia/resultados'
*/
Controller3578f1cb5db723d411f528ce3f5450d9Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller3578f1cb5db723d411f528ce3f5450d9.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller3578f1cb5db723d411f528ce3f5450d9.form = Controller3578f1cb5db723d411f528ce3f5450d9Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
const Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.url(options),
    method: 'get',
})

Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.definition = {
    methods: ["get","head"],
    url: '/evolucion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.url = (options?: RouteQueryOptions) => {
    return Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
const Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/evolucion'
*/
Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4.form = Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
const Controller620f9c0526688354fc6e34fd956e5060 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller620f9c0526688354fc6e34fd956e5060.url(options),
    method: 'get',
})

Controller620f9c0526688354fc6e34fd956e5060.definition = {
    methods: ["get","head"],
    url: '/logros',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
Controller620f9c0526688354fc6e34fd956e5060.url = (options?: RouteQueryOptions) => {
    return Controller620f9c0526688354fc6e34fd956e5060.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
Controller620f9c0526688354fc6e34fd956e5060.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller620f9c0526688354fc6e34fd956e5060.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
Controller620f9c0526688354fc6e34fd956e5060.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller620f9c0526688354fc6e34fd956e5060.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
const Controller620f9c0526688354fc6e34fd956e5060Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller620f9c0526688354fc6e34fd956e5060.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
Controller620f9c0526688354fc6e34fd956e5060Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller620f9c0526688354fc6e34fd956e5060.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/logros'
*/
Controller620f9c0526688354fc6e34fd956e5060Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller620f9c0526688354fc6e34fd956e5060.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller620f9c0526688354fc6e34fd956e5060.form = Controller620f9c0526688354fc6e34fd956e5060Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
const Controller2ed6f5d6b8555cf7fc29890dc1751560 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2ed6f5d6b8555cf7fc29890dc1751560.url(options),
    method: 'get',
})

Controller2ed6f5d6b8555cf7fc29890dc1751560.definition = {
    methods: ["get","head"],
    url: '/mision',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
Controller2ed6f5d6b8555cf7fc29890dc1751560.url = (options?: RouteQueryOptions) => {
    return Controller2ed6f5d6b8555cf7fc29890dc1751560.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
Controller2ed6f5d6b8555cf7fc29890dc1751560.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller2ed6f5d6b8555cf7fc29890dc1751560.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
Controller2ed6f5d6b8555cf7fc29890dc1751560.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller2ed6f5d6b8555cf7fc29890dc1751560.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
const Controller2ed6f5d6b8555cf7fc29890dc1751560Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2ed6f5d6b8555cf7fc29890dc1751560.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
Controller2ed6f5d6b8555cf7fc29890dc1751560Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2ed6f5d6b8555cf7fc29890dc1751560.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/mision'
*/
Controller2ed6f5d6b8555cf7fc29890dc1751560Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller2ed6f5d6b8555cf7fc29890dc1751560.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller2ed6f5d6b8555cf7fc29890dc1751560.form = Controller2ed6f5d6b8555cf7fc29890dc1751560Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
const Controllerce228f7683992a2676f3fa55e33eca9d = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerce228f7683992a2676f3fa55e33eca9d.url(options),
    method: 'get',
})

Controllerce228f7683992a2676f3fa55e33eca9d.definition = {
    methods: ["get","head"],
    url: '/indicaciones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
Controllerce228f7683992a2676f3fa55e33eca9d.url = (options?: RouteQueryOptions) => {
    return Controllerce228f7683992a2676f3fa55e33eca9d.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
Controllerce228f7683992a2676f3fa55e33eca9d.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerce228f7683992a2676f3fa55e33eca9d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
Controllerce228f7683992a2676f3fa55e33eca9d.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerce228f7683992a2676f3fa55e33eca9d.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
const Controllerce228f7683992a2676f3fa55e33eca9dForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerce228f7683992a2676f3fa55e33eca9d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
Controllerce228f7683992a2676f3fa55e33eca9dForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerce228f7683992a2676f3fa55e33eca9d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/indicaciones'
*/
Controllerce228f7683992a2676f3fa55e33eca9dForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerce228f7683992a2676f3fa55e33eca9d.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerce228f7683992a2676f3fa55e33eca9d.form = Controllerce228f7683992a2676f3fa55e33eca9dForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
const Controller1daf09739a392428785c3a8bd21de39d = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller1daf09739a392428785c3a8bd21de39d.url(options),
    method: 'get',
})

Controller1daf09739a392428785c3a8bd21de39d.definition = {
    methods: ["get","head"],
    url: '/alertas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
Controller1daf09739a392428785c3a8bd21de39d.url = (options?: RouteQueryOptions) => {
    return Controller1daf09739a392428785c3a8bd21de39d.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
Controller1daf09739a392428785c3a8bd21de39d.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller1daf09739a392428785c3a8bd21de39d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
Controller1daf09739a392428785c3a8bd21de39d.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller1daf09739a392428785c3a8bd21de39d.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
const Controller1daf09739a392428785c3a8bd21de39dForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1daf09739a392428785c3a8bd21de39d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
Controller1daf09739a392428785c3a8bd21de39dForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1daf09739a392428785c3a8bd21de39d.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/alertas'
*/
Controller1daf09739a392428785c3a8bd21de39dForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1daf09739a392428785c3a8bd21de39d.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller1daf09739a392428785c3a8bd21de39d.form = Controller1daf09739a392428785c3a8bd21de39dForm

/**
* Multiple routes resolve to \Inertia\Controller::Controller, so this export is a
* dictionary keyed by URI rather than a callable, with the verbs prefixed where two routes share a URI.
* Call a specific route with `Controller['<key>'](...)`, or import the route by name from your
* generated `routes/` directory.
*/
const Controller = {
    '/acceso': Controllera8f31746d05cf7ee770b3bc9c8c9dce9,
    '/admin/usuarios': Controller54336251d65a1835cc801220072543e1,
    '/dashboard': Controller42a740574ecbfbac32f8cc353fc32db9,
    '/inicio': Controllerb5f643ebf80df5cd7fad77562c7d770b,
    '/salud': Controllerba44bdf21e17eb8c3507a3a2631a0549,
    '/tripulacion': Controller5b17b125dcd5264bf51c586bd858ca4d,
    '/admin/misiones': Controller08fbefe50c126e971bb77009b7ecca58,
    '/registrar': Controllere0bc9f9d0b03c8128e8fcd7201f88536,
    '/guia': Controller856d6ad912c01076b19ffd7374b33517,
    '/guia/psicologica': Controller9581ca4baf00741c2faf54bb460e4eac,
    '/guia/resultados': Controller3578f1cb5db723d411f528ce3f5450d9,
    '/evolucion': Controllerfaa1cc5a11f0e6ef3c7e8a43211904c4,
    '/logros': Controller620f9c0526688354fc6e34fd956e5060,
    '/mision': Controller2ed6f5d6b8555cf7fc29890dc1751560,
    '/indicaciones': Controllerce228f7683992a2676f3fa55e33eca9d,
    '/alertas': Controller1daf09739a392428785c3a8bd21de39d,
}

export default Controller