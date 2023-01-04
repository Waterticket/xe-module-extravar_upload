<?php
class Extravar_uploadController extends Extravar_upload
{
	public function triggerBeforeDisplay(&$output)
	{
		if(Context::get('act') == 'dispBoardWrite' || Context::get('act') == 'dispHotosubmallProductCreate' || Context::get('act') == 'dispHotosubmallProductEdit')
		{
			$module_info = Context::get('module_info');
			$module_srl = $module_info->module_srl;
			if ($module_srl == 112) $module_srl = 135;

			$args = new stdClass;
			$args->target_module_srl = $module_srl;
			$out = executeQueryArray('extravar_upload.getReginfoTargetSrl', $args);

			foreach($out->data as $config)
			{
				if($config->use == 'N') return;

				$js = array('./modules/extravar_upload/tpl/js/upload.js', 'body', '', 100010);
				$css = './modules/extravar_upload/tpl/css/upload.css';
				Context::loadFile($js);
				Context::addCssFile($css);

				$target_extra = $config->target_extra;
				$limit_count = $config->limit_count;
				$limit_size = $config->limit_size;
				$img_size_width = $config->img_size_width;
				$img_size_height = $config->img_size_height;

				$temp_output = preg_replace_callback('/<select name="extra_vars'.$target_extra.'".+?<\/select>/is', function($input) use($target_extra,$limit_count,$limit_size,$img_size_width,$img_size_height){
					return $this->replaceExtraVar($input,$target_extra,$limit_count,$limit_size,$img_size_width,$img_size_height);
					}, $output);

				if($temp_output)
				{
					$output = $temp_output;
				}
				unset($temp_output);
			}
		}
		if((Context::get('module') != 'admin') && (!Context::get('act') || Context::get('act') == 'dispBoardContent') && Context::get('document_srl'))
		{
			$module_info = Context::get('module_info');
			$module_srl = $module_info->module_srl;

			$oDocumentModel = getModel('document');
			$ExtraKeys = $oDocumentModel->getExtraKeys($module_srl);

			$args = new stdClass;
			$args->target_module_srl = $module_srl;
			$out = executeQueryArray('extravar_upload.getReginfoTargetSrl', $args);

			foreach($out->data as $config)
			{
				if($config->use == 'N') return;
				$read_css = './modules/extravar_upload/tpl/css/read.css';
				Context::addCssFile($read_css);

				$target_extra = $config->target_extra;
				$img_size_width = $config->img_size_width;
				if(!$img_size_width)$img_size_width='auto';
				$img_size_height = $config->img_size_height;
				if(!$img_size_height)$img_size_height='auto';

				$idx = $config->target_extra;

				foreach($ExtraKeys as $extra)
				{
					if($idx == $extra->idx)$extra_default = $extra->default;
				}

				$temp_output = preg_replace_callback('/'.$extra_default.'/is', function($input) use($target_extra,$img_size_width,$img_size_height){
					return $this->changeExtraVarContent($input,$target_extra,$img_size_width,$img_size_height);
					}, $output, 1);

				if($temp_output)
				{
					$output = $temp_output;
				}
				unset($temp_output);
			}
		}
	}

	function changeExtraVarContent($input,$target_extra,$img_size_width,$img_size_height)
	{
		$document_srl = Context::get('document_srl');
		$args = new stdClass;
		$args->upload_target_srl = $document_srl;
		$args->target_extra = $target_extra;
		$output = executeQueryArray('extravar_upload.getFiles', $args);
		$out = '';
		$out_b = '';
		foreach($output->data as $data)
		{
			if(preg_match('/\.(jpe?g|png|gif)$/i', $data->uploaded_filename)){
				$out = $out.'<div class="extra-imge"><img style="width:'.$img_size_width.';height:'.$img_size_height.'" src="'.$data->uploaded_filename.'"></div>';
			}elseif(preg_match('/\.(mp4|webm)$/i', $data->uploaded_filename)){
				$out = $out.'<div class="extra-imge"><video loop="" autoplay="" muted="" style="width:'.$img_size_width.';height:'.$img_size_height.'"><source src="'.$data->uploaded_filename.' "type="video/mp4" /></video></div>';
			}else{
				$out_b = $out_b.'<div class="extra-binary"><img src="./modules/extravar_upload/tpl/img/icons8-attach-24.png"><a href="'.$data->uploaded_filename.'">'.$data->source_filename.'</a></div>';
			}
		}
		$out = $out.$out_b;
		return $out;
	}

