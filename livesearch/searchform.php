<?php
if (!function_exists("livesearch_get_option")) {
	// plugin not installed or disabled. setup a pseudo function and add some defaults
	function livesearch_get_option($option) {
		$defaults = array(
				"enable_livesearch" => true, // DON'T CHANGE!
				
				// Change the following values to anything you want.
				"default_string" => "type to search...", // This can also be empty
				"default_focus_color" => "#000000",
				"default_blur_color" => "#888888",
				"default_size" => 0
			);
		
		return $defaults[$option];
	}
}
?>
<form <?php echo livesearch_get_option('enable_livesearch') ? 'onsubmit="return liveSearchSubmit()"' : ''; ?> id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
<div>
	<input accesskey="s" type="text"
	id="<?php echo livesearch_get_option('enable_livesearch') ? 'livesearch' : 's'; ?>"
	name="s"
	<?php echo livesearch_get_option('default_size') ? 'size="'.livesearch_get_option('default_size').'"' : ''; ?>
	value="<?php echo $s ? wp_specialchars($s, 1) : livesearch_get_option('default_string');  ?>"
	<?php echo livesearch_get_option('enable_livesearch') ? 'onkeypress="liveSearchStart()"' : ''; ?>
	onblur="<?php echo livesearch_get_option('enable_livesearch') ? "setTimeout('closeResults()',2000);" : "";?> if (this.value == '') {this.value = '<?php echo livesearch_get_option('default_string'); ?>'; this.style.color = '<?php echo livesearch_get_option('default_blur_color'); ?>'; }"
	onfocus="if (this.value == '<?php echo livesearch_get_option('default_string'); ?>') {this.value = '';} this.style.color = '<?php echo livesearch_get_option('default_focus_color'); ?>';"
	style="color: <?php echo $s ? livesearch_get_option('default_focus_color')
	 			  				: livesearch_get_option('default_blur_color'); ?>;"
	/>
<label for="<?php echo livesearch_get_option('enable_livesearch') ? 'livesearch' : 's'; ?>"><input type="submit" id="searchsubmit"
	<?php echo livesearch_get_option('enable_livesearch') ? "style='display: none;'" : ""; ?>
	value="Search" /></label>
<div id="LSResult" style="display: none;"><div id="LSShadow"></div></div>
</div>
</form>
