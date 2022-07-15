<?php
header("Content-Type: text/vtt; charset=utf-8");

function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return $content;
}

function samiTime2vttTime($samiTime) {
    $sec = fmod($samiTime / 1000, 60);
    $min = $samiTime / 1000 / 60 % 60;
    $hour = $samiTime / 1000 / 60 / 60;
    return sprintf("%02d:%02d:%06.3f", $hour, $min, $sec);
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
	echo "WEBVTT\n\n";
	if (strtolower(substr($filename, -3)) !== 'smi') {
		return;
	}
	$pattern = "/<SYNC[^>]*Start=(?<time>\d*)[^>]*><P[^>]*Class=(?<lang>\w*)[^>]*>/i";
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
		$adssadf = "00:00:00.000 --> 24:00:00.000\r\n자막에 문제가 발견되었습니다.\r\n(Encoding Find Fail : ".$CharCheck.")";
		echo mb_convert_encoding($adssadf, "UTF-8", mb_detect_encoding($adssadf, $ary));
		return;
	}
	if ($CharCheck != "UTF-8"){
		$str = mb_convert_encoding($str, "UTF-8", $CharCheck);
	}
	$str = str_replace("\r\n","", $str);
	$str = preg_replace("/<\/body>|<\/sami>/i", "", $str);
	$pattern = "/<SYNC[^>]*Start=(?<time>\d*)[^>]*><P[^>]*Class=(?<lang>\w*)[^>]*>/i";
	$strz = preg_split($pattern, $str);
	$strzf = preg_match_all($pattern, $str, $out);
	$strz = arr_del($strz, 0);
	$size = count($strz);
	for ($i = 0; $i < $size - 1; $i++) {
		$istwowrite = false;
		$message = $strz[$i];
		echo samiTime2vttTime($out['time'][$i])." --> ".samiTime2vttTime($out['time'][$i+1])."\r\n";
		// 엔터시작
		if (preg_match("/<br>|<br\/>/i", $message)) {
			$istwowrite = true;
			$message = preg_replace("/<br>|<br\/>/i", "\r\n", $message);
		}
		if ($istwowrite == false) {
			$message = "&nbsp;\r\n".$message;
		}
		// 엔터완료
		// 색깔시작
		$message = preg_replace("/<\/font>/i", "</c>", $message); // </font> 태그 </c> 변경
		$fontcolor_preg = "/<font[^>]*color=(?<color>.+?)(?=>|$)*>/i";
		$fontcolor = preg_match_all($fontcolor_preg, $message, $font_out);
		$fontsize = count($font_out);
		for ($y = 0; $y < $fontsize; $y++) {
			if (array_key_exists($y, $font_out['color'])) {
				$colorname = str_replace("\"", "", $font_out['color'][$y]);
				$colorname = str_replace("#", "", $colorname);
				$message = str_replace($font_out[0][$y], "<c.color-".strtolower($colorname).">", $message);
			}
		} // 색깔
		echo $message."\r\n\r\n";
	}
}
?> 