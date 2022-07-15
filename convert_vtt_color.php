<?php
header("Content-Type: text/css; charset=utf-8");

function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return $content;
}

function arr_del($list_arr, $del_num) {
    $key = array_search($del_num, $list_arr);
    array_splice($list_arr, $key, 1);
    return $list_arr;
}

$filename = "./" .urldecode($_GET['name']);
if (file_exists($filename)) {
	$beginTime = 0;
	$endTime = 0;
	$index = 0;
	$file = fopen($filename, "r") or die("파일열기에 실패하였습니다");
	//$filestring = preg_split('/\n/i', $filestrings);
	$str = file_get_contents_utf8($filename);
	if (strtolower(substr($filename, -3)) !== 'smi') {
		return;
	}
	$ary = array();
	$ary[] = "ASCII";
	$ary[] = "JIS";
	$ary[] = "WINDOWS-1252";
	$ary[] = "EUC-KR";
	$ary[] = "SJIS-WIN";
	$ary[] = "UTF-8";
	$ary[] = "CP1252";
	$CharCheck = mb_detect_encoding($str, $ary);
	if ($CharCheck == false) {
		return;
	}
	if ($CharCheck != "UTF-8"){
		$str = mb_convert_encoding($str, "UTF-8", $CharCheck);
	}
	$str = str_replace("\r\n","", $str);
	$str = preg_replace("/<\/body>|<\/sami>/i", "", $str);
	$str = str_replace("\"", "", $str);
	$pattern = "/<font[^>]*color=(?<color>.+?)(?=>|$)/i";
	$strzf = preg_match_all($pattern, $str, $out);
	$arf = array_unique($out['color']);
	sort($arf);
	$size = count($arf);
	for ($i = 0; $i < $size; $i++) {
		$ishex = false;
		$color = strtolower($arf[$i]);
		if (preg_match("/#/i", $color)) {
			$ishex = true;
			$color = str_replace("#","", $color);
		}
		echo "::cue(.color-".$color.") {\r\n";
		echo "    color: ";
		if ($ishex) {
			echo "#";
		}
		echo $color."; \r\n";
		echo "}\r\n\r\n";
	}
}
?> 