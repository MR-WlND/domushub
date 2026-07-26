<?php

if (!function_exists('portal_route')) {
    /**
     * Tạo route URL theo portal của từng vai trò.
     * 
     * Các vai trò nội bộ có portal riêng: admin, manager, staff, technician, security, cleaning.
     * Các vai trò khác (resident...) fallback về admin portal.
     */
    function portal_route($name, $parameters = [], $absolute = true)
    {
        $role = auth()->check() ? auth()->user()->role : 'admin';

        $portalRoles = ['admin', 'manager', 'staff', 'technician', 'security', 'cleaning'];

        if (in_array($role, $portalRoles)) {
            // Kiểm tra route tồn tại theo role prefix — nếu không có thì fallback admin
            $routeName = $role . '.' . $name;
            if (\Illuminate\Support\Facades\Route::has($routeName)) {
                return route($routeName, $parameters, $absolute);
            }
        }

        // Fallback về admin portal
        return route('admin.' . $name, $parameters, $absolute);
    }
}

if (!function_exists('is_portal_route')) {
    /**
     * Kiểm tra route hiện tại có khớp với tên route theo portal của user hay không.
     */
    function is_portal_route($name)
    {
        $role = auth()->check() ? auth()->user()->role : 'admin';

        $portalRoles = ['admin', 'manager', 'staff', 'technician', 'security', 'cleaning'];

        if (in_array($role, $portalRoles)) {
            $routeName = $role . '.' . $name;
            if (\Illuminate\Support\Facades\Route::has(rtrim($routeName, '*'))) {
                return request()->routeIs($routeName);
            }
        }

        return request()->routeIs('admin.' . $name);
    }
}
