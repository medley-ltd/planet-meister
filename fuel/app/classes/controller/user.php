<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use Log\Log;
class Controller_User extends Controller_JsonCommon
{
    
    //フラグ詳細　registered
    const USER_REGISTERED = 0;
    const USER_NOTENTRY = 1;
    const USER_NICO_NOTFOUND = 2;
    const PARAM_ERROR = 99;
    
    
    //ユーザー登録基本初期情報
    protected static $_base_status = array(
        'level' => '1',
        'exp' => '0',
        'money'=> '1000',
        'now_stamina'=> '10',
        'max_stamina'=> '10',
        'friend_count'=> '0',
        'friend_max_count'=> '20',
        'base_hp'=> '100',
        'base_mp'=> '50',
        'base_att'=> '10',
        'base_spd'=> '10',
        'base_def'=> '10',
        'base_wis'=> '10',
        //'current_job_id'=> '',
        'current_avatar_id'=> '000001',
        'current_weapon_id'=> '000001',//
        
    );
    
    public $_response;
    
    public function __construct(\Request $request) {
        parent::__construct($request);
    }

    /*
     *  テスト用　オートインサート
     */
    public function action_auto_insert(){
        for ($i = 0; $i < 10 ; $i++)
        {
            $post = Model_Post::forge();
            
            $row = array();
            $row['title'] = $i .'番目の投稿の件名';
            $row['summary'] = $i .'番目の投稿の概要';
            $row['body'] = 'これは' . $i .'番目の投稿です' . '¥n' . 'テストで自動投稿しています。';
            
            $post->set($row);
            $result = $post->save();
        }
        
        echo 'Finished!';
    }
    /*
     *  ユーザー登録
     */
    public function action_entry($nico_id = null ,$name = null){

        // nico_id チェック
        $nico_id = Input::get('nico_viewer_id');
        
        if(!$nico_id){
            $data['status'] = 'NG';
            $data['message'] = 'ユーザーID取得に失敗(USR-Ent 01)';
            Util_Jsonio::output($data);
            return ;
        }
        
  /*      if($this->action_checkById($nico_id) != self::USER_NOTENTRY){
            return false;
        }
 */       
        //なまえチェック
/*        if(!$name){
            return false;
        }
*/
        $model_userdata = new Model_UserData();
        $entry_base = self::$_base_status;
        $entry_base['nico_id'] = $nico_id;
        $entry_base['name'] = 'default';
        
        //nicoAPIよりニコポを取得する
        $entry_base['sap_money'] =0;

        $now_date = date('Y-m-d H:i:s');
        $entry_base['last_login_date'] = $now_date;
        $entry_base['update_date'] = $now_date;
        $entry_base['insert_date'] = $now_date;
        
        //TODO model直になっているが、ヴァリデートチェックを入れること
        $model_userdata->set($entry_base);
        $model_userdata->save();
        
        $data['status'] = 'OK';
        Util_Jsonio::output($data);
        
    }
    
