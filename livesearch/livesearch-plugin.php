<?php
/*
Plugin Name: LiveSearch
Plugin URI: http://www.cneophytou.com/2006/03/26/livesearch/
Description: Includes LiveSearch in your blog. Now Widgetized. Based (almost entirely) on <a href="http://fernando.dubtribe.com/archives/2005/06/01/livesearch-for-wordpress-1512/">LiveSearch</a> code.
Version: 1.4
Author: Constantinos Neophytou
Author URI: http://www.cneophytou.com/

released under the terms of the GNU GPL
*/
global $livesearch_plug_inserted;
$livesearch_plug_inserted = false;

load_plugin_textdomain('livesearch');

function livesearch_wp_head() {
	global $livesearch_plug_inserted;
	// output links to livesearch css, livesearch javascript and define the exact location of results page
	if (livesearch_get_option('enable_livesearch')) {
		$plugin_dir = get_bloginfo('home') . "/wp-content/plugins/livesearch";
		if (file_exists(TEMPLATEPATH."/livesearch.css")) {
			$css_location = get_bloginfo('stylesheet_directory')."/livesearch.css";
		} else {
			$css_location = $plugin_dir . "/livesearch.css";
		}
	?>
	
	<!-- Load Plug for LiveSearch -->
	<link rel="stylesheet" href="<?php echo $css_location; ?>" type="text/css" media="screen" />
	<script type="text/javascript" src="<?php echo $plugin_dir; ?>/livesearch.js.php"></script>
	<!-- End Load Plug for LiveSearch -->
	
	<?php
	}
	$livesearch_plug_inserted = true;
}

function livesearch_wp_foot() {
	global $livesearch_plug_inserted;
	if (!$livesearch_plug_inserted) {
		livesearch_wp_head();
	}
}

// set a Livesearch option in the options table of WordPress
function livesearch_set_option($option_name, $option_value) {
  // first get the existing options in the database
  $livesearch_options = get_option('livesearch_options');
  // set the value
  $livesearch_options[$option_name] = $option_value;
  // write the new options to the database
  update_option('livesearch_options', $livesearch_options);
}

// get an option from the WordPress options database table
// if the option does not exist (yet), the default value is returned
function livesearch_get_option($option_name) {

  // get options from the database
  $livesearch_options = get_option('livesearch_options'); 
  
  if (!$livesearch_options || !array_key_exists($option_name, $livesearch_options)) {
    // no options in database yet, or not this specific option 
    // create default options array
    $livesearch_default_options							= array();
    $livesearch_default_options['enable_livesearch']    = true;
    $livesearch_default_options['default_string']       = __('start typing to search', 'livesearch');
    $livesearch_default_options['default_focus_color']  = "#000000";
    $livesearch_default_options['default_blur_color']   = "#888888";
    $livesearch_default_options['default_size']			= 0;
	
    // add default options to the database (if options already exist, 
    // add_option does nothing
    add_option('livesearch_options', $livesearch_default_options, 
               'Settings for Livesearch plugin');

    // return default option if option is not in the array in the database
    // this can happen if a new option was added to the array in an upgrade
    // and the options haven't been changed/saved to the database yet
    $result = $livesearch_default_options[$option_name];

  } else {
    // option found in database
    $result = $livesearch_options[$option_name];
  }
  
  return $result;
}

// function that is added as an Action to ADMIN_MENU
// it adds an option subpage to the options menu in WordPress administration
function livesearch_admin() {
  if (function_exists('add_options_page')) {
    add_options_page('LiveSearch Options' /* page title */, 
                     'LiveSearch' /* menu title */, 
                     8 /* min. user level */, 
                     basename(__FILE__) /* php file */ , 
                     'livesearch_options' /* function for subpanel */);
  }
}

