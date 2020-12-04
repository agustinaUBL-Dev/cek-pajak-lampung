<?php
/*
 * testing parking 
 */
 include 'simple_html_dom.php';
 
 
 // declare value
 $nomora = 'G';
 $nomorb = 2222;
 $nomorc = 'UT';
 
 // get data 
 $inf = get_info_motor("G 2222 UT");
 $inf = filter_data($inf);
 $inf = html_to_json($inf);
 
 // 
 header('Content-Type: application/json');
 echo ($inf);

 
// get info
function get_info_motor($nomor_full){
	// break to three 
	$np = explode(" ", $nomor_full);
	
	// var 
	$nomora = $np[0];
	$nomorb = $np[1];
	$nomorc = $np[2];
 
	// curl
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"http://dppad.jatengprov.go.id/info-pajak-kendaraan/");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"nomora=$nomora&nomorb=$nomorb&nomorc=$nomorc");  //Post Fields
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$headers = [
		'content-type: application/x-www-form-urlencoded',
		'host: dppad.jatengprov.go.id',
		'origin: http://dppad.jatengprov.go.id',
		'referer: http://dppad.jatengprov.go.id/info-pajak-kendaraan/',
		'user-agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
		'connection: keep-alive',
		'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
		'content-length: 30'
	];

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$server_output = curl_exec ($ch);

	curl_close ($ch);
	
	
	// return
	return  $server_output;
}

function filter_data($data_mentah){
	
	$data = $data_mentah;
	
	// explode by <div id="infohasil">
	$d1 = explode('<div id="infohasil">', $data);
	$d2 = $d1[1];
	
	// explode by </table>
	$d3 = explode('</table>', $d2);
	$d4 = $d3[0].'</table>';
	
	// delete outer from table 
	$d5 = explode('<div class="inforesult">', $d4);
	$d6 = explode(' <div id="head-ipk" class="tit-ipk"><b>INFO PAJAK KENDARAAN</b></div>', $d5[1]);
	$d7 = $d6[1];
	
	// delete <td>:</td>
	$d7 = str_replace('<td>:</td>','',$d7);
	$d7 = str_replace('<td width="20px">:</td>','',$d7);
	$d7 = str_replace(' class="result-pajak"','',$d7);
	$d7 = str_replace('<span style="color:red">*</span>','',$d7);
	
	$data = $d7;
	
	return $data;
}


// html dom to json 
function html_to_json($html_s){
	$html = str_get_html($html_s);
	$row_count=0;
	$json = array();
	foreach ($html->find('tr') as $row) {
			$info = $row->find('td',0)->innertext;
			$value = $row->find('td',1)->innertext;

			$json[$info]=$value;
		}
    return json_encode($json);
}
?>


