<?php
/*
Plugin Name: WP Simple Meta Tags
Description: Allows you to set meta tags on page/post.
Version: 1.0
Author: Kumbhdesign Inc.
Author URI: http://kumbhdesign.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!class_exists("wp_simple_meta_tags")) {
	class wp_simple_meta_tags{
	
		function __construct() {
            add_action("save_post", array(&$this, 'savemetadata'));
    		add_action("wp_head", array(&$this, 'displayonfrontend'),0);
    		add_action('admin_init', array(&$this, 'regmetasettings'));
    		add_action('add_meta_boxes', array(&$this, 'addmetametaboxes'));
            add_action('admin_menu', array(&$this, 'addadminpages'));
		}

		function savemetadata($post_id) {

            $wpDesc = sanitize_meta(isset($_POST['wpmetadescription']) ? $_POST['wpmetadescription'] : '');
            $wpmKey = sanitize_meta(isset($_POST['wpmetakeywords']) ? $_POST['wpmetakeywords'] : '');

			update_post_meta($post_id, '_wp_m_description', $wpDesc);
			update_post_meta($post_id, '_wp_m_keywords', $wpmKey);
		}

		function displayonfrontend(){
			global $post;
				if(is_page() || is_home()){
					if(get_option('use_pages_meta_data') == 'on'){
						$isImplemeted = true;
						$meta_description = (get_post_meta($post->ID, '_wp_m_description', true) != '') ? get_post_meta($post->ID, '_wp_m_description', true) : get_option('page_meta_description');
						$meta_keywords = (get_post_meta($post->ID, '_wp_m_keywords', true) != '') ? get_post_meta($post->ID, '_wp_m_keywords', true) : get_option('page_meta_keywords');
					}
				}

				if(is_single()){
					if(get_option('use_posts_meta_data') == 'on'){
						$isImplemeted = true;	
						$meta_description = (get_post_meta($post->ID, '_wp_m_description', true) != '') ? get_post_meta($post->ID, '_wp_m_description', true) : get_option('post_meta_description');
						$meta_keywords = (get_post_meta($post->ID, '_wp_m_keywords', true) != '') ? get_post_meta($post->ID, '_wp_m_keywords', true) : get_option('post_meta_keywords');
					}
				}

				if($isImplemeted){
					echo '<meta name="description" content="'. esc_attr($this->returnFormat($meta_description)) .'" />' . "\n";
					echo '<meta name="keywords" content="'. esc_attr($this->returnFormat($meta_keywords)) .'" />' . "\n";
				}
			}

        function returnFormat($text){
            return htmlentities(stripslashes($text), ENT_COMPAT, "UTF-8");
        }

        function wp_create_wonder_form(){
    		global $post;
    		?>
    		<input type="hidden" id="wpsbmt" name="wpsbmt" value="1" />

            <p>
                <label for="wpmetadescription">Meta Description</label><br />
                <input placeholder="Description" type="text" id="wpmetadescription" name="wpmetadescription" style="width: 100%" value="<?php echo get_post_meta($post->ID, '_wp_m_description', true); ?>" />
            </p>

            <p>
                <label for="wpmetakeywords">Meta Keywords</label><br />
                <input placeholder="Keyword" type="text" id="wpmetakeywords" name="wpmetakeywords" style="width: 100%" value="<?php echo get_post_meta($post->ID, '_wp_m_keywords', true); ?>" />
            </p>

    		<?php
    	}


        function addadminpages(){
            add_menu_page('Meta tag defaults', 'WP Simple Meta Tags', 'manage_options', 'meta_tags',  array(&$this, 'renderadminpage'));
        }

		function addmetametaboxes() {
			add_meta_box( 'MetaTagsPlugin', 'WP Simple Meta Tags', array(&$this, 'wp_create_wonder_form'), 'page', 'advanced', 'high' );
			add_meta_box( 'MetaTagsPlugin', 'WP Simple Meta Tags', array(&$this, 'wp_create_wonder_form'), 'post', 'advanced', 'high' );

		}

    	/**
    	 * registerMetaSettings
    	 *
    	 * Run when the plugin is first installed.  It adds options into the wp-options
    	 */
    	function regmetasettings()
    	{
    		register_setting( 'meta-tag-settings', 'page_meta_keywords' );
    		register_setting( 'meta-tag-settings', 'page_meta_description' );

    		register_setting( 'meta-tag-settings', 'post_meta_keywords' );
    		register_setting( 'meta-tag-settings', 'post_meta_description' );

    		register_setting( 'meta-tag-settings', 'use_pages_meta_data' );
    		register_setting( 'meta-tag-settings', 'use_posts_meta_data' );


			if(get_option('meta_description') != ''){
    			update_option('page_meta_description', get_option('meta_description'));
    			update_option('post_meta_description', get_option('meta_description'));
    			update_option('meta_description', '');
    		}
    		if(get_option('meta_keywords') != ''){
    			update_option('page_meta_keywords', get_option('meta_keywords'));
    			update_option('post_meta_keywords', get_option('meta_keywords'));
    			update_option('meta_keywords', '');
    		}
    	}


    	/**
    	 * renderadminPage()
    	 *
    	 * Paints the tag_options page
    	 */
    	function renderadminpage(){
    		?>
    		
    		<div class="wrap">
        		<h1>WP Simple Meta Tag</h1>
        		<p>Here you can decide on what sections of the site to use this plugin on.</p>
        		<form method="post" action="options.php">
        			<?php settings_fields( 'meta-tag-settings' ); ?>
                    <h2>Pages</h2>
                    <hr>
        			<table class="form-table">
                        <tr valign="top">
                            <th><label for="use_pages_meta_data">Use plugin on pages</label></th>
        					<td colspan="2"><label><input type="checkbox" name="use_pages_meta_data" id="use_pages_meta_data" <?php if(get_option("use_pages_meta_data") == "on"){ echo 'checked="checked"'; } ?> /> Tick to use</label></td>
        				</tr>
                    </table>
                    <h2>Posts</h2>
                    <hr>
                    <table class="form-table">
                        <tr valign="top">
                            <th><label for="use_posts_meta_data">Use plugin on posts</label></th>
        					<td colspan="2"><label><input type="checkbox" name="use_posts_meta_data" id="use_posts_meta_data" <?php if(get_option("use_posts_meta_data") == "on"){ echo 'checked="checked"'; } ?> /> Tick to use</label></td>
        				</tr>
        			</table>
                    <hr>
        			<p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        			</p>
        		</form>
    		</div>
            <?php
        }
	}

	//initialize the class to a variable
	$wp_meta_var = new wp_simple_meta_tags();


}
?>
