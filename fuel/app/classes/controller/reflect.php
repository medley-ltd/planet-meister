<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reflect
 *
 * @author niimura
 */
class Controller_Reflect extends Controller_Rest {
    //put your code here
    
    public function get_index(){
        
        $data['status'] = 'OK';
        $this->response($data);
        
    }
}

?>
