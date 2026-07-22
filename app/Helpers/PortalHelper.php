<?php

if (!function_exists('portal_route')) {
    function portal_route($name, $parameters = [], $absolute = true)
    {
        $role = auth()->check() ? auth()->user()->role : 'admin';
        
        // If the role is one of the portal roles, prepend it. Otherwise default to admin.
        if (in_array($role, ['admin', 'manager', 'staff', 'technician'])) {
            return route($role . '.' . $name, $parameters, $absolute);
        }

        return route('admin.' . $name, $parameters, $absolute);
    }
}

if (!function_exists('is_portal_route')) {
    function is_portal_route($name)
    {
        $role = auth()->check() ? auth()->user()->role : 'admin';
        
        if (in_array($role, ['admin', 'manager', 'staff', 'technician'])) {
            return request()->routeIs($role . '.' . $name);
        }

        return request()->routeIs('admin.' . $name);
    }
}
