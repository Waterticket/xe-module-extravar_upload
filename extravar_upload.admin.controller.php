<?php


class Extravar_uploadAdminController extends Extravar_upload
{

	public function procExtravar_uploadAdminInsert()
	{
		$vars = Context::getRequestVars();

		foreach($vars->target_list as $target)
		{
			$temp = explode(',', $target);
			$args = new stdClass;
			$args->target_module_srl = $temp[0];
			$args->target_extra = $temp[1];
			$output = executeQueryArray('extravar_upload.getReginfo', $args);
			if($output->data){
				return new BaseObject(-1, 'msg_already_registed');
			}else{
				$args->use = $vars->use;
				$args->limit_count = $vars->limit_count;
				$args->limit_size = $vars->limit_size;
				$args->img_size_width = $vars->img_size_width;
				$args->img_size_height = $vars->img_size_height;			
				$output = executeQueryArray('extravar_upload.insertReginfo', $args);
				if (!$output->toBool())
				{
					return $output;
				}
			}
		}

		$this->setMessage('success_registed');
		$this->setRedirectUrl(Context::get('success_return_url'));
	}
	
	public function procExtravar_uploadAdminGetExtraKeys()
	{
		$obj = Context::getRequestVars();
		$module_srl = $obj->module_srl;
		
		Context::setRequestMethod('JSON');
		Context::setResponseMethod('JSON');
		
		$oDocumentModel = getModel('document');
		$target_extra = $oDocumentModel->getExtraKeys($module_srl);
		
		$this->add('file_srl', $target_extra);
		return;
	}
	public function procExtravar_uploadAdminDelete()
	{
		$obj = Context::getRequestVars();
		
		$args = new stdClass;
		$args->extravar_srl = $obj->extravar_srl;
		$output = executeQueryArray('extravar_upload.delReginfo', $args);
		if (!$output->toBool())
		{
			return $output;
		}
	}
	public function procExtravar_uploadAdminModify()
	{
		$obj = Context::getRequestVars();

		$args = new stdClass;
		$args->extravar_srl = $obj->extravar_srl;
		$args->use = $obj->use;
		$args->limit_count = $obj->limit_count;
		$args->limit_size = $obj->limit_size;
		$args->img_size_width = $obj->img_size_width;
		$args->img_size_height = $obj->img_size_height;
		$args->use_cover = $obj->use_cover;
		$args->cover_size_width = $obj->cover_size_width;
		$args->cover_size_height = $obj->cover_size_height;
		
		$output = executeQuery('extravar_upload.updateReginfo', $args);

		if (!$output->toBool())
		{
			return $output;
		}
		$this->setMessage('success_registed');
		$this->setRedirectUrl(Context::get('success_return_url'));
	}
	public function procExtravar_uploadAdminDeleteFile()
	{
		$obj = Context::getRequestVars();
		
		foreach($obj->file_srl as $file)
		{
			$args = new stdClass;
			$args->file_srl = $file;

			$output = executeQuery('extravar_upload.getFile', $args);
			if(!$output->toBool()) continue;

			$file_info = $output->data;
			if(!$file_info) continue;

			$file_path = $file_info->uploaded_filename;

			$output = executeQuery('extravar_upload.deleteFile', $args);
			if (!$output->toBool())
			{
				return $output;
			}

			FileHandler::removeFile($file_path);
			$path_info = pathinfo($file_path);
			FileHandler::removeBlankDir($path_info['dirname']);
		}
	}
}
