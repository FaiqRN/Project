<?php

if (!function_exists('isMenuActive')) {
    function isMenuActive($menu, $activemenu) {
        return $activemenu === $menu ? 'active' : '';
    }
}