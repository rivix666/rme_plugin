<?php

namespace models;

use WordPress\ORM\BaseModel;

class Subs extends BaseModel
{
	public $id;
	public $user_id; // TODO change all to protected
	public $url;
	public $exp_date;
	public $licenses_num;

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		return 'rme_subs';
	}

	public static function get_searchable_fields()
	{
		return ['url', 'user_id', 'exp_date', 'licenses_num'];
	}
}

class SubsOrderData extends BaseModel
{
	public $id;
	public $sub_id; // Connected by foreign key with Subs->id
	public $order_id;
	public $product_id;

	public static function get_primary_key()
	{
		return 'id';
	}

	public static function get_table()
	{
		return 'rme_subs_products';
	}

	public static function get_searchable_fields()
	{
		return ['sub_id', 'order_id', 'product_id'];
	}
}

?>