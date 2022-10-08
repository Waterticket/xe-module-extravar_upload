<?php

class Extravar_uploadModel extends Extravar_upload
{
	public function getImageFiles($upload_target_srl)
	{
		if(!$upload_target_srl)return false;
		
		$args = new stdClass;
		$args->upload_target_srl = $upload_target_srl;
		$args->order_type = 'asc';
		$args->isvalid = 'Y';
		$output = executeQueryArray('extravar_upload.getFilesListByUploadTargetSrl', $args);
		if(!$output->toBool()) return false;
		$temp_output = Array();
		foreach($output->data as $no => $data){
			$match = preg_match('/\.(jpg|jpeg|gif|png)$/', strtolower($data->uploaded_filename), $output_array);
			if($match===0)unset($output->data[$no]);
		}
		return $output->data;
	}
}
