<?php

include_once __DIR__."/../utils/global.php";
include_once __DIR__."/../models/models.php";

use models\Subs as ac_S;
use models\SubsOrderData as ac_SOD;




// Edit my account menu order
//---------------------------------------------------------------------------------------------------
function myAccountMenuOrder()
{
    
    $menuOrder = array(
        'subscriptions' => __('Subskrypcje', 'woocommerce'),
        'orders' => __('Orders', 'woocommerce'),
        'edit-address' => __('Addresses', 'woocommerce'),
        'edit-account' => __('Szczegóły Konta', 'woocommerce'),
        'customer-logout' => __('Logout', 'woocommerce'),
        // 'downloads' => __('Download', 'woocommerce'),
        // 'dashboard' => __('Dashboard', 'woocommerce'),
    );

    return $menuOrder;
}

add_filter('woocommerce_account_menu_items', 'myAccountMenuOrder');

// Register new endpoints to use inside My Account page.
//---------------------------------------------------------------------------------------------------
function myAccountNewEndpoints()
{
    add_rewrite_endpoint('subscriptions', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('m3u', EP_ROOT | EP_PAGES);

    wp_enqueue_style('rmeMyAccount', plugin_dir_url(__FILE__).'../../css/account.css');
}

add_action('init', 'myAccountNewEndpoints');

// Get new endpoint content
//---------------------------------------------------------------------------------------------------
function subscriptionsEndpointContent()
{
    $user = wp_get_current_user();
    if (!$user)
    {
        throw new ErrorException(sprintf("[%s::%s] User is null", __CLASS__, __FUNCTION__));
    }

    $factory = new SubsPageFactory();
    $factory->showSubscriptionsTable($user);
}

add_action('woocommerce_account_subscriptions_endpoint', 'subscriptionsEndpointContent');

// Get new endpoint content
//---------------------------------------------------------------------------------------------------
function m3uEndpointContent()
{
    try
    {
        if ($_GET['m3u_download'])
        {
            $uuid = $_GET['uuid'];
            $content = "https://jmpiano.pl:8080/rme_test?uuid=$uuid";
    
            header("Content-type: audio/mpegurl");
            header("Cache-Control: no-store, no-cache");
            header('Content-Disposition: attachment; filename="content.m3u"');
            header('Content-Length: '.strlen($content));
            
            echo $content;
        }
    }
    catch (Exception $e)
    {
        echo "Niepoprawny link";
    }
}

add_action('woocommerce_account_m3u_endpoint', 'm3uEndpointContent');

//---------------------------------------------------------------------------------------------------
class SubsPageFactory
{
    public function showSubscriptionsTable($user)
    {
        // Get $user subscriptions
        $subs = $this->populateSubsArray($user);

        // If user doesn't have any, show him message
        if (!$subs || sizeof($subs) == 0)
        {
            // TODO use the same font that is used in myAccount
            echo "Niestety nie masz jeszcze wykupionej żadnej licencji :(.</br>Zapoznaj sie z naszą ofertą w celu dokonania zakupu.";
            return;
        }


        $headers = array("Id", "Zamówienie", "Data wygaśnięcia", "Linki", "Przedłuż");
        $this->showTable($this->createTableHeader($headers), $this->createTableBody($subs));
    }

    //---------------------------------------------------------------------------------------------------
    private function showTable($head, $body)
    {
        echo 
            "<table>
                $head 
                $body
            </table>";
    }

    //---------------------------------------------------------------------------------------------------
    private function createTableHeader(array $headers)
    {
        $html = "<thead><tr>";
        foreach ($headers as $it)
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
            foreach($row as $cell)
            {
                $html .= "<td>$cell</td>";
            }
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        return $html;
    }

    //---------------------------------------------------------------------------------------------------
    private function populateSubsArray($user)
    {
    //Id", "Zamówienie", "Data wygaśnięcia", "Linki", "Odnów");

     
        $subs = ac_S::query()
            ->where('user_id', $user->ID)
            ->sort_by('exp_date')
            ->order('ASC')
            ->find();

        $result = array();
        foreach ($subs as $it)
        {
            $orders_links = $this->prepareOrderLinks($it);
            $exp_date_info = $this->prepareExpDateInfo($it);
            $links = $this->prepareLinkButtons($it);

            array_push($result, array($it->id, $orders_links, $exp_date_info, $links, "btn"));
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
        $today = date("Y-m-d");
        $date = date("d-m-Y", strtotime($sub->exp_date));
        if ($today > $sub->exp_date)
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
        return $this->createButtonWithLink("Radio", $radio_url).$this->createDownloadM3uButton($sub);       
    }

    //---------------------------------------------------------------------------------------------------
    private function createButtonWithLink($button_name, $link)
    {
        return "<button onclick=\"window.location.href = '$link';\">$button_name</button>";
    }

    private function createDownloadM3uButton($sub)
    {
        return "<button id='btnfun1' name='btnfun1' onClick='location.href=\"m3u?m3u_download=1&uuid=$sub->url\"'>M3U</button>";

      //  disabled
    }
   // ICECAST_URL
}