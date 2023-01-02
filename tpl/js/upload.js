jQuery('input[name=delete-select-item]').on("click",function(){
	var name_srl = jQuery(this).attr('name_srl');

	if(jQuery('input[name=cart][name_srl='+name_srl+']:checked').length == 0){
		alert('삭제할 항목을 선택해주세요');
		return false;
	}
	var file_srls = [];
	jQuery('input[name=cart][name_srl='+name_srl+']:checked').each(function() {
		file_srl = jQuery(this).attr('data-file-srl');
		file_srls.push(file_srl);
	});
	exec_json('extravar_upload.deleteFileExtraVar', {'file_srls': file_srls.join(','), 'mid_name': window.current_mid}, function(data) {
		var message = data.message;
		if(message != 'success') {
			alert(message);
			return false;
		}else{
			jQuery.each(file_srls, function(idx, srl){
				jQuery('.extra-file-image[data-file-srl='+srl+']').remove();
				jQuery('.extra-file-binary[data-file-srl='+srl+']').remove();
			});
		};
		if(jQuery('.extra-file-image[name_srl='+name_srl+']').length == 0){
			jQuery('.extra-file-image-d[name_srl='+name_srl+']').css('display','none');
		};
		if(jQuery('.extra-file-binary[name_srl='+name_srl+']').length == 0){
			jQuery('.extra-file-binary-d[name_srl='+name_srl+']').css('display','none');
		};
		if(jQuery('.extra-file-image[name_srl='+name_srl+']').length == 0 & jQuery('.extra-file-binary[name_srl='+name_srl+']').length == 0){
			jQuery('.extra-file-control[name_srl='+name_srl+']').css('display','none');
		}
	});
});

function getFileinfo(name_srl){
	var extra_file_size = 0;
	jQuery('.extra-file-size[name_srl='+name_srl+']').each(function(){
		var size = (/\d+(?:[.]?[\d]+)/gi).exec(jQuery(this).html())[0];
		extra_file_size += Number(size);
	});
	jQuery('.extra-file-info[name_srl='+name_srl+']').each(function(){
		var size = (/\d+(?:[.]?[\d]+)/gi).exec(jQuery(this).html())[0];
		extra_file_size += Number(size);
	});
	if(extra_file_size < 1024){
		extra_file_size = extra_file_size.toFixed(1)+'KB';
	}else{
		extra_file_size = (extra_file_size/1024).toFixed(1)+'MB';
	}
	var extra_file_count = jQuery('.extra-file-image[name_srl='+name_srl+']').length + jQuery('.extra-file-binary[name_srl='+name_srl+']').length;
	var params = {
		'file_size' : extra_file_size,
		'file_count' : extra_file_count,
	};
	return params;
}
function set_cover(){
	jQuery('div.cover-extra').off('click');
	jQuery('div.cover-extra').on('click', function(){
		var idx = jQuery(this).attr('name_srl');
		if(jQuery(this).parent().parent('div.extra-file-image').attr('cover') == 'Y'){
			jQuery('div.extra-file-image[name_srl='+idx+']').css('border','3px solid #DDD').removeAttr('cover');
			jQuery('div.cover-extra[name_srl='+idx+']').css('background','#DDD');
		}else{
			jQuery('div.extra-file-image[name_srl='+idx+']').css('border','3px solid #DDD').removeAttr('cover');
			jQuery('div.cover-extra[name_srl='+idx+']').css('background','#DDD');
			jQuery(this).css('background','#7de65b').parent().parent('div.extra-file-image').attr('cover','Y').css('border','3px solid #7de65b');
		}
		var input_file_srl = new Array;
		jQuery('div.extra-file-image[cover=Y]').each(function(index,item){
			input_file_srl.push(jQuery(item).attr('data-file-srl'));
		});
		jQuery('form[editor_sequence]>input[name=cover_extra]').remove();
		jQuery('form[editor_sequence]').append('<input type="hidden" name="cover_extra" value='+input_file_srl+'>')
	});
}

jQuery('.extra-file-image-d, .extra-file-binary-d').on("DOMSubtreeModified",function(){
	var name_srl = jQuery(this).attr('name_srl');
	var fileinfo = getFileinfo(name_srl);
	jQuery('.extra-file-control[name_srl='+name_srl+']').css('display','block');
	jQuery('.extra_file_count[name_srl='+name_srl+']').html(fileinfo.file_count+'개 첨부 됨');
	jQuery('.extra_attached_size[name_srl='+name_srl+']').html(' ('+fileinfo.file_size+')');
	set_cover();
});

