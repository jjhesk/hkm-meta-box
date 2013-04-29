<?php
// Prevent loading this file directly
defined('ABSPATH') || exit ;
require_once RWMB_FIELDS_DIR . 'select-advanced.php';
require_once RWMB_FIELDS_DIR . 'checkbox-list.php';
if (!class_exists('RWMB_Twosteps_Field')) {
	class RWMB_Twosteps_Field {

		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */

		static function admin_enqueue_scripts() {
			RWMB_Select_Advanced_Field::admin_enqueue_scripts();
			//wp_enqueue_script('rwmb-twosteps-data');
			//	wp_enqueue_style('rwmb-taxonomy', RWMB_CSS_URL . 'taxonomy.css', array(), RWMB_VER);
			//wp_enqueue_script('rwmb-taxonomy', RWMB_JS_URL . 'taxonomy.js', array('jquery', 'rwmb-select-advanced', 'wp-ajax-response'), RWMB_VER, true);
			wp_enqueue_style('rwmb-twosteps', RWMB_CSS_URL . 'steps.css', array(), RWMB_VER);
			wp_enqueue_script('rwmb-twosteps', RWMB_JS_URL . 'steps.js', array('jquery', 'wp-ajax-response'), RWMB_VER, true);

			// add_action('wp_head', array(&$this, 'print_script_head'));

		}

		/**
		 * Add default value for 'taxonomy' field
		 *
		 * @param $field
		 *
		 * @return array
		 */
		static function normalize_field($field) {
			$default_args = array('step_1' => array(), 'step_1' => array());
			if (!isset($field['options']))
				$field['options'] = array();
			//Set default args
			$field['options'] = wp_parse_args($field['options'], $default_args);
			//Field name be an array by default
			$field['field_name'] = "{$field['id']}[]";

			return $field;
		}

		/**
		 * Get field HTML
		 *
		 * @param $html
		 * @param $field
		 * @param $meta
		 *
		 * @return string
		 */
		static function html($html, $meta, $field) {
			$options = $field['options'];
			if (!isset($options['step_2'])) {
				echo 'step_2 in the options is undefined.';
				return $html;
			}
			if (!isset($options['step_1'])) {
				echo 'step_1 in the options is undefined.';
				return $html;
			}
			$options_step_1 = $options['step_1'];
			$options_step_2 = $options['step_2'];
			$html = '';
			$html .= self::step_two_advanced($options_step_1, $options_step_2, $field, $meta);
			return $html;
		}

		static function step_two_advanced(&$list1, &$list2, $fields, $data) {
			$field_id = $fields['id'];
			$setting_options = 'twosteps_options_' . $field_id;
			//	$field['options']
			$setting_options_array = array('step1' => $fields['options']['step_1_placeholder'], 'step2' => $fields['options']['step_2_placeholder'], );
			$step2_list = array();
			$field_hide = '';
			$label_step1 = isset($fields['options']['step_1_label']) ? $fields['options']['step_1_label'] : 'Step 1';
			$label_step2 = isset($fields['options']['step_2_label']) ? $fields['options']['step_2_label'] : 'Step 2';
			$v = json_decode($data, false);
			//	echo "<pre>";
			//echo $v;
			//	print_r($data);
			//	echo "</pre>";
			$li = '<div class="stepsbox">';
			$li .= '<input class="twostepcombo" type="text" name="' . $field_id . '" value="' . $data . '" hidden />';
			$li .= '<div class="section_step_hkm step1">' . $label_step1 . '</div>';
			$li .= '<select id="' . $field_id . '" class="step1 combobox ' . $field_id . '">';
			foreach ($list1 as $k => $val) {
				$li .= '<option value="' . $k . '">' . $val . '</option>';
			}
			$li .= '</select>';
			$li .= '<div class="section_step_hkm hidden step2 ' . $field_id . '">' . $label_step2 . '</div>';
			$li .= '<input id="step2" type="hidden" class="step2 combobox ' . $field_id . '"/>';
			$li .= '</div>';
			$list1_keys = array_keys($list1);
			$k_e = 0;
			foreach ($list2 as $key => $value) {
				$list_of = self::get_list_post_array_by_type_select2($value);
				$step2_list[$list1_keys[$k_e]] = $list_of;
				$k_e++;
			}

			wp_localize_script('rwmb-twosteps', 'twosteps_' . $field_id, $step2_list);
			wp_localize_script('rwmb-twosteps', $setting_options, $setting_options_array);
			return $li;
		}

		static function step_two(&$list1, &$list2, &$fields, &$saved_data) {
			$field_id = $fields['id'];
			$field_hide = '';
			$li = '<input class="twostepcombo" type="text" name="' . $field_id . '" value="' . $saved_data . '" ' . $field_hide . ' />';
			$step2_list = array();
			//step 1==============================
			$list1_keys = array_keys($list1);
			$list1 = array_merge(array("-1" => "[ empty field here ]"), $list1);
			$li .= '<label class="section_step_hkm step1">Step 1</label>';
			$li .= '<select id="' . $field_id . '" class="step1 combobox ' . $field_id . '">';
			foreach ($list1 as $k => $val) {
				$li .= '<option value="' . $k . '">' . $val . '</option>';
			}
			// print_r($list1_keys);
			$li .= '</select>';
			//step 2===========================
			$li .= '<label class="section_step_hkm hidden step2 ' . $field_id . '">Step 2</label>';
			$k_e = 0;
			foreach ($list2 as $key => $value) {
				//array==============================
				$step2_list = self::get_list_post_array_by_type($value);
				$field_id = $fields['id'] . '-' . $value;
				//print_r($step2_list);
				$li .= '<select id="' . $field_id . '-step2-' . $value . '" class="step2 combobox hidden ' . $list1_keys[$k_e] . ' ' . $field_id . '">';
				foreach ($step2_list as $k => $val) {
					$li .= '<option value="' . $k . '">' . $val . '</option>';
				}
				$li .= '</select>';

				$k_e++;
			}
			return $li;
		}

		/*extracted from hkm cross reference class*/
		static function get_list_post_array_by_type($posttype) {
			$optionpost = null;

			// $optionpost[-1] = " [ empty field here ] ";
			$items = get_posts(array('post_type' => $posttype, 'posts_per_page' => -1));
			foreach ($items as $item) {
				$optionpost[$item -> ID] = $item -> ID . " - " . $item -> post_title;
			}
			return $optionpost;
		}

		static function get_list_post_array_by_type_select2($posttype) {
			$optionpost = null;

			// $optionpost[-1] = " [ empty field here ] ";
			$items = get_posts(array('post_type' => $posttype, 'posts_per_page' => -1));
			foreach ($items as $item) {
				$optionpost[] = array("id" => $item -> ID, "text" => $item -> ID . " - " . $item -> post_title);
			}
			return $optionpost;
		}

		/**
		 * Save post taxonomy
		 *
		 * @param $post_id
		 * @param $field
		 * @param $old
		 *
		 * @param $new
		 */
		//static function save($new, $old, $post_id, $field) {
		//	wp_set_object_terms($post_id, $new, $field['options']['taxonomy']);
		//}

		/**
		 * Standard meta retrieval
		 *
		 * @param mixed     $meta
		 * @param int       $post_id
		 * @param array     $field
		 * @param bool      $saved
		 *
		 * @return mixed
		 */
		//static function meta($meta, $post_id, $saved, $field) {
		//	$options = $field['options'];
		//$meta = wp_get_post_terms($post_id, $options['taxonomy']);
		//$meta = is_array($meta) ? $meta : (array)$meta;
		//$meta = wp_list_pluck($meta, 'slug');
		//	return $meta;
		//	}

	}

}
