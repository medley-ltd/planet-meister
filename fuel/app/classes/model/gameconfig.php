<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JobMst
 *
 * @author nimu.co
 */
use Log\Log;

class Model_Gameconfig extends \Fuel\Core\Model_Crud{
    //put your code here
    protected static $_table_name = 'game_config';
    protected static $_primary_key = 'id';

    /*
     * getGameConfig()
     * アイテムデータを取得する
     * nimu.co
     */
    public function getGameConfig(){
        $ret = self::find_by();
        if(!$ret){
            return null;
        }
        return $ret[0];
    }

	/**
	 * メンテ状態かどうか判定する。
	 * メンテ開始、終了時刻が正しく設定されている前提で判定します。
	 * @param integer $nowTime 任意。判定時刻。
	 * @throws Exception
	 */
	public function isMaintenance($nowTime = null) {
		$config = $this->getGameConfig();
		if (empty($config)) {
			throw new Exception('データが取得できませんでした', -1);
		}

		$now	= is_null($nowTime) ? time() : $nowTime;
		$start	= strtotime($config['maintenace_start']);
		$end	= strtotime($config['maintenace_end']);

		if (empty($end)) {
			// start, end 両方空の時はメンテ無し
			if (empty($start)) {
				return false;
			}
			// start だけ指定されている
			$end = $now;;
		} elseif (empty($start)) {
			// end だけ指定
			$start = $now;
		}

		// start も end も指定されている
		return $start <= $now && $now <= $end;
    }

    /*
     * update()
     * 更新
     * @param $config_array array
     * nimu.co
     */
    public function update($config_array){

        $query = Fuel\Core\DB::update(self::$_table_name);


        //version更新
        if(isset($config_array['version'])){
            $query->value('version',$config_array['version']);
        }

        //staminaheal更新
        if(isset($config_array['staminaheal'])){
            $query->value('staminaheal', $config_array['staminaheal']);
        }

        //preloadbg更新
        if(isset($config_array['preloadbg'])){
            $query->value('preloadbg',$config_array['preloadbg']);
        }

        //titlebg更新
        if(isset($config_array['titlebg'])){
            $query->value('titlebg',$config_array['titlebg']);
        }

        //maintenace_start更新
        if(isset($config_array['maintenace_start'])){
            $query->value('maintenace_start',$config_array['maintenace_start']);
        }

        //maintenace_end更新
        if(isset($config_array['maintenace_end'])){
            $query->value('maintenace_end',$config_array['maintenace_end']);
        }

        //is_emagency更新
        if(isset($config_array['is_emagency'])){
            $query->value('is_emagency',$config_array['is_emagency']);
        }

        //更新日付の更新
        $query->value('update_date',date('Y-m-d H:i:s'));

        //where条件
        $query->where('id',$config_array['id']);

        //SQL実行
        $result = $query->execute();

        return $result;

    }
}

?>
