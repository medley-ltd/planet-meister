<?php
/**
 * ショップマスターモデル
 *
 * @author hideki.takahashi
 */
use \Fuel\Core;

class Model_ShopList extends Model_Crud {
	protected static $_table_name = 'shop_list';
	protected static $_primary_key = 'id';

	/**
	 * ショップの一覧を取得する。
	 * @param int $limit  任意。
	 * @param int $offset 任意。指定時は $limitの倍数で。
	 */
	public function getShopList($limit = null, $offset = null) {
		$query = DB::select()->from(self::$_table_name);

		if (!is_null($limit)) {
			$query->limit($limit);
		}
		if (!is_null($offset)) {
			$query->offset($offset);
		}

		$query->order_by('id');

        return $query->execute()->as_array();
    }
}

