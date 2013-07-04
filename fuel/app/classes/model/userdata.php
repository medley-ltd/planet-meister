<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserData
 *
 * @author nimu.co
 */
class Model_UserData extends \Fuel\Core\Model_Crud {
    //put your code here
    protected static $_table_name = 'user_data';
    protected static $_primary_key = 'nico_id';
    
    protected static $_properties = array(
        'nico_id' ,
        'name' ,
        'level' ,
        'exp' ,
        'sap_money' , 
        'money' ,
        'now_stamina' ,
        'max_stamina' ,
        'stamina_last_recovery_date' ,
        'status_pt' ,
        'remaining_status_pt' ,
        'friend_count' ,
        'friend_max_count' ,
        'base_hp' ,
        'base_mp' ,
        'base_att' ,
        'base_spd' ,
        'base_def' ,
        'base_wis' ,
        'current_job_id' ,
        'tutrial' ,
        'login_bonus_get_date' ,
        'profile_comment' ,
        'last_login_date' ,
        'update_date' ,
        'insert_date'
    );
    
    
    /*
     * get()
     * ユーザーデータを取得する
     * @param $nico_id int
     * nimu.co
     */
    public function get($nico_id){
        $ret = self::find_by('nico_id', $nico_id);
        if(!$ret){
            //return false;
        }
        return $ret[0];
        
    }
    
    /*
     * update()
     * ユーザーデータを登録する
     * @param $nico_id int
     * nimu.co
     */
    public function entry($user_param_array){
        if(!$this->get($user_param_array['nico_id'])){
            $user = self::forge();
            $user->set(array(
                      'nico_id' => $user_param_array['nico_id'],
                      'name'    => $user_param_array['name'],
                      'level'   => 1,        
                            
            ));
            
            return $user->save();
            
            
        }else{
            return false;
        }
    }
    
    /*
     * updateTurtrial
     * チュートリアル更新
     * 
     */
    public function updateTurtrial($nico_id , $status){
        
        $query = Fuel\Core\DB::update(self::$_table_name);

        $query->value('turtrial',$status);
        $query->value('update_date','now()');
        
        $query->where('nico_id',$nico_id);
        $result = $query->execute();
        return $result;
        
    }
    
    
    
    /*
     * levelup()
     * レベルアップ
     * @param $nico_id int
     * nimu.co
     */

    /*
     * update()
     * ユーザーデータ更新
     * @param $nico_id array
     * nimu.co
     */
    public function update($user_param_array){
        
        $user_dt = $this->get($user_param_array['nico_id']);
        
        $query = Fuel\Core\DB::update('user_data');

        //なまえ更新
        if(isset($user_param_array['name'])){
            $query->value('name',$user_param_array['name']);
        }
        /*
        if($user_param_array['level']){
            $query->value('level',$user_dt['level'] + 1 );
        }
        */
        //EXP更新
        if(isset($user_param_array['exp'])){
            $query->value('exp', $user_param_array['exp']);
        }
        //課金額更新
        if(isset($user_param_array['sap_money'])){
            $query->value('sap_money',$user_param_array['sap_money']);
        }

        //ゲーム内マネー更新
        if(isset($user_param_array['money'])){
            $query->value('money',$user_param_array['money']);
            $query->value('money',$user_dt['money'] + $user_param_array['money']);
        }

        //現在スタミナ更新
        if(isset($user_param_array['now_stamina'])){
            $query->value('now_stamina',$user_param_array['now_stamina']);
        }

        //最大スタミナ更新 LV-UP時
        if(isset($user_param_array['max_stamina'])){
            $query->value('max_stamina',$user_param_array['max_stamina']);
        }

        //スタミナ最終回復時刻更新
        if(isset($user_param_array['stamina_last_recovery_date'])){
            $query->value('stamina_last_recovery_date',$user_param_array['stamina_last_recovery_date']);
        }

        //ST-pt更新
        if(isset($user_param_array['status_pt'])){
            $query->value('status_pt',$user_param_array['status_pt']);
        }

        //残りST-pt更新
        if(isset($user_param_array['remaining_status_pt'])){
            $query->value('remaining_status_pt',$user_param_array['remaining_status_pt']);
        }

        //フレンド数更新
        if(isset($user_param_array['friend_count'])){
            $query->value('friend_count',$user_param_array['friend_count']);
        }

        //フレンド最大数更新
        if(isset($user_param_array['friend_max_count'])){
            $query->value('friend_max_count',$user_param_array['friend_max_count']);
        }

        //基本HP更新
        if(isset($user_param_array['base_hp'])){
            $query->value('base_hp',$user_param_array['base_hp']);
        }

        //基本MP更新
        if(isset($user_param_array['base_mp'])){
            $query->value('base_mp',$user_param_array['base_mp']);
        }

        //基本att更新
        if(isset($user_param_array['base_att'])){
            $query->value('base_att',$user_param_array['base_att']);
        }

        //基本spd更新
        if(isset($user_param_array['base_spd'])){
            $query->value('base_spd',$user_param_array['base_spd']);
        }

        //基本def更新
        if(isset($user_param_array['base_def'])){
            $query->value('base_def',$user_param_array['base_def']);
        }

        //基本wis更新
        if(isset($user_param_array['base_wis'])){
            $query->value('base_wis',$user_param_array['base_wis']);
        }

        //現在job-id更新
        if(isset($user_param_array['current_job_id'])){
            $query->value('current_job_id',$user_param_array['current_job_id']);
        }

        //チュートリアルステータス更新
        if(isset($user_param_array['tutrial'])){
            $query->value('tutrial',$user_param_array['tutrial']);
        }

        //ログインボーナス更新
        if(isset($user_param_array['login_bonus_get_date'])){
            $query->value('login_bonus_get_date',$user_param_array['login_bonus_get_date']);
        }

        //プロフィール更新
        if(isset($user_param_array['profile_comment'])){
            $query->value('profile_comment',$user_param_array['profile_comment']);
        }

        //更新日付の更新
        $query->value('update_date',date('Y-m-d H:i:s'));

        $query->where('nico_id',$user_param_array['nico_id']);
        $result = $query->execute();
        return $result;
        
    }
    
    
}

?>
