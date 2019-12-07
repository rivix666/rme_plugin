<?php

// Include this file only if we are on SUBS_ENDPOINT
if (basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) == SUBS_ENDPOINT)
{
    include_once "global.php";

    // TODO find a solution so we can run this all in 'm3u' endpoints
    // The problem wast th error taht says 'Headers already sent'
    if (array_key_exists('m3u', $_GET)) {
        if ($_GET['m3u']) {
            downloadM3u();
        }
    }
    
    function downloadM3u()
    {
        try
        {
            $uuid = $_GET['uuid'];
            $sub_id = $_GET['id'];
            $content = ICECAST_URL.$uuid;
    
            header("Content-type: audio/mpegurl");
            header("Cache-Control: no-store, no-cache");
            header("Content-Disposition: attachment; filename=rme_$sub_id.m3u");
            header("Content-Length: " . strlen($content));
    
            echo $content;
    
        } catch (Exception $e) {
            if ($sentry = sentryClient()) 
                $sentry->captureException($e);
            echo log_write("Wrong link"); // TODO show any message to user that his link is wrong
        }
    }
}