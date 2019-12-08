<?php

include_once "global.php";
include_once __DIR__."/../models/models.php";

use models\Subs as ac_S;
use models\SubsOrderData as ac_SOD;

class SubsPageFactory
{
    const TABLE_HEADERS = array("Id", "Zamówienie", "Data wygaśnięcia", "Linki", "Działania");

    public function showSubscriptionsTable($user)
    {
        // Get $user subscriptions
        $subs = $this->populateSubsArray($user);

        // If user doesn't have any, show him message
        if (!$subs || sizeof($subs) == 0)
        {
            $home_link = "<a href='".home_url()."'>ofertą</a>";
            echo "<p>Niestety nie masz jeszcze wykupionej żadnej licencji :(</p><p>Zapoznaj sie z naszą $home_link w celu dokonania zakupu</p>";
            return;
        }
 
        $this->showTable($this->createTableHeader(), $this->createTableBody($subs));
    }

    //---------------------------------------------------------------------------------------------------
    private function showTable($head, $body)
    {
        // We use order table style so both tables can look the same
        echo 
            "<table class='shop_table shop_table_responsive my_account_orders'>
                $head 
                $body
            </table>";
    }

    //---------------------------------------------------------------------------------------------------
    private function createTableHeader()
    {
        $html = "<thead><tr>";
        foreach (self::TABLE_HEADERS as $it)
        {
            $html .= "<th><span class='nobr'>$it</span></th>";
        }
        $html .= "</tr></thead>";
        return $html;
    }

    //---------------------------------------------------------------------------------------------------
    private function createTableBody(array $rows)
    {
        $html = "<tbody>";
        foreach ($rows as $row)
        {
            $html .= "<tr>";
            foreach($row as $key => $cell)
            {
                $column_name = self::TABLE_HEADERS[$key];
                $html .= "<td data-title='$column_name'>$cell</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        return $html;
    }

    //---------------------------------------------------------------------------------------------------
    private function populateSubsArray($user)
    {
        $subs = ac_S::query()
            ->where('user_id', $user->ID)
            ->sort_by('exp_date')
            ->order('DESC')
            ->find();

        $result = array();
        foreach ($subs as $it)
        {
            $orders_links = $this->prepareOrderLinks($it);
            $exp_date_info = $this->prepareExpDateInfo($it);
            $links = $this->prepareLinkButtons($it);

            // TODO we will add this functionality at the end
            $renew = $this->createButtonWithLink("Przedłuż", "", false);

            array_push($result, array($it->id, $orders_links, $exp_date_info, $links, $renew));
        }

        return $result;
    }

    //---------------------------------------------------------------------------------------------------
    private function prepareOrderLinks($sub)
    {
        $sub_data = ac_SOD::query()
            ->where('sub_id', $sub->id)
            ->find();
        
        // TODO check what wil hapen if we have multiple orders per sub
        $orders_links = "";
        foreach ($sub_data as $data)
        {
            $orders_links .= "<a href='./view-order/$data->order_id'>#$data->order_id</a>";
        }

        return $orders_links;
    }

    //---------------------------------------------------------------------------------------------------
    private function prepareExpDateInfo($sub)
    { 
        $date = date("d-m-Y", strtotime($sub->exp_date));
        if (isDateExpired($sub->exp_date))
        {
            return "<div class='exp_date-expired'>$date</div>";
        }

        $month_from_today = date('Y-m-d', strtotime("+1 month"));
        if ($month_from_today > $sub->exp_date)
        {
            return "<div class='exp_date-warning'>$date</div>";
        }

        return $date;
    }

    //---------------------------------------------------------------------------------------------------
    private function prepareLinkButtons($sub)
    {
        $radio_url = ICECAST_URL.$sub->url;
        return $this->createButtonWithLink("Radio", $radio_url, !isDateExpired($sub->exp_date))
                    ."&nbsp&nbsp&nbsp".$this->createDownloadM3uButton($sub);       
    }

    //---------------------------------------------------------------------------------------------------
    private function createButtonWithLink($button_name, $link, $enabled = true)
    {
        $disabled = "";
        if (!$enabled)
            $disabled = "style='background: #bbb;' disabled='disabled'";;

        return "<button onclick=\"window.location.href = '$link';\" $disabled>$button_name</button>";
    }

    //---------------------------------------------------------------------------------------------------
    private function createDownloadM3uButton($sub)
    {
        $disabled = "";
        if (isDateExpired($sub->exp_date))
            $disabled = "style='background: #bbb;' disabled='disabled'";

      return "<button  onClick=\"location.href='?m3u=1&id=$sub->id&uuid=$sub->url'\" $disabled>M3U</button>";
    }
}

?>