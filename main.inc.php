<?php
/*
Plugin Name: Crop Image
Version: auto
Description: Enables to Crop Images already uploaded to the gallery, basic functionality.  Tested with v2.5.1, v2.4.6
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=700
Author: Chillexistence
Author URI: http://piwigo.org
*/
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');
define('CROPIMAGE_PATH',    PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)));
defined('CROPIMAGE_ID') or define('CROPIMAGE_ID', basename(dirname(__FILE__)));
define('CROPIMAGE_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . CROPIMAGE_ID);


add_event_handler('init', 'cropImage_init' );
add_event_handler('tabsheet_before_select','crop_image_add_tab', 50, 2);

function crop_image_add_tab($sheets, $id)
{  
	global $lang;
	load_language('plugin.lang', dirname(__FILE__).'/');
	 
	$image_id = isset($_GET['image_id'])? $_GET['image_id'] : '';
 
	if ($id == 'photo')
  {
    $sheets['crop'] = array(
      'caption' => l10n('Crop'),
      'url' => CROPIMAGE_ADMIN.'-'.$image_id,
      );
  }
  
  return $sheets;
}
		
function cropImage_init()
{
  global $conf, $pwg_loaded_plugins;		
  if (!defined('PWG_HELP') and !defined('IN_ADMIN') and is_admin())
  {
		// Add an event handler for a prefilter on picture.php pages
		add_event_handler('loc_begin_picture', 'cropImage_set_prefilter_add_to_pic_info', 55 );
	}
}	
	
////////////////////////////////////////////////////////////////////////////////
// Add the prefilter for the Crop Link on the template on picture.php
////////////////////////////////////////////////////////////////////////////////
function cropImage_set_prefilter_add_to_pic_info()
{
	global $template, $conf, $user, $page, $lang;
	load_language('plugin.lang', dirname(__FILE__).'/');
	
	$url_admin =
    CROPIMAGE_ADMIN.'-'.$page['image_id']
    .(isset($page['category']) ? '&amp;cat_id='.$page['category']['id'] : '')
    ;
	$template->set_prefilter('picture', 'cropImage_add_button');
	
	$template->func_combine_css(array('path' => CROPIMAGE_PATH.'/css/cropimage_toolbar_buttons.css'));
	
	//Get the Theme to display the correct icon style
	if ( in_array($user['theme'], array('Sylvia','sylvia')) )
	{
	 	 $ButtonTheme = "sylvia";
	}
	elseif ( in_array($user['theme'], array('elegant')) )
	{
	 	 $ButtonTheme = "elegant";
	}
	elseif ( in_array($user['theme'], array('clear')) )
	{
	 	 $ButtonTheme = "clear";
	}
	elseif ( in_array($user['theme'], array('dark')) )
	{
	 	 $ButtonTheme = "dark";
	}
	else
	{
	 	 $ButtonTheme = "clear";
	}
	
	$template->assign(
    array(
      'U_CROPIMAGE' => $url_admin,
			'U_CROPIMAGE_THEME' => $ButtonTheme,
      )
    );
}	
////////////////////////////////////////////////////////////////////////////////
// Insert the template for the Crop Link on picture.php
////////////////////////////////////////////////////////////////////////////////
function cropImage_add_button($content)
{
	// Add the information after imageInfoTable so before everything else like author and date.
	$search = '{strip}{if isset($U_CADDIE)}{*caddie management BEGIN*}';
	
	$replacement = '
	<a class="pwg-state-default pwg-button" href="{$U_CROPIMAGE}" title="{\'Crop Photo\'|@translate}" rel="nofollow">
    	 <span class="ci-icon ci-icon-cropimage-{$U_CROPIMAGE_THEME}"> </span><span class="pwg-button-text">{\'Crop Photo\'|@translate}</span>
  </a>
	' . $search;

	return str_replace($search, $replacement, $content);
}
	
?>
