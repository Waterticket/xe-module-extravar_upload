�� ����Ʈ ����� ����

����Ʈ ȭ�鿡�� ������� ����ϱ� ���ؼ��� ��Ų ���� �ʿ��մϴ�.

1. ����ϴ� �Խ��� ��Ų ���� > ��� > ��ϼ��� ���� ����� ǥ�� �Ǳ� ���ϴ� �׸� �߰�

2. ��Ų ���Ͽ� ����Ʈ�� ǥ�� ���ִ� ������ ���� �մϴ�.

3. ����ġ�� �Խ��� �������� ���� ����
_list_nomal.html ����

<!--@if($val->eid=='rating')--><span class="starRating" title="{$document->getExtraValueHTML($val->idx)}{$lang->score}"><span style="width:{$document->getExtraValueHTML($val->idx)*10}%">{$document->getExtraValueHTML($val->idx)}</span></span><!--@else-->{$document->getExtraValueHTML($val->idx)}<!--@end-->

�� ������ ã�� �Ʒ��� ���� ����(���� �κа� ����Ʈ �κ� 2���� ����)

<!--@if($val->eid=='rating')--><span class="starRating" title="{$document->getExtraValueHTML($val->idx)}{$lang->score}"><span style="width:{$document->getExtraValueHTML($val->idx)*10}%">{$document->getExtraValueHTML($val->idx)}</span></span><!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='Y')--><img width="{$document->cover_extra_info[$val->idx]->cover_size_width}" height="{$document->cover_extra_info[$val->idx]->cover_size_height}" src="{$document->cover_extra_info[$val->idx]->cover_extra_url}"><!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='N')--><!--@else-->{$document->getExtraValueHTML($val->idx)}<!--@end--></td>


* ���� 
<!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='Y')--> : ������� �����ϸ� �̹����� ǥ���մϴ�.

{$document->cover_extra_info[$val->idx]->cover_size_width} : ����� ���� ����
{$document->cover_extra_info[$val->idx]->cover_size_height} : ����� ���� ����
{$document->cover_extra_info[$val->idx]->cover_extra_url} : ����� �ּ� ����

<!--@elseif($document->cover_extra_info[$val->idx]->cover_extra=='N')--> : ������� �������� ���� ���� �� �������� �Ӵϴ�.

�ٸ� �Խ��ǵ� �� ������ �����Ͽ� �����ϸ� �˴ϴ�.

ps. �̹��� ���ϸ� �����ϸ� �̹��� ����� ������ ���� ������ ó��° ���ε�� ������ ����Ϸ� �ڵ� �����˴ϴ�.



�� �߰��� �̹������� ��� �������� �Լ� ����

����ϰ� �����Ű�(��Ų����/���/������) ����

$extra_files = getModel('extravar_upload')->getImageFiles($document_srl);

�� �������� ȣ�� �Ͻø� $extra_files�� �迭 ������ �̹��� ���� ������ �������� ��������

���Ͻô� �������� �����ؼ� ����Ͻø� �˴ϴ�.