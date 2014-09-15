<?php
$str = "<a href=\"/search?initiative_id=tbindexz_20140812&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q=%B8%BA%D6%D8%B0%F3%CD%C8&tab=all&bcoffset=1&s=0\" class=\"page-prev\" trace=\'srp_select_pageup\'><span class=\"icon-btn-prev-2\"></span></a><a href=\"/search?initiative_id=tbindexz_20140812&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q=%B8%BA%D6%D8%B0%F3%CD%C8&tab=all&bcoffset=1&s=88\"  class=\"page-next\" trace='srp_select_pagedown'>";
$pattern = "/<\/a><a href=\"(.*?)\"  class=\"page-next\" trace='srp_select_pagedown'>/i";
#$pattern = "/<a href=\"\/([_-=\.\?%&a-z0-9]+?)\"  class=\"page-next\" trace='srp_select_pagedown'>/i";
preg_match_all($pattern, $str, $match);
print_r($match);
