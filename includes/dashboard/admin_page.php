<?php

include_once __DIR__ . "/ice_auth_rebuild.php";
include_once __DIR__ . "/../utils/global.php";

function rmeSetupMenu()
{
    $user = wp_get_current_user();
    
    // We want to show this menu only to administrators
    if (!empty($user->roles))
    {
        if (in_array('administrator', (array) $user->roles)) {
            add_menu_page('RME ADMIN', 'RME (NIE DOTYKAĆ)', 'manage_options', 'test-plugin', 'showPage');
        }
    }
}
add_action('admin_menu', 'rmeSetupMenu');

//---------------------------------------------------------------------------------------------------
function showPage()
{
    // Requests handle
    if (isset($_REQUEST['rebuild_pyr_db'])) {        
        rebuildPyrDbRequested();
    }

    if (isset($_REQUEST['show_db'])) {
        showSubsDataOnPage();
    }

    if (isset($_REQUEST['register_selected'])) {
        $ids = array();
        foreach ($_POST as $key => $value) {
            if (startsWith($key, CHBOX_PREFIX)) {
                array_push($ids, (int)$value);
            }
        }
        registerListeners($ids);
    }

    if (isset($_REQUEST['unregister_selected'])) {
        $ids = array();
        foreach ($_POST as $key => $value) {
            if (startsWith($key, CHBOX_PREFIX)) {
                array_push($ids, (int)$value);
            }
        }
        unregisterListeners($ids);
    }

    // Page show
    ?>
        <form method="post">
            <h2>Show Subs Db Data: </h2>
            <input type="submit" name="show_db" value="Show DB">
            <br><br><br><br><br><br><br><br><br><br><br><br>
            <h2 style="color: red;">Rebuild pyramid db: </h2>
            <input type="submit" name="rebuild_pyr_db" value="Rebuild DB" style="background-color: red; color: white;">
        </form>
    <?php

    die;
}