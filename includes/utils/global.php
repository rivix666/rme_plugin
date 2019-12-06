<?php

define("ICECAST_URL", "https://jmpiano.pl:8080/rme_test?uuid=");

date_default_timezone_set('Europe/Warsaw');

if (!function_exists('write_log')) {
    function write_log($log)
    {
        if (is_array($log) || is_object($log)) {
            error_log(print_r($log, true));
        } else {
            error_log($log);
        }
    }
}
