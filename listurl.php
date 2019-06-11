<style>
	input{
		padding:5px 10px;
		font-size:18px;
	}
</style>
<title>File <?php echo $_GET['inputSttFile'] ?></title>
<?php
$sttFile=$_GET['inputSttFile'];
echo '<button onclick="if(confirm('."'Do you confirm Delete ALL DATABASE?'".')){deleteDatabase('.$sttFile.')}" type="button" style="border:1px solid #ccc;padding:10px;box-shadow:5px 5px 10px #cccc;background:red;color:white;">Delete Database</button>';
//$lines = file("link_detail_".$_GET['inputSttFile'].".txt"); 
$stt=explode('##',file_get_contents('recentPos_'.$sttFile.'.txt'));
echo '<form><input type="text" name="inputUrl" placeholder="URL" value="'.$stt[0].'"/><input type="text" name="inputStt" placeholder="Nth-child" value="'.$stt[4].'"/><input type="text" name="inputSttFile" placeholder="Stt File" value="'.$sttFile.'"/><input type="text" placeholder="Proxy" name="inputProxy" value="'.$stt[1].'"/><input type="text" placeholder="Start Page" name="inputStartPage" value="'.$stt[2].'"/><input type="text" placeholder="End Page" name="inputEndPage" value="'.$stt[3].'"/><input type="text" placeholder="Stop At" name="inputStopAt"/><button style="padding:2px 4px;font-size:18px;" type="button" onclick="getLink(this)">Get URL</button></form>';
echo '<div style="margin:10px 0px;"><button type="button" onclick="generatorInfo(this)" style="padding:5px 10px;font-size:18px;">Get Info from URL</button><button onclick="igroneLine=false" style="padding:5px 10px;font-size:18px;margin-left:10px;">Next Line</button><button style="margin-left:10px;padding:5px 10px;font-size:18px;" onclick="stopAlive=true">Stop Alive</button></br></div>';
echo '<button type="button" id="btnGetAll" onclick="getAllEmbed()" style="padding:5px 10px;font-size:22px;">Get All Embed</button>';
echo '<div style="border:1px solid #ccc">';
/*
if(count($lines)>0){
	if ($file = fopen("link_detail_".$_GET['inputSttFile'].".txt", "r")) {
		while(!feof($file)) {
			$line = fgets($file);
			if($line!=''){
				echo '<p><span>'.$line.'</span><button onclick="getEmbed(this)" class="btnGetEmbed">Get Embed</button></p>';
			}
			# do same stuff with the $line
		}
		fclose($file);
	}
}
*/
echo '</div>';
?>
<div>
<a href="link<?php echo $_GET['inputSttFile'];?>.txt" style="margin-right:10px;">link<?php echo $_GET['inputSttFile'];?>.txt</a>
<a href="link_detail_<?php echo $_GET['inputSttFile'];?>.txt" style="margin-right:10px;">link_detail_<?php echo $_GET['inputSttFile'];?>.txt</a>
<a href="url_<?php echo $_GET['inputSttFile'];?>.txt" style="margin-right:10px;">url_<?php echo $_GET['inputSttFile'];?>.txt</a>
</div>
<div class="logs">

