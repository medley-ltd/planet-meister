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

class Model_jobMst extends \Fuel\Core\Model_Crud{
    //put your code here
    protected static $_table_name = 'job_mst';
    
    /*
     * get()
     * アイテムデータを取得する
     * @param $nico_id int
     * nimu.co
     */
    public function getJobData($job_id ,$level = 1,$now_exp = 0){

        $query_row = Fuel\Core\DB::select()
                                ->from('job_mst')
                                ->where('job_id' , $job_id)
                                ->where('level',$level)
                                ->execute()
                                ->as_array();
        
        if(!$query_row){
            return null;
        }

        //次のLVUPに必要なEXPを計算
        $next_levup = Fuel\Core\DB::select()
                                ->from('job_mst')
                                ->where('job_id' , $job_id)
                                ->where('level',$level+1)
                                ->execute()
                                ->as_array();

        //データなしはMAX
        if(!$next_levup){
            $query_row[0]['explvup']='0';
            $query_row[0]['ismax']='1';
        }else{
            $query_row[0]['explvup']= (string)($next_levup[0]['exp'] - $now_exp);
            $query_row[0]['ismax']='0';
        }
        
        
        //次のを覚えるLVを取得
        $next_skill = Fuel\Core\DB::select()
                                ->from('job_mst')
                                ->where('job_id' , $job_id)
                                ->where('level','>',$level)
                                ->where('skill_id','!=',null)
                                ->limit(1)
                                ->execute()
                                ->as_array();
        
        if(!$next_skill){
            $query_row[0]['nextskill']=null;
            $query_row[0]['nextskilllevel']=null;
        }else{
            $query_row[0]['nextskill']=$next_skill[0]['skill_id'];
            $query_row[0]['nextskilllevel']=$next_skill[0]['level'];
        }
               
        
        //var_dump($query_row[0]);
        
        return $query_row[0];
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
