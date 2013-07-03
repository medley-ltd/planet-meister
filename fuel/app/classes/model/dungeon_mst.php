<?php
/**
 * ショップマスターモデル
 *
 * @author nimu.co
 */
use \Fuel\Core;

class Model_DungeonMst extends Model_Crud {
	protected static $_table_name = 'dungeon_mst';
	protected static $_primary_key = 'id';

	/**
	 * ダンジョンの一覧を取得する。
	 * @param int $limit  任意。
	 * @param int $offset 任意。指定時は $limitの倍数で。
	 */
	public function getDungeonMst($limit = null, $offset = null) {
		$query = DB::select()->from(self::$_table_name);
        
        $target_date = date('Y-m-d h:m:s');
        
        //公開日の範囲内であるか
        $query->where('open_start_date','=<',$target_date);
        $query->where('open_end_date','=>',$target_date);

		if (!is_null($limit)) {
			$query->limit($limit);
		}
		if (!is_null($offset)) {
			$query->offset($offset);
		}

		$query->order_by('is_new');

        return $query->execute()->as_array();
    }
}

