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
		return preg_replace_callback("/((?:[^\x09\x0A\x0D\x20-\x7E]{3})+)/", array("Util_Jsonio","decode_callback"), $str);
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
		return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", array("Util_Jsonio","encode_callback"), $str);
	}

	public static function encode_callback($matches) {
		$char = mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
		return $char;
	}

	public static function result() {

	}

	/**
	 * GETパラメータの 'request' キーに存在する JSON の値を取得する。
	 * @param string $key 任意。$key 取得したいJSONのキー。nullの時は JSON パラメータを全て返す。
	 * @param string $fil 任意。json_decode() をかけたい時は 'json_decode' を指定のこと。
	 * @return string|boolean $key がある時は、その値。$key がパラメータにない時は false を返す。
	 * @see http://press.nekoget.com/fuelphp_doc/classes/input.html
	 */
        public static function get($key = NULL, $fil = NULL) {
		if (!($params = Input::param('request'))) {
			return false;
		}
		$params = json_decode($params, true); // 連想配列で取得

		if (is_null($key)) {
			return $params;
		}

		if (empty($params) || !array_key_exists($key, $params)) {
			return false;
		}

		if ($fil == "json_decode") {
			return json_decode(nl2br($params[$key]));
		}

		return $params[$key];
	}

	public static function output($response) {
		echo json_encode($response);
		exit;
	}
}
