<?php
require ('simple_html_dom.php');
	$headers[]  = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36";

if(isset($_GET['_token']) && isset($_GET['inputUrl'])){
	$sttFile=$_GET['inputSttFile'];
	$text='';
	$text1='';
	$startPage=$_GET['inputStartPage'];
	$endPage=$_GET['inputEndPage'];
	$type='href';
	$element='.ml-mask.jt';
	$url=$_GET['inputUrl'];
	//$proxy = '119.190.34.70:80';
	$proxy=$_GET['inputProxy'];
	//$stt=$_GET['inputStt']-1;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url.'page-'.$startPage.'.html');
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($curl, CURLOPT_POST, 0);
	if($proxy!=''){
		curl_setopt($curl,CURLOPT_PROXY,$proxy);
	}
	$data = curl_exec($curl);
	$html=str_get_html($data);
	$stringHref='';
	foreach($html->find('a.ml-mask.jt') as $i=>$ahref){
		$stringHref.=$ahref->href. PHP_EOL;
	}
	$myfile = fopen("url_".$sttFile.".txt", "a");
	fwrite($myfile,$stringHref);
	fclose($myfile);
    echo 'success';
}
else if(isset($_GET['inputEmbed'])){
	if(isset($_GET['inputTypeMovie'])=='tv'){
		$id=$_GET['inputId'];
		$url=$_GET['inputUrl'];
		$epiName=$_GET['inputEpi'];
		$proxy=$_GET['inputProxy'];
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		if($proxy!=''){
			curl_setopt($curl,CURLOPT_PROXY,$proxy);
		}
		$data1 = curl_exec($curl);
		curl_close($curl);
		$html1=str_get_html($data1);
		$embed='';
		if(isset($html1->find('#media-player>script')[0])){
			preg_match('/\"(.*?)\"/',$html1->find('#media-player>script')[0]->innertext,$str);
			$embed=str_get_html(base64_decode($str[1]))->getElementsByTagName('iframe')->getAttribute('src');
		}
		else{
			$embed=$html1->find('#media-player>center')[0]->first_child ()->href;
		}
		$data='';
		$f = fopen('link_tv.txt','r');
		while ($line = fgets($f)) {
			if(strpos($line,$id) && strpos($line,$epiName.' #EPISODES#')){
				$data =$data. str_replace(' #EMBEDS#','',substr($line,0,strlen($line)-2)).' !! '.$embed.' #EMBEDS#'.PHP_EOL;
			}
			else{
				$data =$data.$line;
			}
		}

		fclose($f);
		$f = fopen('link_tv.txt','w');
		fwrite($f, $data);
		fclose($f);
		
		$data='';
		$f = fopen('link_tv_detail.txt','r');
		while ($line = fgets($f)) {
			//$emailWithComma = $line . ",";

			//check if email marked to remove
			if($line==$id.'##'.$url.PHP_EOL)
				continue;

			$data =$data. $line;
		}

		fclose($f);
		$f = fopen('link_tv_detail.txt','w');
		fwrite($f, $data);
		fclose($f);
	}
	else{
		$id=$_GET['inputId'];
		$url=$_GET['inputUrl'];
		$proxy=$_GET['inputProxy'];
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		if($proxy!=''){
			curl_setopt($curl,CURLOPT_PROXY,$proxy);
		}
		$data1 = curl_exec($curl);
		curl_close($curl);
		$html1=str_get_html($data1);
		$embed='';
		if(isset($html1->find('#media-player>script')[0])){
			preg_match('/\"(.*?)\"/',$html1->find('#media-player>script')[0]->innertext,$str);
			$embed=str_get_html(base64_decode($str[1]))->getElementsByTagName('iframe')->getAttribute('src');
			//echo '<p>'.$e1->href.' __ '.$embed.'</p>';
		}
		else{
			$embed=$html1->find('#media-player>center')[0]->first_child ()->href;
			//echo $html1->find('#media-player>center')[0]->first_child ()->href.'</br>';
		}
		/*
		$myfile1 = fopen("embed.txt", "a");
		$txt1 = $id.'##'.$embed.PHP_EOL;
		fwrite($myfile1, $txt1);
		fclose($myfile1);
		*/
		
		$data='';
		$f = fopen('link.txt','r');
		while ($line = fgets($f)) {
			//$emailWithComma = $line . ",";
			//check if email marked to remove
			if(strpos($line,$id)===false){
				$data =$data.$line;
			}
			else{
				$data =$data. str_replace(' #EXTRACTEMBED#','',substr($line,0,strlen($line)-2)).' !! '.$embed.' #EXTRACTEMBED#'.PHP_EOL;
			}
		}

		fclose($f);
		$f = fopen('link.txt','w');
		fwrite($f, $data);
		fclose($f);
		
		$data='';
		$f = fopen('link_detail.txt','r');
		while ($line = fgets($f)) {
			//$emailWithComma = $line . ",";

			//check if email marked to remove
			if($line==$id.'##'.$url.PHP_EOL)
				continue;

			$data =$data. $line;
		}

		fclose($f);
		$f = fopen('link_detail.txt','w');
		fwrite($f, $data);
		fclose($f);
	}
}
else if(isset($_GET['inputConfirm'])){
	$sttFile=$_GET['sttFile'];
	if($_GET['inputTypeMovie']=='tv'){
		unlink('link_tv_'.$sttFile.'.txt');
		unlink('link_tv_detail_'.$sttFile.'.txt');
	}
	else{
		unlink('link'.$sttFile.'.txt');
		unlink('link_detail_'.$sttFile.'.txt');
		unlink('link_tv_detail_'.$sttFile.'.txt');
		unlink('link_tv_'.$sttFile.'.txt');
	}
	unlink('url_'.$sttFile.'.txt');
	unlink('recentPos_'.$sttFile.'.txt');
}
else if(isset($_GET['inputGetEmbed'])){
	$igroneLine=$_GET['igroneLine'];
	$file=$_GET['inputGetEmbed'];
	if($_GET['inputTypeMovie']=='movies'){
		$f = fopen('link_detail_'.$file.'.txt','r');
		$fx='link'.$file.'.txt';
		$fx1='link_detail_'.$file.'.txt';
	}
	else{
		$f = fopen('link_tv_detail_'.$file.'.txt','r');
		$fx='link_tv_'.$file.'.txt';
		$fx1='link_tv_detail_'.$file.'.txt';
	}
	
	$i=0;
	$strText='';
	$aimLine='';
	while ($line = fgets($f)) {
		if($i==0){
			$aimLine=$line;
		}
		else{
			$strText.=$line;
		}
		$i++;
	}
	fclose($f);
	$f1 = fopen($fx1,'w');
	fwrite($f1, $strText);
	fclose($f1);
	if($_GET['inputTypeMovie']=='tv'){
		$id=explode('##',$aimLine)[0];
		$url=explode('##',$aimLine)[2];
		//$url= substr($url,0,strlen($url)-1);
		preg_match('/http\:.*?\.html/',$url,$finalURL);
		$url=$finalURL[0];
		$epiName=explode('##',$line)[1];
	}
	else{
		$id=explode('##',$aimLine)[0];
		$url=explode('##',$aimLine)[1];
		//$url= substr($url,0,strlen($url)-1);
		preg_match('/http\:.*?\.html/',$url,$finalURL);
		$url=$finalURL[0];
	}
	
	
	$lines = file($fx1); 
	echo count($lines);
	
	$proxy=$_GET['inputProxy'];
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	if($proxy!=''){
		curl_setopt($curl,CURLOPT_PROXY,$proxy);
	}
	$data1 = curl_exec($curl);
	curl_close($curl);
	$html1=str_get_html($data1);
	$embed='';
	if($html1->find('#media-player')){
		if(isset($html1->find('#media-player>script')[0])){
			preg_match('/\"(.*?)\"/',$html1->find('#media-player>script')[0]->innertext,$str);
			if(strpos(base64_decode($str[1]),'iframe')){
				$embed=str_get_html(base64_decode($str[1]))->getElementsByTagName('iframe')->getAttribute('src');
			}
		}
		else{
			$embed=$html1->find('#media-player>center')[0]->first_child ()->href;
		}
	}
	
	$data='';
	$f1 = fopen($fx,'r');
	while ($line1 = fgets($f1)) {
		if($_GET['inputTypeMovie']=='tv'){
			if(strpos($line1,$id) && strpos($line1,$epiName.' #EPISODES#')){
				$data =$data. str_replace(' #EMBEDS#','',substr($line1,0,strlen($line1)-2)).' !! '.$embed.' #EMBEDS#'.PHP_EOL;
			}
			else{
				$data =$data.$line1;
			}
		}
		else{
			if(strpos($line1,$id)===false){
				$data =$data.$line1;
			}
			else{
				$data =$data. str_replace(' #EXTRACTEMBED#','',substr($line1,0,strlen($line1)-2)).' !! '.$embed.' #EXTRACTEMBED#'.PHP_EOL;
			}
		}
	}

	fclose($f1);
	$f1 = fopen($fx,'w');
	fwrite($f1, $data);
	fclose($f1);
				
}
else if(isset($_GET['curl'])){
	$curl = curl_init();
	$proxy=$_GET['inputProxy'];
	curl_setopt($curl, CURLOPT_URL, $_GET['curl']);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($curl, CURLOPT_POST, 0);
	if($proxy!=''){
		curl_setopt($curl,CURLOPT_PROXY,$proxy);
	}
	$data = curl_exec($curl);
	$html=str_get_html($data);
	echo $data;
}
else if(isset($_GET['getInfo']) && isset($_GET['sttFile'])  && isset($_GET['inputProxy'])){
	$sttFile=$_GET['sttFile'];
	$f = fopen('url_'.$sttFile.'.txt','r');
	$proxy=$_GET['inputProxy'];
	$i=0;
	$text='';
	$text1='';
	$text2='';
	$aimLine='';
	$igroneLine=$_GET['igroneLine'];
	$strLine='';
	$f = fopen('url_'.$sttFile.'.txt','r');
	while ($line = fgets($f)) {
		if($i==0){
			$aimLine=$line;
		}
		else{
			$strLine.=$line;
		}
		$i++;
	}
	fclose($f);
	$f = fopen('url_'.$sttFile.'.txt','w');
	fwrite($f, $strLine);
	fclose($f);
	$f = fopen('url_'.$sttFile.'.txt','r');
	preg_match('/http\:.*?\.html/',$aimLine,$finalURL);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL,$finalURL[0]);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($curl, CURLOPT_POST, 0);
	if($proxy!=''){
		curl_setopt($curl,CURLOPT_PROXY,$proxy);
	}
	$data = curl_exec($curl);
	$html=str_get_html($data);
	preg_match('/url\((.*?)\)/',$html->find('.mvi-content>.thumb.mvic-thumb')[0]->getAttribute('style'),$poster);
	$poster=$poster[1];
	$title=$html->find('.mvi-content>.mvic-desc>h3')[0]->innertext;
	if(!$html->find('#details',0)){
		$description=$html->find('.mvi-content>.mvic-desc>.desc')[0]->innertext;
		$genre=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[0]->childNodes(1)->innertext;
		$actor=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[1]->childNodes(1)->innertext;
		$director=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[2]->childNodes(1)->innertext;
		$country=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[3]->childNodes(1)->innertext;
		$release=explode('</strong>',$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-right>p')[3]->innertext)[1];
		$arr[$aimLine]=[];
		$txt=$release.' #YEAR# '.$title.' #TITLE# '.$description.' #DESCRIPTION# '.$genre.' #GENRES# '.$actor.' #ACTORS# '.$director.' #DIRECTOR# '.$country.' #COUNTRIES# '.' #MOVIE#   #PROD#   #DURATION# '.$release.' #RELEASE# '.$poster.' #IMAGES# '.PHP_EOL;
		$text.=$txt;
		$myfile = fopen("link".$sttFile.".txt", "a");
		fwrite($myfile, $txt);
		fclose($myfile);
		foreach($html->find('.server_line>.server_version>a') as $y=>$e1){
			if($y<=40){
				$text1.= $title.'##'.$e1->href.PHP_EOL;
				$myfile1 = fopen("link_detail_".$sttFile.".txt", "a");
				fwrite($myfile1, $title.'##'.$e1->href.PHP_EOL);
				fclose($myfile1);
				
			}
		}
	}
	else{
		$description=$html->find('.mvi-content>.mvic-desc>.desc')[0]->innertext;
		$genre=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[0]->childNodes(1)->innertext;
		$actor=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[1]->childNodes(1)->innertext;
		$director=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[2]->childNodes(1)->innertext;
		$country=$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-left>p')[3]->childNodes(1)->innertext;
		$release=explode('</strong>',$html->find('.mvi-content>.mvic-desc>.mvic-info>.mvici-right>p')[3]->innertext)[1];	
		$epi=$html->find('.episode.episode_series_link');
		foreach($epi as $c=>$ep){
			$nameEp=$ep->innertext;
			$txt=$release.' #YEAR# '.$title.' #TITLE# #SEASON# '.$nameEp.' #EPISODES# '.$description.' #DESCRIPTION# '.$genre.' #GENRES# '.$actor.' #ACTORS# '.$director.' #DIRECTOR# '.$country.' #COUNTRIES# '.' #MOVIE#   #PROD#   #DURATION# '.$release.' #RELEASE# '.$poster.' #IMAGE# '.PHP_EOL;
			$myfile = fopen("link_tv_".$sttFile.".txt", "a");
			fwrite($myfile, $txt);
			fclose($myfile);
			$curl1 = curl_init();
			curl_setopt($curl1, CURLOPT_URL, $ep->href);
			curl_setopt($curl1, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($curl1, CURLOPT_ENCODING, "gzip");
			curl_setopt($curl1, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl1, CURLOPT_FOLLOWLOCATION, 1);
			if($proxy!=''){
				curl_setopt($curl1,CURLOPT_PROXY,$proxy);
			}
			$data1 = curl_exec($curl1);
			curl_close($curl1);
			$html1=str_get_html($data1);
			foreach($html1->find('.server_line>.server_version>a') as $y=>$e1){
				if($y<=40){
					$myfile1 = fopen("link_tv_detail_".$sttFile.".txt", "a");
					fwrite($myfile1, $title.'##'.$nameEp.'##'.$e1->href.PHP_EOL);
					fclose($myfile1);
					
				}
			}
		}
	}
	$lines = file('url_'.$sttFile.'.txt'); 
	echo count($lines);
}
else if($_GET['keepAliveServer']==true){
	
}
?>