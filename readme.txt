① 리스트 썸네일 사용법

리스트 화면에서 썸네일을 사용하기 위해서는 스킨 수정 필요합니다.

1. 사용하는 게시판 스킨 설정 > 고급 > 목록설정 에서 썸네일 표시 되기 원하는 항목 추가

2. 스킨 파일에 리스트를 표시 해주는 파일을 수정 합니다.

3. 스케치북 게시판 기준으로 수정 예제
_list_nomal.html 파일

<!--@if($val->eid=='rating')--><span class="starRating" title="{$document->getExtraValueHTML($val->idx)}{$lang->score}"><span style="width:{$document->getExtraValueHTML($val->idx)*10}%">{$document->getExtraValueHTML($val->idx)}</span></span><!--@else-->{$document->getExtraValueHTML($val->idx)}<!--@end-->

위 내용을 찾아 아래와 같이 수정(공지 부분과 리스트 부분 2군데 있음)

<!--@if($val->eid=='rating')--><span class="starRating" title="{$document->getExtraValueHTML($val->idx)}{$lang->score}"><span style="width:{$document->getExtraValueHTML($val->idx)*10}%">{$document->getExtraValueHTML($val->idx)}</span></span><!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='Y')--><img width="{$document->cover_extra_info[$val->idx]->cover_size_width}" height="{$document->cover_extra_info[$val->idx]->cover_size_height}" src="{$document->cover_extra_info[$val->idx]->cover_extra_url}"><!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='N')--><!--@else-->{$document->getExtraValueHTML($val->idx)}<!--@end--></td>


* 설명 
<!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='Y')--> : 썸네일이 존재하면 이미지를 표시합니다.

{$document->cover_extra_info[$val->idx]->cover_size_width} : 썸네일 넓이 변수
{$document->cover_extra_info[$val->idx]->cover_size_height} : 썸네일 높이 변수
{$document->cover_extra_info[$val->idx]->cover_extra_url} : 썸네일 주소 변수

<!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='N')--> : 썸네일이 존재하지 않은 글은 빈 공간으로 둡니다.

다른 게시판도 위 문장을 참고하여 수정하면 됩니다.

ps. 이미지 파일만 가능하며 이미지 썸네일 지정을 하지 않으면 처번째 업로드된 파일이 썸네일로 자동 지정됩니다.



② 추가된 이미지파일 목록 가져오는 함수 사용법

사용하고 싶으신곳(스킨파일/모듈/위젯등) 에서

$extra_files = getModel('extravar_upload')->getImageFiles($document_srl);

의 형식으로 호출 하시면 $extra_files에 배열 형식의 이미지 파일 정보를 가져오게 되있으며

원하시는 형식으로 편집해서 사용하시면 됩니다.