jQuery(function(){
	jQuery("input#Filedata_e").on('change', function(){
		var file_list = jQuery(this)[0].files;
		var target_extra = jQuery(this).attr('name_srl');
		var idx = jQuery("input#Filedata_e").index(this);
		jQuery.each(file_list,function(i,item){
			var formData = new FormData();
			formData.append("upload_target_srl", jQuery('input[name=document_srl]').val());
			formData.append("Filedata", item);
			formData.append("target_extra", target_extra);
			formData.append("module_mid", current_mid);
			jQuery.ajax({
				url: request_uri + 'index.php?mid=extravar_upload&act=insertFileExtraVar',
				data: formData,
				processData: false,
				contentType: false,
				type: 'POST',
				success: function(data){
					var dataj = data;
					if(dataj.message == 'msg_exceeds_limit_count'){
						alert('업로드 갯수를 초과하였습니다.');
						return false;
					};

					if(dataj.message == 'msg_exceeds_limit_size'){
						alert('업로드 용량을 초과하였습니다.');
						return false;
					};
					
					if(/\.(jpe?g|png|gif)$/i.test(dataj.source_filename)) {
						jQuery('.extra-file-image-d[name_srl='+target_extra+']').append('<div class="extra-file-image" data-file-srl="'+dataj.file_srl+'" name_srl="'+target_extra+'"><span><img src="'+dataj.uploaded_filename+'"><div class="cover-extra" name_srl="'+target_extra+'" title="썸네일로 지정"><i class="xi-check"></i></div></span><span class="extra-file-size" name_srl="'+target_extra+'">'+(dataj.file_size/1000).toFixed(1)+'KB</span><span><input name="cart" type="checkbox" data-file-srl="'+dataj.file_srl+'" name_srl="'+target_extra+'"></span></div>');
						jQuery('.extra-file-image-d[name_srl='+target_extra+']').css('display','block');
					}else if(/\.(mp4|webm)$/i.test(dataj.source_filename)) {
						jQuery('.extra-file-image-d[name_srl='+target_extra+']').append('<div class="extra-file-image" data-file-srl="'+dataj.file_srl+'" name_srl="'+target_extra+'"><span><video loop="" autoplay="" muted=""><source src="' + dataj.uploaded_filename + '"type="video/mp4" /></video></span><span class="extra-file-size" name_srl="'+target_extra+'">'+(dataj.file_size/1000).toFixed(1)+'KB</span><span><input name="cart" type="checkbox" data-file-srl="'+dataj.file_srl+'" name_srl="'+target_extra+'"></span></div>');
						jQuery('.extra-file-image-d[name_srl='+target_extra+']').css('display','block');
					}else {
						jQuery('.extra-file-binary-d[name_srl='+target_extra+']').append('<li class="extra-file-binary" data-file-srl="'+dataj.file_srl+'" name_srl="'+target_extra+'"><span class="extra-file-name">'+dataj.source_filename+'</span><span class="extra-file-info" name_srl="'+target_extra+'"><span>'+(dataj.file_size/1000).toFixed(1)+'KB</span><span><input name="cart" type="checkbox" data-file-srl="'+dataj.file_srl+'" name_srl="'+target_extra+'"> Select</span></span></li>');
						jQuery('.extra-file-binary-d[name_srl='+target_extra+']').css('display','block');
					}	
					jQuery('.extra-file-image img').off('click')
					jQuery('.extra-file-image img').on('click',function(){
						jQuery(this).parent().parent().find('input').prop('checked',function(){
								return !jQuery(this).prop('checked');
							});
					})
					
					jQuery('input#Filedata_e').eq(idx).val('');
					
					if(jQuery('input[name=document_srl]').val() == 0){
						jQuery('input[name=document_srl]').val(dataj.upload_target_srl);
					}
				},
			});
		});
	});
	init_get_file();
	fileDropDown();
});

