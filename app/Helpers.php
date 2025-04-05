<?php

use Illuminate\Support\Facades\Request;

if (!function_exists('activeSidebar')) {
    function activeSidebar($pattern)
    {
        return Request::routeIs($pattern)
            ? 'bg-orange-500 text-orange-foreground'
            : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground';
    }
}
