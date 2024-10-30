<?php 
/*
Plugin Name: LH HTML Cleaner
Plugin URI: https://lhero.org/portfolio/lh-html-cleaner/
Description: Removes blacklisted tags and attributes from the content of posts/pages/custom post types on save.
Author: Peter Shaw
Version: 1.33
Author URI: https://shawfactor.com
*/
if (!class_exists('LH_html_cleaner_plugin')) {


class LH_html_cleaner_plugin {

var $opt_name = "lh_html_cleaner-options";
var $hidden_field_name = 'lh_html_cleaner-submit_hidden';
var $namespace = 'lh_html_cleaner';
var $options;
var $filename;
var $path = 'lh-html-cleaner/lh-html-cleaner.php';
var $blacklisted_tags_field_name = "blacklisted_tags_field";
var $blacklisted_attributes_field_name = "blacklisted_attributes_field";

private function is_this_plugin_network_activated(){

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

if ( is_plugin_active_for_network( $this->path ) ) {
    // Plugin is activated

return true;

} else  {


return false;


}

}


private function array_fix( $array )    {
        return array_filter(array_map( 'trim', $array ));

}

private function removeElementsByTagNames($tagNames, $document) {
foreach($tagNames as $tagName ){
  $nodeList = $document->getElementsByTagName($tagName);
  for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0; ) {
    $node = $nodeList->item($nodeIdx);
    $node->parentNode->removeChild($node);
  }
}
}


private function removeAttributeByAttributeNames($attributeNames, $document) {
foreach($attributeNames as $attributeName ){
foreach($document->getElementsByTagName('*') as $element ){
if ($element->getAttribute($attributeName)){
$element->removeAttribute($attributeName);
}
}
}
}

public function plugin_menu() {
add_options_page('LH HTML Cleaner', 'HTML Cleaner', 'manage_options', $this->filename, array($this,"plugin_options"));

}

public function network_plugin_menu() {
add_submenu_page('settings.php', 'LH HTML Cleaner', 'HTML Cleaner', 'manage_options', $this->filename, array($this,"plugin_options"));


}


function plugin_options() {

if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}


   
 // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'

if( isset($_POST[  $this->hidden_field_name ]) && $_POST[  $this->hidden_field_name ] == 'Y' ) {



$blacklisted_tags_pieces = explode(",", sanitize_text_field($_POST[ $this->blacklisted_tags_field_name ]));

if (is_array($blacklisted_tags_pieces)){

$options[ $this->blacklisted_tags_field_name ] = $this->array_fix($blacklisted_tags_pieces);

}

$blacklisted_attributes_pieces = explode(",", sanitize_text_field($_POST[ $this->blacklisted_attributes_field_name ]));

if (is_array($blacklisted_attributes_pieces)){

$options[ $this->blacklisted_attributes_field_name ] = $this->array_fix($blacklisted_attributes_pieces);

}

if (update_site_option( $this->opt_name, $options )){


$this->options = get_site_option($this->opt_name);

?>
<div class="updated"><p><strong><?php _e('HTML settings saved', $this->namespace ); ?></strong></p></div>
<?php


}



} 

     // settings form

// Now display the settings editing screen

include ('partials/option-settings.php');







}

public function sanitize_content( $content ) {
$doc = new DOMDocument();

// load the HTML into the DomDocument object (this would be your source HTML)
$doc->loadHTML("<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"></head><body>".stripslashes($content)."</body></html>");

// Remove blacklisted elements
$this->removeElementsByTagNames($this->options[ $this->blacklisted_tags_field_name ], $doc);
// Remove blacklisted attributes
$this->removeAttributeByAttributeNames($this->options[ $this->blacklisted_attributes_field_name ], $doc);

// return cleaned html

$content = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $doc->saveHtml());


$content = str_replace("<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">", "", $content);

return $content;

}





// add a settings link next to deactive / edit
public function add_settings_link( $links, $file ) {

	if( $file == $this->filename ){

if ($this->is_this_plugin_network_activated()){

$links[] = '<a href="'.  network_admin_url( 'settings.php?page=' ).$this->filename.'">Settings</a>';


} else {

		$links[] = '<a href="'. admin_url( 'options-general.php?page=' ).$this->filename.'">Settings</a>';

}
	}
	return $links;
}



function __construct() {

$this->options = get_site_option($this->opt_name);
$this->filename = plugin_basename( __FILE__ );

if ( $this->is_this_plugin_network_activated("lh-html-cleaner/lh-html-cleaner.php") ) {
add_action('network_admin_menu', array($this,"network_plugin_menu"));
} else {
add_action('admin_menu', array($this,"plugin_menu"));
}

add_filter('content_save_pre' , array($this,"sanitize_content"));
add_filter('plugin_action_links', array($this,"add_settings_link"), 10, 2);
add_filter('plugin_action_links_'.plugin_basename( __FILE__ ), array($this,"add_settings_link"), 10, 2);
add_filter('network_admin_plugin_action_links_'.plugin_basename( __FILE__ ), array($this,"add_settings_link"), 10, 2);

}

}


$lh_html_cleaner = new LH_html_cleaner_plugin();

}


?>