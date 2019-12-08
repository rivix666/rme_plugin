<?php

// Include this file only if we are on SUBS_ENDPOINT
if (basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) == SUBS_ENDPOINT)
{
    include_once "global.php";

    function downloadM3u()
    {
        try
        {
            $uuid = $_GET['uuid'];
            $sub_id = $_GET['id'];
            $content = ICECAST_URL.$uuid;
 
			// Clean whole output buffer, so in the file we will have only generated link and not whole page html
            if (ob_end_clean())
			{
				header("Content-Type: audio/mpegurl");
            	header("Content-Transfer-Encoding: none");
            	header("Cache-Control: no-store, no-cache");
            	header("Content-Disposition: attachment; filename=rme_$sub_id.m3u");
            	header("Content-Length: ".strlen($content));
				echo $content;
			}
			else
			{
				throw new Exception(sprintf("[%s::%s] Can not clear output buffer before file download", __CLASS__, __FUNCTION__));
				// TODO show any message to user that there is a problem with download
			}

        } catch (Exception $e) {
            if ($sentry = sentryClient()) 
                $sentry->captureException($e);
            write_log("Wrong link"); // TODO show any message to user that his link is wrong
        }
    }

    // TODO find a solution so we can run this all in 'm3u' endpoints
    // The problem wast th error taht says 'Headers already sent'
    if (array_key_exists('m3u', $_GET)) {
        if ($_GET['m3u']) {
            downloadM3u();
        }
    }
}