<?php
// Prevent loading this file directly
defined('ABSPATH') || exit ;

if (!class_exists('HKM_refernx')) {
	// HKMhelper Class
	class HKM_refernx {
		/*function __construct(){
		 $this->meta_featured_image_div=array();
		 }*/
		static public function ls_PostTitle($type, $empty_field_first = true) {
			$optionpost = array();
			if ($empty_field_first)
				$optionpost[-1] = " [ empty field here ] ";
			$items = get_posts(array('post_type' => $type, 'posts_per_page' => -1, ));
			foreach ($items as $item) {
				$optionpost[$item -> ID] = $item -> ID . " - " . $item -> post_title;
			}
			return $optionpost;
		}

		static public function ls_PostTitle_if_customField_filled($type, $customfield, $empty_field_first = true) {
			$optionpost = array();
			if ($empty_field_first)
				$optionpost[-1] = " [ empty field here ] ";
			$items = get_posts(array('post_type' => $type, 'posts_per_page' => -1, ));

			$args = array('post_type' => $type, 'posts_per_page' => -1, 'post_status' => 'publish', 'meta_query' => array("relation" => "",
			//===
			array('key' => $customfield, )
			//===
			));
			$actionquery = new WP_query($args);
			if ($actionquery -> have_posts()) :
				while ($actionquery -> have_posts()) : $actionquery -> the_post();
				$optionpost[$actionquery -> post->ID] = $actionquery -> post->ID . " - " . $actionquery -> post->post_title;
				endwhile;
			endif;
			return $optionpost;
		}

		static public function ls_PostTitle_BeforePastDate($type, $date_field, $empty_field_first = TRUE, $past = TRUE) {
			$optionpost = array();
			$items = get_posts(array('post_type' => $type, 'posts_per_page' => -1, ));
			//$selected_post_id = array();
			if ($empty_field_first)
				$optionpost[-1] = " [ empty field here ] ";

			foreach ($items as $item) {
				//$selected_post_id[]=$item -> ID;
				$event_start = get_post_meta($item -> ID, $date_field, TRUE);
				//
				//2012年12月6日
				$date = date_parse_from_format("yy年m月d日", $event_start);
				$ndate = strtotime($date['year'] . '-' . $date['month'] . '-' . $date['day']);
				$ntoday = strtotime("now");
				//indication before now
				if ($ndate < $ntoday && $past) {
					//this is the past day from
					$optionpost[$item -> ID] = $item -> ID . " - " . $item -> post_title;
				}
				//indication in the future
				if ($ndate > $ntoday && !$past) {
					$optionpost[$item -> ID] = $item -> ID . " - " . $item -> post_title;
				}
			}
			return $optionpost;
		}

		static public function ls_slug($id, $type) {
			$optionpost = null;
			$items = get_posts(array('post_type' => $type, 'posts_per_page' => -1, "include" => array($id)));
			$optionpost = $items[0] -> post_name;
			return $optionpost;
		}

		static public function getPostThumb_url_by_id($id) {
			$optionpost = wp_get_attachment_image_src(get_post_thumbnail_id($items[0] -> ID), "full");
			if (!isset($optionpost)) {
				return "";
			}
			return $optionpost[0];
		}

		static public function getPostImg_url_by_id_field($id, $field) {
			$idc = get_post_meta($id, $field, true);
			$m = wp_get_attachment_image_src($idc, 'full');
			return $m[0];
		}

		static public function getPostImgRefex($value, $custom_field, $picture_size = "full") {
			//we assume to get One ID;
			$img_id = get_post_meta($value, $custom_field, TRUE);
			$specific = wp_get_attachment_image_src($img_id, $picture_size);
			$specific['title'] = get_the_title($value);
			$specific['link'] = get_permalink($value);
			return $specific;
		}

		static public function page_image_srcs_to_the_box($field, $page_id) {
			$args = array('post_type' => 'page', 'status' => 'published', 'p' => $page_id);
			$image_page_qry = new WP_Query($args);
			$totalpost_count = $image_page_qry -> post_count;
			$out = array();
			if ($image_page_qry -> have_posts()) :
				while ($image_page_qry -> have_posts()) : $image_page_qry -> the_post();
					$ids = get_post_meta($image_page_qry -> post -> ID, $field, FALSE);
					$k = count($ids);
					for ($v = 0; $v < $k; $v++) {
						$img = wp_get_attachment_image_src($ids[$v], "full");
						$out[] = $img[0];
					}
				endwhile;
			endif;
			wp_reset_query();
			return $out;
			// as an array
		}

		static public function ProcessPostExtend($get_list_ids, $options) {
			$list = array();
			$defaults = array(
			//=============
			/*
			 "picture"=>array(
			 "picture_size" => "full",
			 "picturefield"=>FALSE,
			 ),
			 */
			//=============
			"date" => 'l, F jS, Y',
			//=============
			"title" => TRUE,
			//=============
			// "html" => FALSE,
			//=============
			"link" => TRUE,
			//=============
			//"excerpt"=>FALSE
			//=============
			);
			$options = wp_parse_args($options, $defaults);
			foreach ($get_list_ids[0] as $key => $value) :
				//$objectpost = get_post($value);
				if (isset($options["picture"])) {
					if ($options["picture"]["picturefield"] == FALSE) {
						//get this value as the ID of thumbnail
						$img_id = get_post_thumbnail_id($value);
					} else {
						//get this value as the ID of images
						$img_id = get_post_meta($value, $options["picture"]["picturefield"], TRUE);
					}
					$specific = wp_get_attachment_image_src($img_id, $options["picture"]["picture_size"]);
				}

				if (isset($options["date"])) {
					//$options["date"]
					//@source http://codex.wordpress.org/Formatting_Date_and_Time
					//$objectpost->post_date
					//date_i18n($options["date"] ,strtotime($objectpost->post_date));
					//strtotime($objectpost->post_date)
					//get_the_date
					$specific["date"] = get_the_time($options["date"], $value);
				}
				if (isset($options["link"])) {
					$specific["link"] = get_permalink($value);
				}
				if (isset($options["title"])) {
					$specific["title"] = get_the_title($value);
				}
				if (isset($options["excerpt"])) {
					$specific["excerpt"] = $value;
				}
				$list[] = $specific;
			endforeach;
			//print_r($get[0]);
			return $list;
		}

		static public function ProcessMetaboxListingBindAlternativeImage($get_list_ids, $custom_field, $picture_size = "full", $unique = FALSE) {
			$list = array();
			if (!isset($custom_field)) {
				return false;
			}
			foreach ($get_list_ids[0] as $key => $value) {
				//get this value as the ID of thumbnail
				$img_id_A = get_post_thumbnail_id($value);
				//get this value as the ID of images
				$img_id_B = get_post_meta($value, $custom_field, TRUE);
				$img_id = !empty($img_id_B) ? $img_id_B : $img_id_A;
				$specific = wp_get_attachment_image_src($img_id, $picture_size);
				$specific[3] = get_the_title($value);
				$specific[4] = get_permalink($value);
				// print_r($specific);

				if ($unique) {
					$list[$value] = $specific;
				} else {
					$list[] = $specific;
				}
			}
			//print_r($get[0]);
			return $list;
		}

		static public function ProcessMetaboxListing($get_list_ids, $custom_field = FALSE, $picture_size = "full", $unique = FALSE) {
			$list = array();
			foreach ($get_list_ids[0] as $key => $value) {
				if ($custom_field == FALSE) {
					//get this value as the ID of thumbnail
					$img_id = get_post_thumbnail_id($value);
				} else {
					//get this value as the ID of images
					$img_id = get_post_meta($value, $custom_field, TRUE);
				}
				$specific = wp_get_attachment_image_src($img_id, $picture_size);
				$specific[3] = get_the_title($value);
				$specific[4] = get_permalink($value);
				//print_r($specific);
				/*
				 Array
				 (
				 [0] =&gt; http://wsm.imusictech.com/wp-content/uploads/2013/02/home-artists-01.png
				 [1] =&gt; 125
				 [2] =&gt; 147
				 [3] =&gt; 楊燕
				 [4] =&gt; http://wsm.imusictech.com/wp-content/u
				 )
				 Array
				 (
				 [3] =&gt; 呂珊
				 )
				 */
				if ($unique) {
					$list[$value] = $specific;
				} else {
					$list[] = $specific;
				}
			}
			//print_r($get[0]);
			return $list;
		}

		/**!
		 * HKM
		 * @link get_term_name_single
		 * @license creative common / MIT LICENSE
		 * @author Heskemo
		 *                 -return a list of links that the post is surronding..
		 * @param taxonomy - self explaintory
		 * @param post_ID - the post ID
		 * @version 1.1
		 */
		static public function get_term_name_single($slug, $taxonomy) {
			if (empty($slug)) {
				return "";
			} else {
				$term = get_term_by('slug', $slug, $taxonomy);
				if (is_object($term)) {
					$slug = $term -> slug;
					$name = $term -> name;
					$link = get_term_link($term -> slug, $taxonomy);
				} else {
					$name = $term['name'];
				}
				return $name;
			}
			//array('slug' => $slug, 'name' => $name, 'link' => $link);
		}

	}

}
?>