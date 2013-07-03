<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserEquipment
 *
 * @author MacBook
 */
class Model_UserItem extends \Fuel\Core\Model_Crud {
    //put your code here
    protected static $_table_name = 'user_item';
    
    /*
     * getByUserItem()
     * ユーザーアイテムを取得する
     * @param $nico_id int
     * @param $item_id varchar
     * nimu.co
     */
    public function getByUserItem($nico_id, $item_id = null,$zero = false){
        
        $query = Fuel\Core\DB::select()
                                ->from(self::$_table_name)
                                ->where('nico_id' , $nico_id);
        //アイテムIDが指定ある場合
        if($item_id){
            $query->where('item_id',$item_id);
        }

        //在庫あるものを取得する。zero=trueの場合は在庫ゼロを含めた取得
        if(!$zero){
            $query->where('amount' ,'>', 0);  

        }
        $result_row = $query->execute()->as_array();
        
        if(!$result_row){
            return null;
        }
        
        
        return $result_row;
        
    }

    /*
     * getByUserItem()
     * ユーザーアイテムを取得する
     * @param $nico_id int
     * @param $item_id varchar
     * nimu.co
     */
    public function getByUserAvatar($nico_id,$job_id=null, $item_id = null,$zero = false){
        
        $query = Fuel\Core\DB::select()
                                ->from(self::$_table_name)
                                ->where('nico_id' , $nico_id);

        
        
        //JOB指定ある場合
        $use_type = 0 ; //0 はフリー
        if($job_id){            
            switch ($job_id) {
                case '1':
                    $use_type = array(0,1);

                    break;
                case '2':
                    $use_type = array(0,2);

                    break;
                case '3':
                    $use_type = array(0,3);

                    break;
                case '4':
                    $use_type = array(0,4);

                    break;

                default:
                    break;
            }
            
            $query->where('use_type','IN' , $use_type);
        }

        
        //アイテム区分をアバター（A:アバター / W:武器）
        $query->where('type','IN' ,array('A','W') );

        //アイテムIDが指定ある場合
        if($item_id){
            $query->where('item_id',$item_id);
        }

        //在庫あるものを取得する。zero=trueの場合は在庫ゼロを含めた取得
        if(!$zero){
            $query->where('amount' ,'>', 0);  

        }
        $result_row = $query->execute()->as_array();
        
        if(!$result_row){
            return null;
        }
        
        
        return $result_row;
        
    }
    
    
    
    /*
     * setItem()
     * ユーザーデータを登録する
     * @param $nico_id int
     * @param $amount int
     * nimu.co
     */
    public function setItem($action ,$nico_id, $item_id , $amount=1){

        //増減ステータスの分岐
        switch ($action) {
            case 'add':

                $sql_insert ='INSERT INTO user_item (id, nico_id, type ,item_id, amount, update_date, insert_date) ';
                $sql_value ="VALUES (NULL,"  .$nico_id. ",'" .  substr($item_id, 0, 1). "','" .$item_id . "',".$amount.", now(), now()) ";
                $sql_on_deplicate = 'ON DUPLICATE KEY UPDATE amount= amount + '. $amount.', update_date =now();';
                $sql = $sql_insert. $sql_value . $sql_on_deplicate;
                break;

            case 'del':

                $row = $this->getByUserItem($nico_id, $item_id);
                if($row){
                    if($row[0]['amount'] >= $amount){
                        
                        $sql_update ='update user_item set update_date = now() ,amount = amount - '. $amount;
                        $sql_where =" where nico_id ='"  .$nico_id. "' and type ='" .  substr($item_id, 0, 1). 
                                                        "' and item_id ='" .$item_id . "';";
                        $sql = $sql_update . $sql_where;
                        
                        
                    }else{
                        //在庫がないものはエラー
                        $resulit['status'] = 'NG';
                        $resulit['message']='アイテムが足りません';
                        return false;
                    }
                }else{
                    //在庫がないものはエラー
                    $resulit['status'] = 'NG';
                    $resulit['message']='アイテムが足りません';
                    return false;
                }

                break;

            default:
                $resulit['status'] = 'NG';
                return false;
                break;
        }
        
        try {

            $query = DB::query($sql);
            $ret = $query->execute();

            if($ret > 0){
                $resulit = true;
            }else{
                $resulit = false;
            }

            return $resulit;        

        }  catch (Exception $e){
            $resulit['status'] = 'NG';
            return false;
        }
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
