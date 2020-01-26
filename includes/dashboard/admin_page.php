<?php

include_once __DIR__ . "/ice_auth_rebuild.php";

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
        // TODO finish this
        showSubsDataOnPage();
    }

    if (isset($_REQUEST['unregister_selected'])) {
        // TODO finish this
        showSubsDataOnPage();
    }

    // Page show
    ?>
        <form method="post">
            <h2>Rebuild pyramid db: </h2>
            <input type="submit" name="rebuild_pyr_db" value="Rebuild DB">
            <h2>Show Subs Db Data: </h2>
            <input type="submit" name="show_db" value="Show DB">
        </form>
    <?php

    die;
}