    /*
     *  ユーザー情報取得
     */
    public function action_update(){
        $nico_id = 1;
        $model_userdata = new Model_UserData();
        $user_dt = $model_userdata->get($nico_id);
        
        
        $query = Fuel\Core\DB::update('user_data');
        $query->value('level',$user_dt['level'] + 1 );
        $query->where('nico_id',1);
        $result = $query->execute();
        
        
        
    }
    public function action_update2(){
        $nico_id = 1;
        $model_userdata = new Model_UserData();
        
        $user_param_array['nico_id'] =$nico_id;
        //$user_param_array['name'] ='てすと';
        $user_param_array['money']='1000';
        $ret = $model_userdata->update($user_param_array);
        
        if($ret){
        Log::error('OK');
        }else{
        Log::error('NG');
        }
        
    }

     
     /*
      * ユーザーステータス取得
      */
     public function get_status(){
         
         //try {
             
            //request パラメータにIDが入ってた場合はユーザー指定 
            $nico_id = Util_Jsonio::get('id'); 
            $gettype ='friend';

            //自身のデータを取得する
            if(!$nico_id){
                $nico_id = Input::get('nico_viewer_id');
                $gettype ='owner';
            }

            if(!$nico_id){
                $data['status'] = 'NG';
                $data['message'] = 'ユーザーID取得に失敗(USR-Sta 01)';
                Util_Jsonio::output($data);
                //throw new Exception('ユーザーID取得に失敗(USR-Sta 01)');
                
                return;
            }
             
         //} catch (Exception $exc) {
             //echo $exc->getTraceAsString();
         //       throw new Exception('予期せぬエラーが発生しました(USR-Sta 02)');
             
         //}


/*
        //IDを取得
        $nico_id	= Util_Jsonio::get('id');
        $gettype = null; 
        if(!$nico_id){
            // 自ユーザー APIを通す
            $nico_id = '1';     //デバッグ用
            $gettype ='owner';
        }else{
            //$nico_id = $nico_id->id;
            $gettype ='friend';
        }
 */       
        $model_userdata = new Model_UserData();
        $model_userjob = new Model_UserJob();

        
        $user_status = array();
        //ユーザーデータの取得
        $user_dt = $model_userdata->get($nico_id);
        //ユーザーが現在選択しているジョブ（装備など）を取得
        $user_job = $model_userjob->getByUserJobId($user_dt['current_job_id']);
        
        
        $user_status['id'] = $user_dt['nico_id'];
        $user_status['name'] =$user_dt['name'];
        $user_status['level'] =$user_dt['level'];
        $user_status['exp'] =$user_dt['exp'];
        
        //levelマスタを参照して次LVUP達成に要するEXPを取得してくる
        
        $user_status['explvup'] =$user_dt['exp'];
        
        //取得が自分（owner）であればポイント関連を取得する
        if($gettype == 'owner' ){
            $user_status['sap_money'] =$user_dt['sap_money'];
            $user_status['money'] =$user_dt['money'];
        }
        $user_status['now_stamina'] =$user_dt['now_stamina'];
        $user_status['max_stamina'] =$user_dt['max_stamina'];
        $user_status['stamina_last_recovery_date'] =$user_dt['stamina_last_recovery_date'];
        $user_status['friend_count'] =$user_dt['friend_count'];
        $user_status['friend_max_count'] =$user_dt['friend_max_count'];
        
        $user_status['bhp'] =$user_dt['base_hp'];
        $user_status['bmp'] =$user_dt['base_mp'];
        
        $user_status['batt'] =$user_dt['base_att'];
        $user_status['bspd'] =$user_dt['base_spd'];
        $user_status['bdef'] =$user_dt['base_def'];
        $user_status['bwis'] =$user_dt['base_wis'];
        
        
        
        $user_status['job_id'] =$user_dt['current_job_id'];
        $user_status['tutrial'] =$user_dt['tutrial'];
        $user_status['login_bonus_get_date'] =$user_dt['login_bonus_get_date'];
        $user_status['profile_comment'] =$user_dt['profile_comment'];
        
        $user_status['weapon_id'] = $user_job['weapon_id'];

        if($user_job['avater_id_1']){
            $user_status['avater'][]=$user_job['avater_id_1'];
        }
        if($user_job['avater_id_2']){
            $user_status['avater'][]=$user_job['avater_id_2'];
        }
        if($user_job['avater_id_3']){
            $user_status['avater'][]=$user_job['avater_id_3'];
        }
        if($user_job['avater_id_4']){
            $user_status['avater'][]=$user_job['avater_id_4'];
        }
        if($user_job['avater_id_5']){
            $user_status['avater'][]=$user_job['avater_id_5'];
        }
        if($user_job['avater_id_6']){
            $user_status['avater'][]=$user_job['avater_id_6'];
        }

        Util_Jsonio::output($user_status);
        
     }
    
