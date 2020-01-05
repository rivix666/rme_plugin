<?php

function nsGoogleAnalytics()
{
    $user = wp_get_current_user();
    
    // We don't want to track activity of admin and shop manager
    if (!empty($user->roles))
    {
        if (in_array('administrator', (array) $user->roles)) {
            return;
        }
    
        if (in_array('shop_manager', (array) $user->roles)) {
            return;
        }
    }

    ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-154625166-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-154625166-1');
    </script>
    <?php
}

add_action('wp_head', 'nsGoogleAnalytics', 10);
