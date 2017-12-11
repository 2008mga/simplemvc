<?php

if (!function_exists('app')) {

    function app() {
        return \Simple\Kernel\App::getInstance()->init();
    }

}

if (!function_exists('config')) {

    function _get($arr, $key)
    {
        return $arr[$key];
    }

    function config($to, $default = null) {
        $to = explode('!', $to);
        $repo = app()->make('config')->load($to[0]);

        $result = $repo->get($to[1], $default);

        if (is_array($result)) {
            for ($i = 0; $i < count($to); $i++) {
                if ($i <= 1) {
                    continue;
                }

                $result = _get($result, $to[$i]);
            }
        }


        return $result;
    }
}