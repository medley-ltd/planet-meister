<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Controller_User extends Controller
{
    
    
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
    
    public function action_index(){
        
        $data = array();
        //$data['rows'] = Model_Post::find_all();
        $data['rows'] = Model_UserData::find_all();
        return View::forge('user/list',$data);
        
    }
    
}

?>
