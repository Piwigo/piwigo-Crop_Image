<?php
/*
Plugin Name: Crop Image
Version: 2.5.d
Description: Enables to Crop Images already uploaded to the gallery, basic functionality.  Tested with v2.5.1, v2.4.6
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=700
Author: Chillexistence
Author URI: http://piwigo.org
*/

if( !defined("CROPIMAGE_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
include_once(PHPWG_ROOT_PATH.'admin/include/image.class.php');
include_once(dirname(__FILE__).'/functions.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Basic checks                                                          |
// +-----------------------------------------------------------------------+

$_GET['image_id'] = isset($_GET['tab']) ? $_GET['tab'] : '';

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];

// +-----------------------------------------------------------------------+
// | Process form                                                          |
// +-----------------------------------------------------------------------+

load_language('plugin.lang', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');

// apply crop and redirect
if (isset($_POST['submit_crop']))
{
  include_once(dirname(__FILE__).'/crop.class.php');
  
  $CropImg = get_file_to_crop($_POST['picture_file']);
	$img = new crop_image($CropImg['PATH']);
  list($width, $height) = getimagesize($CropImg['PATH']);
  $img->cropimage_resize(
    $CropImg['PATH'],
    $_POST['x'],
    $_POST['y'], 
    $_POST['x2'],
    $_POST['y2'],
		$_POST['w'],
		$_POST['h']
    );
  $img->destroy();

  $has_md5sum_fs = pwg_db_num_rows(pwg_query('SHOW COLUMNS FROM `'.IMAGES_TABLE.'` LIKE "md5sum_fs" '));
  
	$query='
SELECT
    id,
    path,
    '.($has_md5sum_fs ? 'md5sum_fs,' : '').'
    representative_ext
  FROM '.IMAGES_TABLE.'
  WHERE id = '.(int)$_POST['image_id'].'
;';
  $row = pwg_db_fetch_assoc(pwg_query($query));
  if ($row == null)
  {
    return false;
  }
	
	sync_metadata(array($row['id']));

  $datas = array('coi' => null);

  $activity_details = array('action' => 'crop');

  if ($has_md5sum_fs)
  {
    $md5sum = md5_file($row['path']);
    $datas['md5sum_fs'] = $md5sum;

    $activity_details['md5sum_fs_previous'] = $row['md5sum_fs'];
    $activity_details['md5sum_fs_new'] = $md5sum;
  }

  single_update(IMAGES_TABLE, $datas, array('id' => $row['id']));

  pwg_activity('photo', $row['id'], 'edit', $activity_details);
		
  delete_element_derivatives($row); 
  
  $_SESSION['page_infos'][] = l10n('Photo Cropped'); 
  redirect($admin_photo_base_url);
}

// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

$tabsheet = new tabsheet();
$tabsheet->set_id('photo');
$tabsheet->select('crop');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+


$template->set_filenames(
  array(
    'plugin_admin_content' => dirname(__FILE__).'/crop_image.tpl'
    )
  );

// get picture from gallery
if (isset($_GET['image_id']))
{
  $query = '
SELECT
    file,
    path, 
    coi, 
    width, 
    height,
		name
  FROM '.IMAGES_TABLE.'
  WHERE id = '. (int)@$_GET['image_id'] .'
;';
  $result = pwg_query($query);
  
  if (!pwg_db_num_rows($result))
  {
    array_push($page['errors'], l10n('Unknown Photo ID'));
  }
  else
  {
    $picture = pwg_db_fetch_assoc(pwg_query($query));
    $picture['filename'] = basename($picture['path']);
    
		$picture['banner_src'] = PHPWG_ROOT_PATH . $picture['path'];
  
		list($RatioWidth, $RatioHeight) = getimagesize($picture['path']);
	
    $template->assign(array(
  		'TITLE' => render_element_name($picture),
  		'IN_CROP' => true,
      'picture' => $picture,
      'crop' => get_crop_display($picture),
  		'image_id' => (int)@$_GET['image_id'],
			'image_ratio' => $RatioWidth/$RatioHeight,
      'random_avoid_cache_key' => generate_key(10),
      'ADMIN_PAGE_TITLE' => l10n('Edit photo').' <span class="image-id">#'.$_GET['image_id'].'</span>',
      ));
  }
}

// +-----------------------------------------------------------------------+
// | sending html code                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>