     /*
      * チュートリアル更新
      */
     public function get_turtrial(){
         /*$post_data1 =  \Fuel\Core\Input::post();
         var_dump($post_data1);
*/
        $nico_id = Input::get('nico_viewer_id'); 
		$this->data['id']	= Util_Jsonio::get('id');
		$this->data['process']	= Util_Jsonio::get('process');
        
        //パラメータチェック
        if($this->data['id'] != $nico_id){
            
            //パラメータエラー
            
            return;
        }

        
        
        
/*         
         //NICO APIからnico_id取得
         if(!$nico_id){
             
             $nico_id = 1;
             
             //return false;
         }
         $user = Model_UserData::find($nico_id);
         $user->set(array('turtrial' => $status,
                          'update_date' => date()));
         $user->save();
*/
         $this->response($data);
    }     

     
     public function get_job($nico_id = null){
         
         $model_user_job = new Model_UserJob();
         $model_job_mst = new Model_jobMst();
         
         // APIよりnico_id取得

         $nico_id = 1;  //デバッグ用
         
         if(!$nico_id){
    
             // エラー
             $response['status'] = 'NG';
             $response['message'] = 'user-id取得エラー(UJ-01)';
             Util_Jsonio::output($response);
             return;
         }
         
         $ret = $model_user_job->getByUserJoblist($nico_id);
         
         foreach ($ret as $list) {
             
             $job_data['id'] = $list['user_job_id'];
         //Log::error('job_id->'.$list['job_id']);
         //Log::error('level->'.$list['level']);
             
             //JOBマスタから取得
             $jobmst = $model_job_mst->getJobData($list['job_id'],$list['level'],$list['exp']);
             
             $job_data['name'] = $jobmst['job_name'];
             $job_data['level'] = $list['level'];

             $job_data['ismax'] = $jobmst['ismax'];
             $job_data['explevup'] = $jobmst['explvup'];
             $job_data['nextskill'] = $jobmst['nextskill'];
             $job_data['nextskilllevel'] = $jobmst['nextskilllevel'];
             
             $job_data['hp'] = $list['passive_hp'];
             $job_data['mp'] = $list['passive_mp'];
             $job_data['att'] = $list['passive_att'];
             $job_data['spd'] = $list['passive_spd'];
             $job_data['def'] = $list['passive_def'];
             $job_data['wis'] = $list['passive_wis'];
             $skill_list =  array();
             if($list['skill_id_1']){
                 $skill_list[]=$list['skill_id_1'];
             }
             if($list['skill_id_2']){
                 $skill_list[]=$list['skill_id_2'];
             }
             if($list['skill_id_3']){
                 $skill_list[]=$list['skill_id_3'];
             }
             if($list['skill_id_4']){
                 $skill_list[]=$list['skill_id_4'];
             }
             $job_data['skill'] = $skill_list;
             
             $job_list[]=$job_data;
         }
         
         $ret_data = array();
         $ret_data['status'] ='OK';
         $ret_data['job'] =$job_list;
         
         
         Util_Jsonio::output($ret_data);
         //$this->response($ret_data);
         
         
     }
    