</div>
<script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
<script>
	var igroneLine=true;
	var cancelInterval=false;
	var stopAlive=false;
	function getLink(el){
		$(el).attr('hide-onclick',$(el).attr('onclick')).removeAttr('onclick');
		for(var i=0;i<$(el).parents('form').find('input').length;i++){
			$($(el).parents('form').find('input')[i]).attr('disabled','true');
		}
		
		var url=$(el).parents('form').find('input[name="inputUrl"]').val();
		var proxy=$(el).parents('form').find('input[name="inputProxy"]').val();
		var startPage=$(el).parents('form').find('input[name="inputStartPage"]').val();
		var endPage=$(el).parents('form').find('input[name="inputEndPage"]').val();
		var stt=$(el).parents('form').find('input[name="inputStt"]').val();
		var sttFile=$(el).parents('form').find('input[name="inputSttFile"]').val();
		$.ajax({
			url:'index.php',
			method:'get',
			data:{
				_token:1,
				inputUrl:url,
				inputProxy:proxy,
				inputStartPage:startPage,
				inputEndPage:endPage,
				inputStt:stt,
				inputSttFile:sttFile
			},
			success:function(result){
				if(result=='success'){
					$(el).attr('onclick',$(el).attr('hide-onclick')).removeAttr('hide-onclick');
					for(var i=0;i<$(el).parents('form').find('input').length;i++){
						$($(el).parents('form').find('input')[i]).removeAttr('disabled');
					}
					$(el).parents('form').find('input[name="inputStartPage"]').val(parseInt($(el).parents('form').find('input[name="inputStartPage"]').val())+1);
					if($(el).parents('form').find('input[name="inputStartPage"]').val()-1==$(el).parents('form').find('input[name="inputEndPage"]').val()){
						alert('Complete Get URL!');
					}
					var timeOutVar=setTimeout(function(){
						if(parseInt($(el).parents('form').find('input[name="inputStartPage"]').val())<=parseInt($(el).parents('form').find('input[name="inputEndPage"]').val())){
							getLink(el);
						}
						else{
							clearTimeout(timeOutVar);
							var keepAlive=setInterval(function(){
								keepAliveServer();
								if(stopAlive==true){
									clearInterval(keepAlive);
								}
							},900);
						}
					},1000);
				}
				if($('.logs').find('p').length>50){
					$('.logs').find('p')[50].remove();
				}
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:green;padding:10px 20px;">SUCCESS </span>'+Date(Date.now())+'- Get URL to <b>FILE: <a href="url_<?php echo $_GET['inputSttFile']; ?>.txt">url_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
			},
			error:function(error){
				console.log(error);
				if($('.logs').find('p').length>50){
					$('.logs').find('p')[50].remove();
				}
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:red;padding:10px 20px;">ERROR </span>'+Date(Date.now())+'- '+error.status+' '+error.statusText+'-Get URL to <b>FILE: <a href="url_<?php echo $_GET['inputSttFile']; ?>.txt">url_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
			}
		});
	}
	function getEmbed(el){
		//console.log($(el).parent().find('span').text().split('##')[1]);
		var arr=$(el).parent().find('span').text().split('##');
		$.ajax({
			url:'index.php',
			method:'get',
			data:{
				inputUrl:arr[1].substr(0,arr[1].length-1),
				inputId:arr[0],
				inputEmbed:true,
				inputOgirinal:$(el).parent().find('span').text(),
				inputSttFile:$('input[name="inputSttFile"]').val(),
				inputProxy:$('input[name="inputProxy"]').val()
			},
			success:function(result){
				$(el).parent('p').remove();
			},
			error:function(error){
				console.log(error);
			}
		});
	}
	var x=0;
	function getAllEmbed(){
		$('input[name="inputSttFile"]').attr('disabled','true');
		/*
		for(var i=0;i<$('.btnGetEmbed').length;i++){
			$('.btnGetEmbed')[i].click();
		}
		*/
		$.ajax({
			method:'get',
			url:'index.php',
			data:{
				inputGetEmbed:$('input[name="inputSttFile"]').val(),
				inputProxy:$('input[name="inputProxy"]').val(),
				inputTypeMovie:'movies',
				igroneLine:igroneLine
			},
			success:function(result){
				if(igroneLine==false){
					igroneLine=true;
				}
				if(result>0){
					$('input[name="inputSttFile"]').removeAttr('disabled');
					setTimeout(function(){
						getAllEmbed();
					},500);
				}
				else{
					alert('Complete!');
					var keepAlive=setInterval(function(){
						keepAliveServer();
						if(stopAlive==true){
							clearInterval(keepAlive);
						}
					},900);
				}
				if($('.logs').find('p').length>50){
					$('.logs').find('p')[50].remove();
				}
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:green;padding:10px 20px;">SUCCESS </span>'+Date(Date.now())+'- Get link Embed from '+(parseInt(result)+1)+' line - <b>FILE: <a href="link_detail_<?php echo $_GET['inputSttFile']; ?>.txt">link_detail_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
			},
			error:function(error){
				if(igroneLine==false){
					igroneLine=true;
				}
				$('input[name="inputSttFile"]').removeAttr('disabled');
				setTimeout(function(){
					$('#btnGetAll').click();
					//getAllEmbed();
				},500);
				if($('.logs').find('p').length>50){
					$('.logs').find('p')[50].remove();
				}
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:red;padding:10px 20px;">ERROR </span>'+Date(Date.now())+'- '+error.status+' '+error.statusText+'Get link Embed from - <b>FILE: <a href="link_detail_<?php echo $_GET['inputSttFile']; ?>.txt">link_detail_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
			}
		});
	}
	function deleteDatabase(file){
		$.ajax({
			method:'get',
			url:'index.php',
			data:{
				inputConfirm:true,
				sttFile:file
			},
			success:function(result){
				window.location.href='listurl.php';
			},
			error:function(error){
				console.log(error);
			}
		});
	}
	function generatorInfo(el){
		//$(el).attr('hide-onclick','generatorInfo(this)').removeAttr('onclick');
		for(var i=0;i<$('form').find('input').length;i++){
			$($('form').find('input')[i]).attr('disabled','true');
		}
		$.ajax({
			url:'index.php',
			method:'get',
			data:{
				sttFile:$('input[name="inputSttFile"]').val(),
				getInfo:true,
				inputProxy:$('input[name="inputProxy"]').val(),
				igroneLine:igroneLine
			},
			success:function(result){
				if(igroneLine==false){
					igroneLine=true;
				}
				if(parseInt(result)>0){
					//$(el).attr('onclick','generatorInfo(this)').removeAttr('hide-onclick');
					setTimeout(function(){
						generatorInfo();
					},1000);
				}
				else{
					alert('Complete get info From URL');
					var keepAlive=setInterval(function(){
						keepAliveServer();
						if(stopAlive==true){
							clearInterval(keepAlive);
						}
					},900);
				}
				for(var i=0;i<$('form').find('input').length;i++){
					$($('form').find('input')[i]).removeAttr('disabled');
				}
				if($('.logs').find('p').length>50){
					$('.logs').find('p')[50].remove();
				}
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:green;padding:10px 20px;">SUCCESS </span>'+Date(Date.now())+'- Generator info from '+(parseInt(result)+1)+' line - <b>FILE: <a href="url_<?php echo $_GET['inputSttFile']; ?>.txt"> url_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
			},
			error:function(error){
				if(igroneLine==false){
					igroneLine=true;
				}
				console.log(error);
				setTimeout(function(){
					generatorInfo();
				},1000);
				if($('.logs').find('p').length>50){
					$('.logs').find('p')[50].remove();
				}
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:red;padding:10px 20px;">ERROR </span>'+Date(Date.now())+'- '+error.status+' '+error.statusText+'Generator info from - <b>FILE: <a href="url_<?php echo $_GET['inputSttFile']; ?>.txt"> url_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
			}
		})
	}
	function keepAliveServer(){
		$.ajax({
			method:'get',
			url:'index.php',
			data:{
				keepAliveServer:true
			},
			success:function(result){
				//console.log(result);
			},
			error:function(error){
				console.log(error);
			}
		})
	}
</script>