<?php

class RmeException extends ErrorException 
{
    private $error_code = -1;

    public function __construct($message) 
    {
        $code = uniqid("", false);
        $this->error_code = $code;       
        parent::__construct("[CODE $code] $message");
    }

    public function getErrorCode()
    {
        return $this->error_code;
    }
}

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
    if (is_a($exception, "RmeException"))
    {
        $sentry = sentryClient();
        $sentry->captureException($exception);
    
        $code = $exception->getErrorCode();
        $message_to_user = "Wystąpił błąd! Skontaktuj się proszę z naszym działem obsługi 'kontakt@radiomaxelektro.pl'. W wiadomości załącz niniejszy numer błędu: $code";  
        echo "<script>alert(\"$message_to_user\")</script>";
    }
}

set_exception_handler('exceptionHandler');

//---------------------------------------------------------------------------------------------------
function errorHandler($errno, $errstr, $errfile, $errline)
{
    if (!empty($errfile) && strpos($errfile, "wp-content/plugins/rme_plugin/") == false)
        return false;

    switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:   
            $error_code = intl_error_name($errno);
            $e = new RmeException("[ERROR][$error_code] $errstr - $errfile:$errline");
            throw $e;

        case E_WARNING:
        case E_USER_WARNING:
            $error_code = intl_error_name($errno);
            sentryClient()->captureMessage("[WARNING][$error_code] $errst - $errfile:$errline");
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            $error_code = intl_error_name($errno);
            sentryClient()->captureMessage("[NOTICE][$error_code] $errst - $errfile:$errline");
            break;

        default:
            $error_code = intl_error_name($errno);
            sentryClient()->captureMessage("[UNKNOWN][$error_code] $errst - $errfile:$errline");
            break;
    }

    // Don't execute PHP internal error handler
    return true;
}

set_error_handler("errorHandler");