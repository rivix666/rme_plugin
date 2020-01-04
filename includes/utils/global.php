<?php

include_once "error_handler.php";

// Defines
define("ICECAST_URL", "https://stream.radiomaxelektro.pl:8080/rme_stream?uuid=");
define("SUBS_ENDPOINT", "subscriptions"); // After change remember to update account.css and hooks names

// Date timezone
// TODO check if we really don;t need to set this
//date_default_timezone_set('Europe/Warsaw');

// Debug logs
//---------------------------------------------------------------------------------------------------
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

//---------------------------------------------------------------------------------------------------
function isDateExpired($date)
{
    $today = date("Y-m-d");
    return $today > $date;
}

// It's awesome but some pages may block it...
// $data - data that should be in downloaded file
// $data_type - eg. 'audio/mpegurl' or 'text/plain', 'text/html'
// More info - https://en.wikipedia.org/wiki/Data_URI_scheme#Web_browser_support
//---------------------------------------------------------------------------------------------------
if (!function_exists('createDownloadLink')) {
    function createDownloadLink($data, $data_type, $link_text)
    {
        return "<a href='data:$data_type;charset=UTF-8,$data'>$link_text</a>";
    }
}