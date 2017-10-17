<?php
/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC.
 * All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 31/05/2010, 00:36
 */

define('NV_SYSTEM', true); 

define('NV_ROOTDIR', pathinfo(str_replace(DIRECTORY_SEPARATOR, '/', __file__), PATHINFO_DIRNAME));

$realpath_mainfile = $set_active_op = '';

require NV_ROOTDIR . '/includes/mainfile.php';

if ($sys_info['allowed_set_time_limit']) {
	set_time_limit(1200);
}
if( NV_CLIENT_IP != '127.0.0.1' )
{
    die( NV_CLIENT_IP );
}

if(is_dir(NV_ROOTDIR . '/Bai-giang')){
	$floder = scandir(NV_ROOTDIR . '/Bai-giang');
	$new_floder = array_shift($floder);
	$new_floder_two = array_shift($floder);
	
	try{
		
		$db->exec("TRUNCATE TABLE `nv4_vi_download_categories`");
		$db->exec("TRUNCATE TABLE `nv4_vi_download_detail`");
		$db->exec("TRUNCATE TABLE `nv4_vi_download`");
		$db->exec("TRUNCATE TABLE `nv4_vi_download_files`");
		global $module_data, $lang_im, $lang_fix;
		$module_data = 'download';
		$lang_im = 'vi';
		$lang_fix = $db_config['prefix'] . '_' . $lang_im;
		$i=1;
		$y = 1;
		foreach($floder as $key => $value){
			
			$string = strstr($value, 'B');
			$db->exec("INSERT INTO " . $lang_fix . "_" . $module_data . "_categories 
			( `parentid`, `title`, `alias`, `description`, `groups_view`, `groups_onlineview`, `groups_download`, `groups_addfile`, 
			`numsubcat`, `subcatid`, `viewcat`, `numlink`, `lev`, `weight` ,`status`) VALUES
			( 0,'". $string ."', '".change_alias($string)."-".$i."', '', 6, 6, 6, 4, 0,'', 'viewcat_list_new', 3, 0,".$i.",1)");			
						
			$count_value[$key+1] = NV_ROOTDIR . '/Bai-giang/'.$value;
			$i++;
		}
		
		foreach($count_value as $key_count => $value_count){
			$list_file = scandir($value_count);
			$new_file = array_shift($list_file);
			$new_file_two = array_shift($list_file);
					
			foreach($list_file as $key_file => $value_file){
				
				if(is_dir($value_count . '/' . $value_file)){
					
					$sql = "INSERT INTO " . $lang_fix . "_" . $module_data . "_categories 
					( `parentid`, `title`, `alias`, `description`, `groups_view`, `groups_onlineview`, `groups_download`, `groups_addfile`, 
					`numsubcat`, `subcatid`, `viewcat`, `numlink`, `lev`, `weight` ,`status`) VALUES
					( ".$key_count.",'". $value_file ."', '".change_alias($value_file)."-".$i."', '', 6, 6, 6, 4, 0,'', 'viewcat_list_new', 3, 0,".$i.",1)";
					
					$newcatid[] = $db->insert_id($sql, '', $data_insert);
					
					$s = implode(",",$newcatid);										

				}else{
					$sqls = "INSERT INTO " . $lang_fix . "_" . $module_data . " 
					( `catid`, `title`, `alias`, `introtext`, `uploadtime`, `updatetime`, `user_id`, `user_name`, `author_name`, 
					`author_email`, `author_url`, `version`, `filesize`, `fileimage`, `status`, `copyright`, `num_fileupload`, `num_linkdirect`, 
					`view_hits`, `download_hits`, `comment_hits`) VALUES
					( ".$key_count.",'".$value_file."','".change_alias($value_file)."-".$key_count."','',".NV_CURRENTTIME.",".NV_CURRENTTIME.",1,'admin','',
					'','','',0,'',1,'',1,0,0,0,0)";
					
					$newid = $db->insert_id($sqls, '', $data_inserts);
					
					$db->exec("INSERT INTO " . $lang_fix . "_" . $module_data . "_detail
					(`id`,`description`, `linkdirect`, `groups_comment`, `groups_view`, `groups_onlineview`, `groups_download`, 
					`rating_detail`) VALUES
					(".$newid.",'','',4,6,6,6,'')");
					
				
					
					$db->exec("INSERT INTO " . $lang_fix . "_" . $module_data . "_files
					( `download_id`, `server_id`, `file_path`, `scorm_path`, `filesize`, `weight`, `status`) VALUES
					(".$newid.", 0, '/download/2017_10/files/". $value_file ."', '', 0,".$y.",1)");
					
					
					
				}
			}
			
			$y++;

			$db->exec("UPDATE nv4_vi_download_categories SET subcatid = '". $s . "' WHERE id = " . $key_count);	
		}
		
		
	}
	catch( PDOException $e )
	{
		die( $e->getMessage( ) );
	}
}
