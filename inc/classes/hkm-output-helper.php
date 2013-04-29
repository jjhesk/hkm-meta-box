<?php
// Prevent loading this file directly
defined('ABSPATH') || exit ;
require_once 'hkm_refernx.php';
if (!class_exists('HKM_extRef')) {
	// HKMhelper Class
	class HKM_extRef extends HKM_refernx {
		//the function will be called from the first selected key
		static public function get_doubleStep_cloned($id, $field, $callback_prefix = '') {
			$data = get_post_meta($id, $field, TRUE);
			$html ="";
			foreach ($data as $number => $json) {
				$arr = json_decode($json);
				//echo '<pre>';
				//print_r($arr);
				//echo '</pre>';
				if (!isset($arr -> key_1) || !isset($arr -> key_2))
					continue;
				$func = $callback_prefix . $arr -> key_1;
				if (function_exists($func))
					$html.=call_user_func($func, $arr -> key_2);
			}
			return $html;
		}

	}

}