// displays options subpage to set options for Livesearch and save any
// changes to these options back to the database
function livesearch_options() {
  if (isset($_POST['info_update'])) {
    ?><div class="updated"><p><strong><?php 
    // process submitted form
    $livesearch_options = get_option('livesearch_options');
    $livesearch_options['enable_livesearch']  = ($_POST['enable_livesearch'] == "true" ? true : false);
    $livesearch_options['default_string']     = $_POST['default_string'];
    $livesearch_options['default_focus_color']= $_POST['default_focus_color'];
    $livesearch_options['default_blur_color'] = $_POST['default_blur_color'];
    $livesearch_options['default_size']       = $_POST['default_size'];
    update_option('livesearch_options', $livesearch_options);
    
    _e('Options saved', 'livesearch')
    ?></strong></p></div><?php
	} 
	
	// show options form with current values
	?>
<div class=wrap>
  <form method="post">
    <h2><?php _e('LiveSearch', 'livesearch'); ?></h2>
    <fieldset name="general">
      <legend><?php _e('General settings', 'livesearch') ?></legend>
      <table width="100%" cellspacing="2" cellpadding="5" class="editform">
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Enable Livesearch', 'livesearch') ?></th>
          <td><input type="checkbox" name="enable_livesearch" id="enable_livesearch" value="true" <?php if (livesearch_get_option('enable_livesearch')) echo "checked"; ?> />
            <br /><?php _e('By unchecking this checkbox your search box will work as normal.', 'livesearch'); ?>
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Default searchbox text', 'livesearch') ?></th>
          <td><input name="default_string" type="text" id="default_string" value="<?php echo livesearch_get_option('default_string'); ?>" size="50" />
            <br /><?php _e('Enter the default text that will be shown in the searchbox.', 'livesearch'); ?>
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Default searchbox size', 'livesearch') ?></th>
          <td><input name="default_size" type="text" id="default_size" value="<?php echo livesearch_get_option('default_size'); ?>" size="50" />
            <br /><?php _e("Enter the desired size of the searchbox. Has to be a numeric value. Enter '0' to leave the default browser size (i.e. not specify a search box size).", 'livesearch'); ?>
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Default Focus Color', 'livesearch') ?></th>
          <td><input name="default_focus_color" type="text" id="default_focus_color" value="<?php echo livesearch_get_option('default_focus_color'); ?>" size="50" />
            <br /><?php _e('Enter the default color the searchbox will have when it contains text other than the default.', 'livesearch'); ?>
          </td>
        </tr>
        <tr>
          <th nowrap valign="top" width="33%"><?php _e('Default Blur Color', 'livesearch') ?></th>
          <td><input name="default_blur_color" type="text" id="default_blur_color" value="<?php echo livesearch_get_option('default_blur_color'); ?>" size="50" />
            <br /><?php _e('Enter the default color the searchbox will have when it contains the default text.', 'livesearch'); ?>
          </td>
        </tr>
      </table>
    </fieldset>
    
    <div class="submit">
      <input type="submit" name="info_update" value="<?php _e('Update options', 'livesearch') ?>" />
	  </div>
  </form>
</div><?php
}

// load texts for localization
load_plugin_textdomain('livesearch');

// add Cron Mail Options page to the Option menu
add_action('admin_menu', 'livesearch_admin');

// add actions if enabled
if (livesearch_get_option('enable_livesearch')) {
	add_action('wp_head',   'livesearch_wp_head');
	add_action('wp_footer',   'livesearch_wp_foot');
}

function widget_livesearch_init() {
	if (!function_exists('register_sidebar_widget'))
		return;
	
	function widget_livesearch($args) {
		extract($args);
		$options = get_option('widget_livesearch');
		$title = empty($options['title']) ? __('Search:') : $options['title'];
		$display = empty($options['display']) ? false : $options['display'];
		
		echo $display ? $before_widget . $before_title . $title . $after_title : '';
		//$searchFile = TEMPLATEPATH . '/searchform.php';
		//if (file_exists($searchFile))
		//	include ($searchFile);
		//else
		include ("searchform.php");
		echo $display ? $after_widget : '';
	}
	
	
	function widget_livesearch_control() {
		$options = $newoptions = get_option('widget_livesearch');
		if ( $_POST["livesearch-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["livesearch-title"]));
			$newoptions['display'] = $_POST["livesearch-display"];
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_livesearch', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$display = $options['display'] ? true : false;
		?>
			<p><label for="livesearch-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="livesearch-title" name="livesearch-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p style="text-align:right;"><label for="livesearch-display"><?php _e('Encase in widget tags:', 'livesearch'); ?><input style="width: 200px;" id="livesearch-display" name="livesearch-display" type="checkbox" value="true" <?php echo $display ? "checked" : ''; ?> /></label></p>
			<input type="hidden" id="livesearch-submit" name="livesearch-submit" value="1" />
		<?php
	}
	
	register_sidebar_widget(__('Search'), 'widget_livesearch');
	register_widget_control(__('Search'), 'widget_livesearch_control', 400, 90);
}

add_action('plugins_loaded', 'widget_livesearch_init');

// Taken from http://unfusion.kunsan.ac.kr/word/archive/779
function live_search_rewrite($wp_rewrite) {
    $rules = array(
        'wp-content/plugins/livesearch/livesearch.php' => '/',
    );
    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
// Hook in.
add_filter('generate_rewrite_rules', 'live_search_rewrite');

?>