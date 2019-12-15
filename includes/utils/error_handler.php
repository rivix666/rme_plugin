<?php

//---------------------------------------------------------------------------------------------------
function sentryClient()
{
    if (class_exists('WP_Sentry_Php_Tracker')) {
        return WP_Sentry_Php_Tracker::get_instance()->get_client();
    }
    return null;
}

//---------------------------------------------------------------------------------------------------
function exceptionHandler($exception)
{
    $sentry = sentryClient();
    $sentry->captureException($exception);

    //wp_redirect(home_url()); # TODO redirect do strony z info że coś poszło nie tak i zeby się z kontaktować z obługą klienta
}

set_exception_handler('exceptionHandler');

//---------------------------------------------------------------------------------------------------
function errorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:   
            $error_code = intl_error_name($errno);
            $e = new Exception("[ERROR][$error_code] $errstr");
            throw $e;

        case E_WARNING:
        case E_USER_WARNING:
            $error_code = intl_error_name($errno);
            sentryClient()->captureMessage("[WARNING][$error_code] $errst");
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            $error_code = intl_error_name($errno);
            sentryClient()->captureMessage("[NOTICE][$error_code] $errst");
            break;

        default:
            $error_code = intl_error_name($errno);
            sentryClient()->captureMessage("[UNKNOWN][$error_code] $errst");
            break;
    }

    // Don't execute PHP internal error handler
    return true;
}

set_error_handler("errorHandler");