	function replaceExtraVar($input,$target_extra,$limit_count,$limit_size,$img_size_width,$img_size_height)
	{
		if($limit_count == 0 && $limit_size == 0){
			$limit_string = '';
		}
		elseif($limit_count == 0 && $limit_size != 0){
			$limit_string = '<div class="upload_limit">업로드 제한 : '.$limit_size.'MB</div>';
		}
		elseif($limit_count != 0 && $limit_size == 0){
			$limit_string = '<div class="upload_limit">업로드 제한 : '.$limit_count.' 개</div>';
		}
		else{
			$limit_string = '<div class="upload_limit">업로드 제한 : '.$limit_count.' 개 / '.$limit_size.'MB</div>';
		}

		if($target_extra == 2) $file_name = '이미지';
		else $file_name = '파일'; 

		$input = preg_replace('/<select/is', '$0 style="display:none;"', $input);
		$input = $input[0].'<div><input id="Filedata_e" class="Filedata_e" style="display:none;" multiple="multiple" type="file" name="filename_e[]" name_srl="'.$target_extra.'" limit_count="'.$limit_count.'" limit_size="'.$limit_size.'"/>
		<a class="app-button file_upload_btn" style="width: 100%" href="javascript:void(0)" onclick="jQuery(this).closest(\'div\').find(\'.Filedata_e\').click()">'.$file_name.' 업로드</a></div>
		'.$limit_string.'
		<div style="display:none;" class="extra-file-image-d" name_srl="'.$target_extra.'"></div>
		<div style="display:none;" class="extra-file-binary-d" name_srl="'.$target_extra.'"></div>
		<div style="display:none;" class="extra-file-control" name_srl="'.$target_extra.'"><div style="float: left;"><span class="extra_file_count" name_srl="'.$target_extra.'"></span><span class="extra_attached_size" name_srl="'.$target_extra.'"></span><span class="allowed_attach_size_extra" name_srl="'.$target_extra.'"></span></div><input type="button" name="delete-select-item" value="선택 삭제" name_srl="'.$target_extra.'"></div>
		';
		return $input;
	}

	public function insertFileExtraVar()
	{
		Context::setRequestMethod('JSON');
		Context::setResponseMethod('JSON');

		$obj = Context::getRequestVars();

		$upload_target_srl = $obj->upload_target_srl;
		if(!$upload_target_srl || $upload_target_srl == 0)
		{
			$upload_target_srl = getNextSequence();

			$oFileController = FileController::getInstance();
			$oFileController->setUploadInfo(0, $upload_target_srl);
		}

		$oModuleModel = &getModel('module');
		$ModuleInfo = $oModuleModel->getModuleInfoByMid($obj->module_mid);

		$module_srl = $ModuleInfo->module_srl;
		$target_extra = $obj->target_extra;

		$args = new stdClass;
		$args->target_module_srl = $module_srl;
		$args->target_extra = $target_extra;

		$output = executeQuery('extravar_upload.getReginfo', $args);

		$limit_count = $output->data->limit_count;
		$limit_size = $output->data->limit_size;
		$img_size_width = $output->data->img_size_width;
		$img_size_height = $output->data->img_size_height;

		if($limit_count > 0){
			$vars = new stdClass;
			$vars->upload_target_srl = $upload_target_srl;
			$vars->target_extra = $target_extra;
			$output = $this->GetFileCount($vars);
			$file_count = $output->data->count+1;
			if($file_count>$limit_count)return new BaseObject(-1, 'msg_exceeds_limit_count');
		}
		if($limit_size > 0){
			$allowed_filesize = $limit_size * 1024 * 1024;
			if($allowed_filesize < filesize($obj->Filedata['tmp_name'])) return new BaseObject(-1, 'msg_exceeds_limit_size');
			$size_args = new stdClass;
			$size_args->upload_target_srl = $upload_target_srl;
			$size_args->target_extra = $target_extra;
			$output = executeQuery('extravar_upload.getAttachedFileSize', $size_args);
			$attached_size = (int)$output->data->attached_size + filesize($obj->Filedata['tmp_name']);
			if($attached_size > $allowed_filesize) return new BaseObject(-1, 'msg_exceeds_limit_size');
		}

		$folder = _XE_PATH_ . "files/extravar_upload";

		if (!is_dir($folder)) mkdir($folder);

		$file_info = $obj->Filedata;

		$file_info['name'] = preg_replace('/\.(php|phtm|phar|html?|cgi|pl|exe|jsp|asp|inc)/i', '$0-x',$file_info['name']);
		$file_info['name'] = removeHackTag($file_info['name']);
		$file_info['name'] = str_replace(array('<','>'),array('%3C','%3E'),$file_info['name']);
		$file_info['name'] = str_replace('&amp;', '&', $file_info['name']);

		$random = new Password();

		$path = sprintf("./files/extravar_upload/%s/%s", $module_srl,getNumberingPath($upload_target_srl,3));

		$ext = substr(strrchr($file_info['name'],'.'),1);

		$_filename = $random->createSecureSalt(32, 'hex').'.'.$ext;
		$filename  = $path.$_filename;

		$idx = 1;
		while(file_exists($filename))
		{
			$filename = $path.preg_replace('/\.([a-z0-9]+)$/i','_'.$idx.'.$1',$_filename);
			$idx++;
		}

		if(!FileHandler::makeDir($path)) return new BaseObject(-1,'msg_not_permitted_create');

		if(!@move_uploaded_file($file_info['tmp_name'], $filename))
		{
			$filename = $path.$random->createSecureSalt(32, 'hex').'.'.$ext;
			if(!@move_uploaded_file($file_info['tmp_name'], $filename))  return new BaseObject(-1,'msg_file_upload_error');
		}

		$oMemberModel = getModel('member');
		$member_srl = $oMemberModel->getLoggedMemberSrl();

		$args = new stdClass;
		$args->file_srl = getNextSequence();
		$args->upload_target_srl = $upload_target_srl;
		$args->target_extra = $obj->target_extra;
		$args->module_srl = $module_srl;
		$args->source_filename = $file_info['name'];
		$args->uploaded_filename = $filename;
		$args->download_count = 0;
		$args->file_size = @filesize($filename);
		$args->member_srl = $member_srl;

		$output = executeQuery('extravar_upload.insertFile', $args);

		if(!$output->toBool()) return $output;

		$this->add('file_srl', $args->file_srl);
		$this->add('file_size', $args->file_size);
		$this->add('source_filename', $args->source_filename);
		$this->add('upload_target_srl', $upload_target_srl);
		$this->add('uploaded_filename', $args->uploaded_filename);
		return;
	}