    /*
     *  ユーザー所持アイテム 
     */
     public function get_item(){
        
        $request = Util_Jsonio::get("request","json_decode");
        
        //nico_id取得
        $nico_id=1; 

        
        /*
        if(empty($request->id)){
            $response['status'] = 'NG';
            $response['message'] = 'user-id取得エラー(UI-01)';
            Util_Jsonio::output($response);
            return;
        }
         
         */
        
        if(empty($request->act)){
            $response['status'] = 'NG';
            $response['message'] = 'act取得エラー(UI-02)';
            Util_Jsonio::output($response);
            return;
        }

        /*
        $nico_id=1; 
        $item_id = 'Z00003';
        //$item_id=null;
        $amount = 1;
        $action = 'del';
        
        $request = new stdClass();
        $request->act='add';
        $request->id= 'Z00004';
        $request->number= '10';
        */
        
        //アイテムIDの指定がなく追加、減算をする場合はエラー
        if($request->act=='add' || $request->act == 'del'){
            if(empty($request->id)){
                $response['status'] = 'NG';
                $response['message'] = 'item-id取得エラー(UI-03)';
                Util_Jsonio::output($response);
                return;
            }
            if(empty($request->number)){
                $response['status'] = 'NG';
                $response['message'] = '個数取得エラー(UI-04)';
                Util_Jsonio::output($response);
                return;
            }elseif($request->number < 1){
                $response['status'] = 'NG';
                $response['message'] = '個数取得エラー(UI-05)';
                Util_Jsonio::output($response);
                return;
            }
        }
        
        //ユーザー所持品
        $model_useritem = new Model_UserItem();
        
        if($request->act=='add' || $request->act == 'del'){
            if(!$model_useritem->setItem($request->act, $nico_id, $request->id, $request->number)){
                $response['status'] = 'NG';
                $response['message'] = 'add/del エラー(UI-05)';
                Util_Jsonio::output($response);
                return;
            }
        }
        
        //getはここで取得
        $row = $model_useritem->getByUserItem($nico_id, $request->id);
        if($row){
            $ret['status'] = 'OK';
            $ret['id'] = $row[0]['item_id'];
            $ret['number'] = $row[0]['amount'];
        }else{
            $ret['status'] = 'OK';
            $ret['id'] = $request->id;
            $ret['number'] = '0';
        }

        //JSON形式で出力
        Util_Jsonio::output($ret);
         
     }

     /*
      * ユーザーアイテムを取得しJSONレイアウトに生成する
      */
     public function get_getUserItem($nico_id,$item_id = null){
        //ユーザー所持品
        $model_useritem = new Model_UserItem();

        $row = $model_useritem->getByUserItem($nico_id,$item_id);
        
        foreach ($row as $value) {
            $user_item['id'] = $row['item_id'];
            $user_item['number'] = $row['amount'];
        }
         return $user_item;
     }
     
     
     public function get_avatar(){
         
         //userjobid を取得する
         
         $user_job_id = 1;
         $nico_id = 1;
         
         $model_user_job = new Model_UserJob();
         $job = $model_user_job->getByUserJobId($user_job_id);
         
         $model_user_item = new Model_UserItem();
         $ret = $model_user_item->getByUserAvatar($nico_id, $job['job_id']);
         
        //JSON形式で出力
        Util_Jsonio::output($ret);
         
         
     }
     
     
    //-------------------------------------------------------------------------
    // ユーティリティ系
    //-------------------------------------------------------------------------

    
    /*
     *  ユーザーデータチェック 
     */
    public function action_checkById($nico_id){
        
        // paramチェック
        if(!$nico_id){
            return self::PARAM_ERROR;
        }

        //APIチェック
        //ニコニコ会員未登録
        //return self::USER_NICO_NOTFOUND;

        //存在チェック
        $model_userdata = new Model_UserData();
        if(!$model_userdata->get($nico_id)){
            return self::USER_NOTENTRY;
        }

        //登録済みユーザー
        return self::USER_REGISTERED;
        
    }
/*
    public function after($response)
    {
        //echo $response;
        $response['test'] = mb_convert_encoding('ほげほげ', "SJIS", "UTF-8,EUC-JP,auto");
        //$response['test'] = 'aaa';

        
        // UTF-8に戻す
        $str = '東京';
        //$response['test2'] =$this->unicode_decode($str);        
        //$response['test'] =$this->unicode_encode($response['test2']);    
        
        header( 'Content-Type: text/javascript; charset=utf-8' );
        $response['test'] = mb_convert_encoding($str, "UTF-8", "SJIS");
        
        return parent::after($response);
    }
  */  

    
    
}

?>
