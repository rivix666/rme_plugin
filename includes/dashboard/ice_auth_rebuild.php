<?php

// I know there is a lot of copied code but... I just don't care...

include_once __DIR__ . "/../ice_auth/ice_auth_mgr.php";
include_once __DIR__ . "/../models/models.php";

use models\Subs as ap_S;
use models\SubsOrderData as ap_SOD;

define("CHBOX_PREFIX", "chbox_");

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

        foreach ($subs as $s)
        {
            $http_code = IceAuthOrderMgr::registerSubscriptionInIceAuth($s);
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

//---------------------------------------------------------------------------------------------------
function registerListeners($ids)
{
    if (empty($ids))
        return;

    $errors = "";
    $skipped = "";
    $registered = "";
    $errors_count = 0;
    $skipped_count = 0;
    $registered_count = 0;

    foreach ($ids as $i)
    {        
        $subs = ap_S::query()
        ->where('id', $i)
        ->find();

        foreach ($subs as $s)
        {
            $http_code = IceAuthOrderMgr::registerSubscriptionInIceAuth($s);
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

//---------------------------------------------------------------------------------------------------
function unregisterListeners($ids)
{
    if (empty($ids))
        return;

    $errors = "";
    $skipped = "";
    $registered = "";
    $errors_count = 0;
    $skipped_count = 0;
    $registered_count = 0;

    foreach ($ids as $i)
    {        
        $subs = ap_S::query()
        ->where('id', $i)
        ->find();

        foreach ($subs as $s)
        {
            $http_code = IceAuthOrderMgr::unregisterSubscriptionFromIceAuth($s);
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

    echo "<p><h2>Unregistered: " . $registered_count . "</h2><br>Sub Ids: " . $registered . "</p>";
    echo "<p><h2>Skipped: "      . $skipped_count    . "</h2><br>Sub Ids: " . $skipped    . "</p>";
    echo "<p><h2>Errors: "       . $errors_count     . "</h2><br>Sub Ids: " . $errors     . "</p><br>";
}

//---------------------------------------------------------------------------------------------------
function showSubsDataOnPage()
{
    ?>
    <style>
        .subs_info table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .subs_info th {
            color: black;
            background-color: #808080;
        }
        .subs_info th, td {
            padding: 5px;
        }
    </style>
    <form method="post">
        <div class="subs_info">
            <p>
            <table>
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>User</th>
                    <th>Url</th>
                    <th>ExpDate</th>
                    <th>Licenses</th>
                    <th>SOD_Id</th>
                    <th>Order_Id</th>
                    <th>Product_Id</th>
                </tr>
    <?php

    $subs = ap_S::query()
        ->find();

    foreach ($subs as $s)
    {
        // Subs array
        echo "<tr>";
        echo "<td><input type='checkbox' name='". CHBOX_PREFIX ."$s->id' value='$s->id'></td>";
        echo "<td><b>".$s->id."</b></td>";
        echo "<td>".$s->user_id."</td>";
        echo "<td>".$s->url."</td>";
        echo "<td>".$s->exp_date."</td>";
        echo "<td>".$s->licenses_num."</td>";

        // Subs data array
        $sub_data = ap_SOD::query()
            ->where("sub_id", $s->id)
            ->find();

        $sd_size = sizeof($sub_data);
        if ($sd_size < 1) {
            echo "<td>THERE IS NO SUB DATA</td>";
        }
        else if ($sd_size > 1) {
            echo "<td>SUB DATA ARRAY TOO BIG: $sd_size</td>";
        }
        else {
            echo "<td><b>".$sub_data[0]->id."</b></td>";
            echo "<td>".$sub_data[0]->order_id."</td>";
            echo "<td>".$sub_data[0]->product_id."</td>";
        }

        // End of table
        echo "</tr>";
    }

    ?>
            </table>
            </p>
        </div>
        <input type="submit" name="register_selected" value="Register selected">
        <input type="submit" name="unregister_selected" value="Unregister selected">
    </form>
    <?php
}