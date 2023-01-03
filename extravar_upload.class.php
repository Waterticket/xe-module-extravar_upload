<?php


class Extravar_upload extends ModuleObject
{

	protected static $_insert_triggers = array(
		array('display', 'before', 'controller', 'triggerBeforeDisplay'),
		array('document.insertDocument', 'after', 'controller', 'triggerAfterInsertDocument'),
		array('document.updateDocument', 'after', 'controller', 'triggerAfterUpdateDocument'),
		array('document.deleteDocument', 'after', 'controller', 'triggerAfterDeleteDocument'),
		array('document.getDocumentList', 'after', 'controller', 'triggerAfterGetDocumentList'),
		array('document.getThumbnail', 'before', 'controller', 'triggerBeforeGetThumbnail'),
	);
	

	protected static $_delete_triggers = array(

	);
	
	protected static $_config_cache = null;
	
	protected static $_cache_handler_cache = null;
	
	public function getConfig()
	{
		if (self::$_config_cache === null)
		{
			$oModuleModel = getModel('module');
			self::$_config_cache = $oModuleModel->getModuleConfig($this->module) ?: new stdClass;
		}
		return self::$_config_cache;
	}

	public function setConfig($config)
	{
		$oModuleController = getController('module');
		$result = $oModuleController->insertModuleConfig($this->module, $config);
		if ($result->toBool())
		{
			self::$_config_cache = $config;
		}
		return $result;
	}

	public function getCache($key, $ttl = 86400, $group_key = null)
	{
		if (self::$_cache_handler_cache === null)
		{
			self::$_cache_handler_cache = CacheHandler::getInstance('object');
		}
		
		if (self::$_cache_handler_cache->isSupport())
		{
			$group_key = $group_key ?: $this->module;
			return self::$_cache_handler_cache->get(self::$_cache_handler_cache->getGroupKey($group_key, $key), $ttl);
		}
		else
		{
			return false;
		}
	}
	
	public function setCache($key, $value, $ttl = 86400, $group_key = null)
	{
		if (self::$_cache_handler_cache === null)
		{
			self::$_cache_handler_cache = CacheHandler::getInstance('object');
		}
		
		if (self::$_cache_handler_cache->isSupport())
		{
			$group_key = $group_key ?: $this->module;
			return self::$_cache_handler_cache->put(self::$_cache_handler_cache->getGroupKey($group_key, $key), $value, $ttl);
		}
		else
		{
			return false;
		}
	}
	
	public function deleteCache($key, $group_key = null)
	{
		if (self::$_cache_handler_cache === null)
		{
			self::$_cache_handler_cache = CacheHandler::getInstance('object');
		}
		
		if (self::$_cache_handler_cache->isSupport())
		{
			$group_key = $group_key ?: $this->module;
			self::$_cache_handler_cache->delete(self::$_cache_handler_cache->getGroupKey($group_key, $key));
		}
		else
		{
			return false;
		}
	}
	
	public function clearCache($group_key = null)
	{
		if (self::$_cache_handler_cache === null)
		{
			self::$_cache_handler_cache = CacheHandler::getInstance('object');
		}
		
		if (self::$_cache_handler_cache->isSupport())
		{
			$group_key = $group_key ?: $this->module;
			return self::$_cache_handler_cache->invalidateGroupKey($group_key) ? true : false;
		}
		else
		{
			return false;
		}
	}
	
	public function createObject($status = 0, $message = 'success' /* $arg1, $arg2 ... */)
	{
		$args = func_get_args();
		if (count($args) > 2)
		{
			global $lang;
			$message = vsprintf($lang->$message, array_slice($args, 2));
		}
		return class_exists('BaseObject') ? new BaseObject($status, $message) : new Object($status, $message);
	}
	
