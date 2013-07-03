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

class Model_Gachadata extends \Fuel\Core\Model_Crud{
    //put your code here
    protected static $_table_name = 'gacha_data';
    protected static $_primary_key = 'id';



    
    /*
     * getGachaResult()
     * ガチャ結果を取得する
     * @param $gacha_id int
     * @param $get_number int
     * nimu.co
     */
    public function getGachaResult($gacha_id , $get_number){
        $query = Fuel\Core\DB::select('gift_id','rate')
                                ->from(self::$_table_name)
                                ->where('gacha_id' , $gacha_id);

        $query->order_by(DB::expr('RAND()'));
        
        $result_row = $query->execute()->as_array();
        
        if(!$result_row){
            return null;
        }
        
        $ret_giftid = null;
        
        //rateのsumのMAX値は1000となり、mt_rand(1,1000)の値と合致及びrate-sumが下回った場合、
        //当選となりその時のgift_idを返す
        foreach ($result_row as $value) {
            $rate_sum += $value['rate'];
            if($rate_sum <= $get_number){
                $ret_giftid = $value['gift_id'];
            }
            
        }
        
        return $ret_giftid;
        
    }
    
    
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
        if(isset($config_array['now_stamina'])){
            $query->value('now_stamina',$config_array['now_stamina']);
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