	function GetFileCount($vars){
		$args = new stdClass;
		$args->upload_target_srl = $vars->upload_target_srl;
		$args->target_extra = $vars->target_extra;
		$output = executeQuery('extravar_upload.getFileCount', $args);
		return $output;
	}

	function deleteFileExtraVar()
	{
		$file_srls = Context::get('file_srls');
		$mid = Context::get('mid_name');
		$oModuleModel = &getModel('module');
		$ModuleInfo = $oModuleModel->getModuleInfoByMid($mid);
		$module_srl = $ModuleInfo->module_srl;

		$logged_info = Context::get('logged_info');

		$srls = explode(',',$file_srls);

		if(!count($srls)) return;

		for($i=0;$i<count($srls);$i++)
		{
			$srl = (int)$srls[$i];
			if(!$srl) continue;

			$args = new stdClass;
			$args->file_srl = $srl;
			$output = executeQuery('extravar_upload.getFile', $args);
			if(!$output->toBool()) continue;

			$file_info = $output->data;
			if(!$file_info) continue;

			$file_path = $file_info->uploaded_filename;

			$oModuleModel = getModel('module');
			$grant = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($module_srl), $logged_info);

			$file_grant = ($logged_info->is_admin == 'Y' || $logged_info->member_srl == $file_info->member_srl || $grant->manager);

			if(!$file_grant) continue;

			if($srl){
				$args = new stdClass;
				$args->file_srl = $srl;
				$output = executeQuery('extravar_upload.deleteFile', $args);
				if(!$output->toBool()) return $output;

				FileHandler::removeFile($file_path);
				$path_info = pathinfo($file_path);
				FileHandler::removeBlankDir($path_info['dirname']);
			}
		}
	}

	public function getFilesInfo()
	{
		$target_extra = Context::get('target_extra');
		$upload_target_srl = Context::get('upload_target_srl');

		Context::setRequestMethod('JSON');
		Context::setResponseMethod('JSON');

		$args = new stdClass;
		$args->upload_target_srl = $upload_target_srl;
		$args->target_extra = $target_extra;
		$output = executeQueryArray('extravar_upload.getFiles', $args);

		if(!$output->toBool()) return $output;

		$arr = array();
		foreach($output->data as $data)
		{
			$arr[] = array("target_extra" => $data->target_extra, "file_srl" => $data->file_srl, "file_size" => $data->file_size, "source_filename" => $data->source_filename, "uploaded_filename" => $data->uploaded_filename, "cover_extra" => $data->cover_extra);
		}
		$this->add('data', $arr);
		return;
	}

	public function triggerAfterInsertDocument(&$output)
	{
		$cover_extra = $output->cover_extra;
		$upload_target_srl = $output->document_srl;
		$args = new stdClass();
		$args->upload_target_srl = $upload_target_srl;
		$args->isvalid = 'Y';
		$out = executeQuery('extravar_upload.updateFileValid', $args);
		
		$args = new stdClass();
		$args->upload_target_srl = $upload_target_srl;
		$args->cover_extra = 'N';
		$out = executeQuery('extravar_upload.updateCoverExtraN', $args);

		if($cover_extra){
			$args = new stdClass();
			$args->file_srl = $cover_extra;
			$args->cover_extra = 'Y';
			$out = executeQuery('extravar_upload.updateCoverExtra', $args);
		}else{
			$args = new stdClass();
			$args->upload_target_srl = $upload_target_srl;
			$args->list_count = 1000;
			$out = executeQueryArray('extravar_upload.getFilesList', $args);
			if($out->data){
				$args = new stdClass();
				$args->file_srl = $out->data[1]->file_srl;
				$args->cover_extra = 'Y';
				$out = executeQuery('extravar_upload.updateCoverExtra', $args);
			}
		}
	}
	public function triggerAfterUpdateDocument(&$output)
	{
		$cover_extra = $output->cover_extra;
		$upload_target_srl = $output->document_srl;
		$args = new stdClass();
		$args->upload_target_srl = $upload_target_srl;
		$args->isvalid = 'Y';
		$out = executeQuery('extravar_upload.updateFileValid', $args);
		
		$args = new stdClass();
		$args->upload_target_srl = $upload_target_srl;
		$args->cover_extra = 'N';
		$out = executeQuery('extravar_upload.updateCoverExtraN', $args);

		if($cover_extra){
			$args = new stdClass();
			$args->file_srl = $cover_extra;
			$args->cover_extra = 'Y';
			$out = executeQuery('extravar_upload.updateCoverExtra', $args);
		}		
	}

	public function triggerAfterDeleteDocument($obj)
	{
		$upload_target_srl = $obj->document_srl;
		$args = new stdClass;
		$args->upload_target_srl = $upload_target_srl;
		$output = executeQueryArray('extravar_upload.getFileAll', $args);
		if(!$output->toBool()) return $output;
		$output_d = executeQuery('extravar_upload.deleteFiles', $args);
		if(!$output_d->toBool()) return $output_d;

		foreach($output->data as $data)
		{
			FileHandler::removeFile($data->uploaded_filename);
		}
		$path_info = pathinfo($output->data[0]->uploaded_filename);
		FileHandler::removeBlankDir($path_info['dirname']);
	}
	
	public function triggerAfterGetDocumentList($obj)
	{
		$module_info = Context::get('module_info');
		$module_srl = $module_info->module_srl;

		$args = new stdClass;
		$args->target_module_srl = $module_srl;
		$out = executeQueryArray('extravar_upload.getReginfoTargetSrl', $args);
		
		foreach($out->data as $config)
		{
			if($config->use_cover == 'N') return;
			$target_extra = $config->target_extra;
			
			$cover_size_width = $config->cover_size_width;
			if(!$cover_size_width)$cover_size_width = 'auto';
			$cover_size_height = $config->cover_size_height;
			if(!$cover_size_height)$cover_size_height = 'auto';
		
			foreach($GLOBALS['XE_DOCUMENT_LIST'] as $no => $data){
				$args = new stdClass;
				$args->upload_target_srl = $no;
				$args->target_extra = $target_extra;
				$args->cover_extra = 'Y';
				$output = executeQuery('extravar_upload.getCover', $args);
				if($output->data){
					$GLOBALS['XE_DOCUMENT_LIST'][$no]->cover_extra_info[$target_extra]->cover_extra = 'Y';
					$GLOBALS['XE_DOCUMENT_LIST'][$no]->cover_extra_info[$target_extra]->cover_extra_url = $output->data->uploaded_filename;
					$GLOBALS['XE_DOCUMENT_LIST'][$no]->cover_extra_info[$target_extra]->cover_size_width = $cover_size_width;
					$GLOBALS['XE_DOCUMENT_LIST'][$no]->cover_extra_info[$target_extra]->cover_size_height = $cover_size_height;
				}else{
					$GLOBALS['XE_DOCUMENT_LIST'][$no]->cover_extra_info[$target_extra]->cover_extra = 'N';
				}
			}
		}
	}

	// 썸네일 생성
	public function triggerBeforeGetThumbnail($obj)
	{
		$oExtravarUploadModel = Extravar_uploadModel::getInstance();
		$cover_extra = $oExtravarUploadModel->getCoverImage($obj->document_srl);
		if (!$cover_extra) return;

		$source_file = $cover_extra->uploaded_filename;

		return FileHandler::createImageFile($source_file, $obj->filename, $obj->width, $obj->height, $obj->image_type, $obj->type, $obj->quality);
	}
}
