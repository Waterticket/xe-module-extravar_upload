<?php

class Extravar_uploadAdminView extends Extravar_upload
{
	public function init()
	{
		$this->setTemplatePath($this->module_path . 'tpl');
	}

	public function dispExtravar_uploadAdminList()
	{
		$args = new stdClass();

		$page = Context::get('page');
		if(!$page) $page = 1;
		$args->page = $page;
		$args->list_count = '20';
		$args->page_count = '10';
		$args->sort_index = Context::get('sort_index');

		$output = executeQueryArray('extravar_upload.getReginfoAll', $args);

		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);
		Context::set('data_list', $output->data);

		$this->setTemplateFile('list');
	}

	public function dispExtravar_uploadAdminInsert()
	{

		$oModuleModel = getModel('module');
		$module_list = $oModuleModel->getMidList();

		$temp_list = array();

		foreach($module_list as $m)
		{
			if($m->module == 'board')
			{
				$temp_list[] = $m;
			}
		}

		Context::set('module_list', $temp_list);

		$this->setTemplateFile('insert');
	}

	public function dispExtravar_uploadAdminModify()
	{
		$obj = Context::getRequestVars();

		$args = new stdClass;
		$args->extravar_srl = $obj->extravar_srl;
		$output = executeQuery('extravar_upload.getReginfoSrl', $args);
		
		$oModuleModel = &getModel('module');
		$module_info = $oModuleModel->getModuleInfoByModuleSrl($output->data->target_module_srl);
		$module_mid = $module_info->mid;

		Context::set('module_mid', $module_mid);
		Context::set('data', $output->data);

		$this->setTemplateFile('modify');
	}

	public function dispExtravar_uploadAdminFilesList()
	{
		$search_target = trim(Context::get('search_target'));
		$search_keyword = trim(Context::get('search_keyword'));
		
		$args = new stdClass();

		$page = Context::get('page');
		if(!$page) $page = 1;
		$args->page = $page;
		$args->list_count = '20';
		$args->page_count = '10';
		$args->sort_index = Context::get('sort_index');

		if($search_target && $search_keyword){
			if($search_target == 'isvalid'){
				$args->isvalid = $search_keyword;
			}
			elseif($search_target == 'source_filename'){
				$args->source_filename = $search_keyword;
			}
			$output = executeQueryArray('extravar_upload.getFilesListSearch', $args);
		}else{
			$output = executeQueryArray('extravar_upload.getFilesList', $args);
		}

		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);
		Context::set('file_list', $output->data);
		
		$this->setTemplateFile('file_list');
	}
	public function dispExtravar_uploadAdminConfigCover()
	{
		$this->setTemplateFile('cover');
	}
}
