<?php
/*
Plugin Name: Crop Image
Version: auto
Description: Enables to Crop Images already uploaded to the gallery, basic functionality.  Unable to undo the crop once cropped, will have to reload the photo.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=700
Author: Chillexistence
Author URI: http://piwigo.org

Parts of this class.php were taken from Header Manager Extension and adapted.
*/
if (!defined('CROPIMAGE_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH . 'admin/include/image.class.php');

/**
 * class derivated from pwg_image
 */
class crop_image extends pwg_image
{									 				 							
  function cropimage_resize($destination_filepath, $x, $y, $x2, $y2, $width, $height)
  {
    global $conf;
    $starttime = get_moment();

    // width/height
    $source_width  = $this->image->get_width();
    $source_height = $this->image->get_height();

    $resize_dimensions = array(
      'width' => $width,
      'height'=> $height,
      'crop' => array(
        'width' => $width,
        'height' => $height,
        'x' => $x,
        'y' => $y,
        ),
      );
    
    // maybe resizing/croping is useless ?
    if ( $resize_dimensions['crop']['width'] == $source_width and $resize_dimensions['crop']['height'] == $source_height )
    {
      // the image doesn't need any resize! We just copy it to the destination
      copy($this->source_filepath, $destination_filepath);
      return $this->get_resize_result($destination_filepath, $resize_dimensions['width'], $resize_dimensions['height'], $starttime);
    }
		
		$conf['original_resize_quality'] = isset($conf['original_resize_quality']) ? $conf['original_resize_quality'] : 90;
    $this->image->set_compression_quality($conf['original_resize_quality']);
    
    // crop
    $this->image->crop($resize_dimensions['crop']['width'], $resize_dimensions['crop']['height'], $resize_dimensions['crop']['x'], $resize_dimensions['crop']['y']);
		
    // save
    $this->image->write($destination_filepath);

    // everything should be OK if we are here!
    return $this->get_resize_result($destination_filepath, $resize_dimensions['crop']['width'], $resize_dimensions['crop']['height'], $starttime);
  }
  
  protected function get_resize_result($destination_filepath, $width, $height, $time=null)
  {
    return array(
      'source'      => $this->source_filepath,
      'destination' => $destination_filepath,
      'width'       => $width,
      'height'      => $height,
      'size'        => floor(filesize($destination_filepath) / 1024).' KB',
      'time'        => $time ? number_format((get_moment() - $time) * 1000, 2, '.', ' ').' ms' : null,
      'library'     => $this->library,
    );
  }
}

?>