<?php
/*
Plugin Name: Crop Image
Version: 2.5.d
Description: Enables to Crop Images already uploaded to the gallery, basic functionality.  Tested with v2.5.1, v2.4.6
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=700
Author: Chillexistence
Author URI: http://piwigo.org

Parts of this functions.inc.php were taken from Header Manager Extension and adapted.
*/
if (!defined('CROPIMAGE_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/image.class.php');

/**
 * get full size and thumbnail urls and size for a banner
 * @param: string filename
 */
function get_file_to_crop($file)
{
	if (file_exists(get_root_url().$file))
  {
	    return array(
      'NAME' => $file,
      'PATH' => get_root_url(). $file,
      'SIZE' => getimagesize($file),
      );
  }
  else
  {
    return false;
  }
}

/**
 * get properties of the jCrop window
 * @param: array picture(width, height[, coi])
 * @return: array crop(display_width, display_height, l, r, t, b, coi(x, y))
 */
function get_crop_display($picture)
{
  global $conf;
  
  // find coi
  if (!empty($picture['coi']))
  {
    $picture['coi'] = array(
      'l' => char_to_fraction($picture['coi'][0])*$picture['width'],
      't' => char_to_fraction($picture['coi'][1])*$picture['height'],
      'r' => char_to_fraction($picture['coi'][2])*$picture['width'],
      'b' => char_to_fraction($picture['coi'][3])*$picture['height'],
      );
  }
  else
  {
    $picture['coi'] = array(
      'l' => 0,
      't' => 0,
      'r' => $picture['width'],
      'b' => $picture['height'],
      );
  }
  $crop['coi']['x'] = ($picture['coi']['r']+$picture['coi']['l'])/2;
  $crop['coi']['y'] = ($picture['coi']['b']+$picture['coi']['t'])/2;
  
	$conf['original_resize_maxwidth'] = (isset($conf['original_resize_maxwidth']) and $conf['original_resize_maxwidth'] > 500) ? $conf['original_resize_maxwidth'] : 1000;
  $conf['original_resize_maxheight'] = (isset($conf['original_resize_maxheight']) and $conf['original_resize_maxheight'] > 500) ? $conf['original_resize_maxheight'] : 2000;

    $crop['display_width'] = $picture['width'];
    $crop['display_height'] = $picture['height'];
    
    $adapted_crop_height = round($conf['original_resize_maxheight']*$picture['width']/$conf['original_resize_maxwidth']);
    
    $crop['l'] = 0;
    $crop['r'] = $picture['width'];
    $crop['t'] = max(0, $crop['coi']['y']-$adapted_crop_height/2);
    $crop['b'] = min($crop['display_height'], $crop['t']+$adapted_crop_height);
  
  return $crop;
}

?>