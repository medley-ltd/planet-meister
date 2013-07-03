<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MonsterMst
 *
 * @author nimu.co
 */
class Model_MonsterMst extends Model {
    //put your code here
    protected static $_table_name = 'monster_mst';
    
    
    
/*
     * アバターデータを全件取得してくる
     */
    public function getMonster($monster_id = null){

        $query = Fuel\Core\DB::select()
                                ->from(self::$_table_name);
        //アイテムIDが指定ある場合        
        if($monster_id){
            $query->where('monster_id',$monster_id);
        }

        $result_row = $query->execute()->as_array();
        
        if(!$result_row){
            return null;
        }        
        
        return $result_row;
        
    }
        
    
    /*
     * get()
     * アイテムデータを取得する
     * @param $nico_id int
     * nimu.co
     */
    public function get($item_id){
        
        if(is_array($item_id)){
            return self::find('all',array('where' ,'IN', $item_id));    
        }else{
            return self::find('all',array('where' => $item_id));
        }

        return null;
    }
    
    /*
     * entry()
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
     * update()
     * ユーザーデータを登録する
     * @param $nico_id int
     * nimu.co
     */
    public function update($user_param_array){
        $user = self::find_by_pk($user_param_array['nico_id']);
        
        /*$user->set(array(
            'name' => $user_param_array['name'],
            'level' => $user_param_array[''],
            'exp' => $user_param_array[''],
            'sap_money'=> $user_param_array[''],
            'money'=> $user_param_array[''],
            'now_stamina'=> $user_param_array[''],
            'max_stamina'=> $user_param_array[''],
            'friend_count'=> $user_param_array[''],
            'friend_max_count'=> $user_param_array[''],
            'base_hp'=> $user_param_array[''],
            'base_mp'=> $user_param_array[''],
            'base_att'=> $user_param_array[''],
            'base_spd'=> $user_param_array[''],
            'base_def'=> $user_param_array[''],
            'base_wis'=> $user_param_array[''],
            'current_job_id'=> $user_param_array[''],
            'current_avatar_id'=> $user_param_array[''],
            'current_weapon_id'=> $user_param_array[''],
            'profile_comment'=> $user_param_array[''],
            'last_login_date'=> $user_param_array[''],
            'update_date' => now(),
            'insert_date' => now(),
           
        ));
        */
        $user->set($user_param_array);
        return $user->save();
                
    }
}

?>