	public function checkTriggers()
	{
		$oModuleModel = getModel('module');
		foreach (self::$_insert_triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $this->module, $trigger[2], $trigger[3], $trigger[1]))
			{
				return true;
			}
		}
		foreach (self::$_delete_triggers as $trigger)
		{
			if ($oModuleModel->getTrigger($trigger[0], $this->module, $trigger[2], $trigger[3], $trigger[1]))
			{
				return true;
			}
		}
		return false;
	}
	
	public function registerTriggers()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		foreach (self::$_insert_triggers as $trigger)
		{
			if (!$oModuleModel->getTrigger($trigger[0], $this->module, $trigger[2], $trigger[3], $trigger[1]))
			{
				$oModuleController->insertTrigger($trigger[0], $this->module, $trigger[2], $trigger[3], $trigger[1]);
			}
		}
		foreach (self::$_delete_triggers as $trigger)
		{
			if ($oModuleModel->getTrigger($trigger[0], $this->module, $trigger[2], $trigger[3], $trigger[1]))
			{
				$oModuleController->deleteTrigger($trigger[0], $this->module, $trigger[2], $trigger[3], $trigger[1]);
			}
		}
		return $this->createObject(0, 'success_updated');
	}
	
	public function moduleInstall()
	{
		$oModuleController = getController('module');
		$oModuleModel = getModel('module');
		$e_module = $oModuleModel->getModuleInfoByMid('extravar_upload');

		if(!$e_module->module_srl)
		{
			$args = new stdClass();
			$args->mid = 'extravar_upload';
			$args->module = 'extravar_upload';
			$args->browser_title = 'extravar_upload_temp';
			$args->use_mobile = 'Y';
			$args->site_srl = 0;
			$args->layout_srl = -1;
			$args->mlayout_srl = -1;
			$oModuleController->insertModule($args);
		}
		return $this->registerTriggers();
	}
	
	public function checkUpdate()
	{
		$oModuleController = getController('module');
		$oModuleModel = getModel('module');
		$e_module = $oModuleModel->getModuleInfoByMid('extravar_upload');

		if(!$e_module->module_srl)
		{
			$args = new stdClass();
			$args->mid = 'extravar_upload';
			$args->module = 'extravar_upload';
			$args->browser_title = 'extravar_upload_temp';
			$args->use_mobile = 'Y';
			$args->site_srl = 0;
			$args->layout_srl = -1;
			$args->mlayout_srl = -1;
			$oModuleController->insertModule($args);
		}

		$oDB = &DB::getInstance();
		if(!$oDB->isColumnExists("extravar_upload_files", "cover_extra")) return true;
		if(!$oDB->isIndexExists("extravar_upload_files","idx_cover_extra")) return true;
		
		if(!$oDB->isColumnExists("extravar_upload_reg_info", "use_cover")) return true;
		if(!$oDB->isColumnExists("extravar_upload_reg_info", "cover_size_width")) return true;
		if(!$oDB->isColumnExists("extravar_upload_reg_info", "cover_size_height")) return true;
		
		return $this->checkTriggers();
	}
	
	public function moduleUpdate()
	{
		$oDB = &DB::getInstance();
		if(!$oDB->isColumnExists('extravar_upload_files', 'cover_extra')){
			$oDB->addColumn('extravar_upload_files','cover_extra','char',1,'N');
		}
		if(!$oDB->isIndexExists("extravar_upload_files","idx_cover_extra")){
			$oDB->addIndex("extravar_upload_files","idx_cover_extra", array("cover_extra"));
		}
		if(!$oDB->isColumnExists('extravar_upload_reg_info', 'use_cover')){
			$oDB->addColumn('extravar_upload_reg_info','use_cover','char',1,'N');
		}
		if(!$oDB->isColumnExists('extravar_upload_reg_info', 'cover_size_width')){
			$oDB->addColumn('extravar_upload_reg_info','cover_size_width','text');
		}
		if(!$oDB->isColumnExists('extravar_upload_reg_info', 'cover_size_height')){
			$oDB->addColumn('extravar_upload_reg_info','cover_size_height','text');
		}
		return $this->registerTriggers();
	}
	
	public function recompileCache()
	{
		$this->clearCache();
	}
}
