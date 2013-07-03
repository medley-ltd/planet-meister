<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gameconfig
 *
 * @author niimura
 */
use Log\Log;

class Controller_Gameconfig extends Controller_Rest{
    //put your code here
    
    public function get_index(){
        try {
            // geame_configを取得
            $model_gameconfig = new Model_Gameconfig();
            
            $game_config = $model_gameconfig->getGameConfig();
            
            if(!$game_config){
                $game_config['status'] = 'NG';
                
                Util_Jsonio::output($game_config);
                
                //$this->response($game_config);
                return;
            }
            


            // popデータを取得
            $game_config['pop'] = array('xxxxxx','ccccccc');

            // バナーデータを取得
            $game_config['illust'] =array('illust/ills00.png','illust/banner_an.gif');

            //
            $game_config['unixtime'] = time();

            // ステータス判定
            $game_config['status'] = 'OK';
            Util_Jsonio::output($game_config);

            
        }  catch (Exception $e){
            Log::error($e);
            
            $game_config['status'] = 'NG';
            Util_Jsonio::output($game_config);
        }
    }
}

?>
