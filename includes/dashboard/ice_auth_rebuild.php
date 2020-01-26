<?php

include_once __DIR__ . "/../ice_auth/ice_auth_mgr.php";
include_once __DIR__ . "/../models/models.php";

use models\Subs as ap_S;
use models\SubsOrderData as ap_SOD;

//---------------------------------------------------------------------------------------------------
function rebuildPyrDbRequested()
{
    // Maybe we will return to this in the future
    // define('BEGIN_REBUILD_URL', 'https://stream.radiomaxelektro.pl:7080/icecast/listener/manage/begin_db_rebuild');
    // define('END_REBUILD_URL', 'https://stream.radiomaxelektro.pl:7080/icecast/listener/manage/end_db_rebuild');

    $errors = "";
    $skipped = "";
    $registered = "";
    $errors_count = 0;
    $skipped_count = 0;
    $registered_count = 0;

    $sub_data = ap_SOD::query()->find();
    foreach ($sub_data as $data)
    {        
        $subs = ap_S::query()
        ->where('id', $data->sub_id)
        ->find();

        $ice_mgr = new IceAuthOrderMgr($data->order_id);
        foreach ($subs as $s)
        {
            $http_code = $ice_mgr->registerSubscriptionInIceAuth($s);
            if ($http_code == 200)
            {
                $registered .= $s->id . ", ";
                $registered_count++;
            }
            else if ($http_code == 234)
            {
                $skipped .= $s->id . ", ";
                $skipped_count++;   
            }
            else
            {
                $errors .= $s->id . ", ";
                $errors_count++;
            }
        }
    }

    echo "<p><h2>Registered: " . $registered_count . "</h2><br>Sub Ids: " . $registered . "</p>";
    echo "<p><h2>Skipped: "    . $skipped_count    . "</h2><br>Sub Ids: " . $skipped    . "</p>";
    echo "<p><h2>Errors: "     . $errors_count     . "</h2><br>Sub Ids: " . $errors     . "</p><br>";
}