<?php
/**
 * ガチャリストモデル
 *
 * @author hideki.takahashi
 */
use \Fuel\Core;

class Model_GachaList extends Model_Crud {
	protected static $_table_name = 'gacha_list';
	protected static $_primary_key = 'id';

	/**
	 * ガチャデータひとつを取得する。
	 * @param integer $gacha_id 取得したいガチャのID
	 */
	public function getGacha($gacha_id) {
		return DB::select()->from(self::$_table_name)
							->where('id', $gacha_id);
	}

	/**
	 * getGachaList
	 * ガチャの一覧を取得する。
	 * @param int $limit  任意。
	 * @param int $offset 任意。指定時は $limitの倍数で。
	 */
	public function getGachaList($limit = null, $offset = null) {
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

