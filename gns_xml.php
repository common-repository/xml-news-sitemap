<?php

/*
Plugin Name: XML News Sitemap
Author: Justin Norton
Author URI: https://www.jnorton.co.uk
Plugin URI: https://www.jnorton.co.uk/wordpress-google-news-xml-sitemap-plugin
Description: This plugin provides a Google News XML Sitemap for WordPress that lists the most recent 1000 articles published in the past two days that are marked to be included in an auto-generated Google Sitemap News XML file in accordance with the standards detailed on the Creating a Google News sitemap page here: https://support.google.com/news/publisher/answer/74288?hl=en-GB
Version: 1.2.5
*/

if (!class_exists('Gns_xml')) {

	register_activation_hook( __FILE__, array( 'Gns_xml', 'saveDefaultSettings' ) );
	register_deactivation_hook(__FILE__, array('Gns_xml', 'removeDefaultSettings'));

	class Gns_xml
	{

		var $version = '1.2.5';
		var $settings = array();
		public static $sitemap_url = '';
		public static $plugin_path = '';
		public static $genres = array();
		public static $access = array();

		function __construct()
		{
			//register_activation_hook(__FILE__, array('Gns_xml', 'saveDefaultSettings'));

			$this->_settings = $this->getSettings();
			$this->_plugin_path = plugin_dir_path( __FILE__ );
			if(isset($this->_settings['gns_xml_url'])){
				$this->_sitemap_url = $this->_settings['gns_xml_url'];
			} else {
				$this->_sitemap_url = get_site_url()."/sitemap-news.xml";
			}
			$this->_access = array(
				'na' => 'Not Applicable',
				'Subscription' => 'Subscription',
				'Registration' => 'Registration'
			);

			add_action('admin_init', array(&$this, 'checkForSettingsSave'));
			add_action('admin_menu', array(&$this, 'addAdministrativeElements'));
			add_action('save_post', array(&$this, 'dbdGsnSavePostData') );
			add_action('add_meta_boxes', array(&$this, 'addMetaBoxes'));
			add_filter('template_redirect', array(&$this, 'renderXML'),0,0);
			add_filter('plugin_action_links_'.plugin_basename( __FILE__ ), array(&$this, 'add_action_links'));

		}

		//FEED TEMPLATE
		function renderXML()
		{
			global $wp, $wp_query;
			$current_url = home_url($wp->request);
			if($current_url == $this->_sitemap_url){
				if ($wp_query->is_404) {
		       		$wp_query->is_404 = false;
		    	}
				include($this->_plugin_path.'lib/template/gns_xml_output.php');
				exit;
			}
		}

		function add_action_links ( $links ) {
		 $plugin_links = array(
		 '<a href="' . admin_url( 'options-general.php?page=gns-xml' ) . '">Settings</a>',
		 );
		return array_merge( $links, $plugin_links );
		}

		//Remove defaults
		public static function removeDefaultSettings(){
			delete_option( 'gns_xml_options' );
		}

		//Save defaults
		public static function saveDefaultSettings()
		{
			$settings = get_option('gns_xml_options', array());
			if ( empty($settings)) {
				$settings = array();
				$settings['gns_xml_custom_post_types'] = array("post");
				$settings['gns_xml_url'] = (string) get_site_url() . "/sitemap-news.xml";
				$settings['gns_xml_pubname'] = (string) get_bloginfo( 'name' );
				$settings['gns_xml_genres'] = serialize('');
				$settings['gns_xml_publanguage'] = (string) 'en';
				$settings['gns_xml_pubaccess'] = (string) 'na';
				$settings['gns_xml_cats'] = array();
				update_option('gns_xml_options', $settings);
			}
		}

		//ADMIN SETTINGS
		function addAdministrativeElements()
		{
			add_options_page(__('Google News Sitemap XML'), __('Google News Sitemap XML'), 'manage_options', 'gns-xml', array(&$this, 'displaySettingsPage'));
		}

		function checkForSettingsSave()
		{

			if (isset($_POST['gns_xml_settings']) && current_user_can('manage_options') && check_admin_referer('gns_xml_settings')) {
				$settings = $this->getSettings();
				if(isset($_POST['gns_xml_custom_post_types']) && !array($_POST['gns_xml_custom_post_types'])){
					 return new WP_Error( 'broke', __( "Oops you can't post that!" ) );
				} else {
					if(isset($_POST['gns_xml_custom_post_types'])){
						$settings['gns_xml_custom_post_types'] = array();
						foreach($_POST["gns_xml_custom_post_types"] as $option){
							$settings['gns_xml_custom_post_types'][] = sanitize_text_field($option);
						}
					}
					if(isset($_POST["post_category"]) && is_array($_POST["post_category"])){
						$settings['gns_xml_cats'] = array();
						foreach($_POST["post_category"] as $option){
							$settings['gns_xml_cats'][] = sanitize_text_field($option);
						}
					}
					if(isset($_POST["gns_xml_pubaccess"]) && is_array($_POST["gns_xml_pubaccess"])){
						$settings['gns_xml_pubaccess'] = sanitize_text_field($_POST["gns_xml_pubaccess"]);
					}
					if(isset($_POST["gns_xml_genres"])){
							$settings['gns_xml_genres'] = serialize(sanitize_text_field($_POST["gns_xml_genres"]));
					}
				}
				if(!is_string($_POST['gns_xml_url']) || !is_string($_POST['gns_xml_pubname']) || !is_string($_POST['gns_xml_publanguage'])){
					return new WP_Error( 'broke', __( "Oops you can't post that!" ) );
				} else {
					$settings['gns_xml_url'] = sanitize_text_field($_POST['gns_xml_url']);
					$settings['gns_xml_pubname'] = sanitize_text_field($_POST['gns_xml_pubname']);
					$settings['gns_xml_publanguage'] = sanitize_text_field($_POST['gns_xml_publanguage']);
				}

				$this->saveSettings($settings);
				wp_redirect(admin_url('options-general.php?page=gns-xml&updated=1'));
			}
		}

		function saveFailed($message){

		}

		function displaySettingsPage()
		{
			include $this->_plugin_path.'lib/admin/settings.php';
		}

		function getSettings()
		{
			if (empty($this->_settings)) {
				$this->_settings = get_option('gns_xml_options', array());
			}
			return $this->_settings;
		}


		function saveSettings($settings)
		{
			if (!is_array($settings)) {
				return;
			}
			$this->_settings = $settings;
			update_option('gns_xml_options', $this->_settings);
		}

		//POST EDIT SCREEN

		/* Adds a box to the main column on the Post and Page edit screens */
		function addMetaBoxes()
		{
			$settings = $this->getSettings();
			$post_types = $settings['gns_xml_custom_post_types'];

			foreach ($post_types as $post_type) {

				add_meta_box(
					'post_edit_box_id',
					'News Sitemap',
					array( $this, 'postEditBox' ),
					$post_type,
					'side',
					'high'
				);

			}

		}

		/* Prints the box content */
		function postEditBox($post)
		{
			global $post;
			$settings = $this->getSettings();
			// Use nonce for verification
			wp_nonce_field( 'gns_xml_field_nonce', 'gns_xml_noncename' );

			// Get saved value, if none exists, "default" is selected
			$gns_xml_include = get_post_meta( $post->ID, 'gns_xml_include', true);
			$gns_xml_pubaccess = get_post_meta( $post->ID, 'gns_xml_pubaccess', true);
			$gns_xml_genres = unserialize(get_post_meta( $post->ID, 'gns_xml_genres', true));
			if(empty($gns_xml_genres)){
				$gns_xml_genres = unserialize($settings['gns_xml_genres']);
			}
			$gns_xml_publanguage = get_post_meta( $post->ID, 'gns_xml_publanguage', true);

			if($gns_xml_publanguage == ""){
				$gns_xml_publanguage = $settings['gns_xml_publanguage'];
			}

			$include_checked = '';
			$exclude_checked = '';

			if(isset($this->_settings['gns_xml_cats']) && in_category( $this->_settings['gns_xml_cats'], $post ) && $gns_xml_include == ""){
				$include_checked = "checked";
			} elseif($gns_xml_include == "include"){
				$include_checked = "checked";
			} elseif($gns_xml_include == "exclude"){
				$exclude_checked = "checked";
			}

			$output = "";
			$output .= '<p class="label"><strong>'.__('Include / Exclude').'</strong></p>';
			$output .= '<input type="radio" name="gns_xml_include" value="include" '.$include_checked.' />';
			$output .= '<label for="gns_xml_include"> Include</label>';
			$output .= '<br />';
			$output .= '<input type="radio" name="gns_xml_include" value="exclude" '.$exclude_checked.' />';
			$output .= '<label for="gns_xml_include"> Exclude</label>';
			$output .= '<br />';
			$output .= '<p class="label"><strong>'.__('Access').'</strong></p>';
			$output .= '<select class="index-list widefat" name="gns_xml_pubaccess">';
			foreach($this->_access as $key => $value){
				if($gns_xml_pubaccess != "" && $key == $gns_xml_pubaccess){
					$selected = 'selected';
				} elseif ($gns_xml_pubaccess == "" && $key == $settings['gns_xml_pubaccess']) {
					$selected = 'selected';
				} else {
					$selected = '';
				}
				$output .= '<option value="'.$key.'" '.$selected.'>'. __($value).'</option>';
			}
			$output .= '</select>';
			$output .= '<br />';
			$output .= '<p class="label"><strong>'.__('Genres').'</strong></p>';
			$output .= '<input type="text" name="gns_xml_genres" value="'.$gns_xml_genres.'"/>';
			$output .= '<p class="label"><label for="gns_xml_publanguage"><strong>'.__('Language').'</strong></label></p>';
			$output .= '<input type="text" name="gns_xml_publanguage" value="'.$gns_xml_publanguage.'"/>';
			echo $output;


		}

		/* When the post is saved, saves our custom data */
		function dbdGsnSavePostData($post_id)
		{
			// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return;


			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if ( !isset($_POST['gns_xml_noncename']) || isset($_POST['gns_xml_noncename']) && !wp_verify_nonce( $_POST['gns_xml_noncename'], 'gns_xml_field_nonce' ) ) {
				return;
			}

			if ( isset($_POST['gns_xml_include']) && $_POST['gns_xml_include'] != "" ) {
				update_post_meta( $post_id, 'gns_xml_include', sanitize_text_field($_POST['gns_xml_include']) );
				update_post_meta( $post_id, 'gns_xml_pubaccess', sanitize_text_field($_POST['gns_xml_pubaccess']) );
				update_post_meta( $post_id, 'gns_xml_genres', serialize(sanitize_text_field($_POST['gns_xml_genres']) ));
				update_post_meta( $post_id, 'gns_xml_publanguage', sanitize_text_field($_POST['gns_xml_publanguage']) );

			}
		}

	}

	add_action('init', 'GnsXmlStart');
	function GnsXmlStart()
	{
		$Gns_xml = new Gns_xml();
	}

}

?>