function init_get_file(){
	var document_srl = jQuery('input[name=document_srl]').val(); //current_url.getQuery('document_srl');
	if(document_srl > 0){
		var Filedata = jQuery("input#Filedata_e");
		jQuery.each(Filedata,function(){
			var target_extra = jQuery(this).attr('name_srl');
			var formData = new FormData();
			formData.append("upload_target_srl", document_srl);
			formData.append("target_extra", target_extra);
			formData.append("mid", "extravar_upload");
			formData.append("act", "getFilesInfo");
			jQuery.ajax({
				url: '/',
				data: formData,
				processData: false,
				contentType: false,
				type: 'POST',
				success: function(data){
					console.log(data);
					var dataj = data;
					jQuery.each(dataj.data,function(i,item){
						if(/\.(jpe?g|png|gif)$/i.test(item.source_filename)) {
							jQuery('.extra-file-image-d[name_srl='+target_extra+']').append('<div class="extra-file-image" data-file-srl="'+item.file_srl+'" name_srl="'+target_extra+'"><span><img src="'+item.uploaded_filename+'"><div class="cover-extra" name_srl="'+target_extra+'" title="썸네일로 지정"><i class="xi-check"></i></div></span><span class="extra-file-size" name_srl="'+target_extra+'">'+(item.file_size/1000).toFixed(1)+'KB</span><span><input name="cart" type="checkbox" data-file-srl="'+item.file_srl+'" name_srl="'+target_extra+'"></span></div>');
							jQuery('.extra-file-image-d[name_srl='+target_extra+']').css('display','');
						}else if(/\.(mp4|webm)$/i.test(item.source_filename)) {
							jQuery('.extra-file-image-d[name_srl='+target_extra+']').append('<div class="extra-file-image" data-file-srl="'+item.file_srl+'" name_srl="'+target_extra+'"><span><video loop="" autoplay="" muted=""><source src="' + item.uploaded_filename + '"type="video/mp4" /></video></span><span class="extra-file-size" name_srl="'+target_extra+'">'+(item.file_size/1000).toFixed(1)+'KB</span><span><input name="cart" type="checkbox" data-file-srl="'+item.file_srl+'" name_srl="'+target_extra+'"></span></div>');
							jQuery('.extra-file-image-d[name_srl='+target_extra+']').css('display','');
						}else {
							jQuery('.extra-file-binary-d[name_srl='+target_extra+']').append('<li class="extra-file-binary" data-file-srl="'+item.file_srl+'" name_srl="'+target_extra+'"><span class="extra-file-name">'+item.source_filename+'</span><span class="extra-file-info" name_srl="'+target_extra+'"><span>'+(item.file_size/1000).toFixed(1)+'KB</span><span><input name="cart" type="checkbox" data-file-srl="'+item.file_srl+'" name_srl="'+target_extra+'"> Select</span></span></li>');
							jQuery('.extra-file-binary-d[name_srl='+target_extra+']').css('display','');
						}
						if(item.cover_extra == 'Y'){
							set_cover();
							jQuery('.extra-file-image[data-file-srl='+item.file_srl+']').find('.cover-extra').trigger('click');
						}
					})
					jQuery('.extra-file-image img').off('click')
					jQuery('.extra-file-image img').on('click',function(){
						jQuery(this).parent().parent().find('input').prop('checked',function(){
								return !jQuery(this).prop('checked');
							});
					})
				}
			});
		})
	};
}

function fileDropDown() {
	// var dropZone = $("#file_upload_btn");
	
	// dropZone.on('dragenter', function(e) {
	// 	e.stopPropagation();
	// 	e.preventDefault();
	// 	dropZone.css('background-color', '#E3F2FC');
	// });
	// dropZone.on('dragleave', function(e) {
	// 	e.stopPropagation();
	// 	e.preventDefault();
	// 	dropZone.css('background-color', '#FFFFFF');
	// });
	// dropZone.on('dragover', function(e) {
	// 	e.stopPropagation();
	// 	e.preventDefault();
	// 	dropZone.css('background-color', '#E3F2FC');
	// });
	// dropZone.on('drop', function(e) {
	// 	e.preventDefault();
	// 	dropZone.css('background-color', '#FFFFFF');

	// 	var files = e.originalEvent.dataTransfer.files;
	// 	if (files != null) {
	// 		if (files.length < 1) {
	// 			alert("폴더는 업로드 할 수 없습니다.");
	// 			return;
	// 		} else {
	// 			dropZone.closest('div').find('.Filedata_e').files = files;
	// 		}
	// 	} else {
	// 		alert("ERROR");
	// 	}
	// });
}
