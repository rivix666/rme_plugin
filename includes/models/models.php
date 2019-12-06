<?php

namespace models;

use WordPress\ORM\BaseModel;

class Subs extends BaseModel
{
	public $id;
	public $user_id; // zamienić potem na protected i dorobic gettery i settery
	public $url;
	public $exp_date;
	public $licenses_num;
	// public licence id dopisac

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
	public $id; // zamienić potem na protected i dorobic gettery i settery
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