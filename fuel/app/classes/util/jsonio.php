<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of util
 *
 * @author niimura
 */
class Util_Jsonio {
    
    //put your code here
    // UTF-8文字列をUnicodeエスケープする。ただし英数字と記号はエスケープしない。
    public static function unicode_decode($str) {
      return preg_replace_callback("/((?:[^\x09\x0A\x0D\x20-\x7E]{3})+)/", array($this,"decode_callback"), $str);
    }

    public static function decode_callback($matches) {
      $char = mb_convert_encoding($matches[1], "UTF-16", "UTF-8");
      $escaped = "";
      for ($i = 0, $l = strlen($char); $i < $l; $i += 2) {
        $escaped .=  "\u" . sprintf("%02x%02x", ord($char[$i]), ord($char[$i+1]));
      }
      return $escaped;
    }

    // Unicodeエスケープされた文字列をUTF-8文字列に戻す
    public static function unicode_encode($str) {
      return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", array($this,"encode_callback"), $str);
    }

    public static function encode_callback($matches) {
      $char = mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
      return $char;
    }

    public static function get($key,$fil=NULL) {
		if (!array_key_exists($key,$_REQUEST)) return false;

		if ($fil == "json_decode") {
			return json_decode(str_replace("\n", "<br>", $_REQUEST[$key]));
		} else {
			return $_REQUEST[$key];
		}
	}
    
    public static function output($response) {
		echo json_encode($response);
		exit;
	}
    
}

?>
