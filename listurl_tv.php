<style>
	input{
		padding:5px 10px;
		font-size:18px;
	}
</style>
<title>File TV <?php echo $_GET['inputSttFile']; ?></title>
<?php
//$stt=explode('##',file_get_contents('recentPos.txt'));
echo '<input type="text" value="'.$_GET['inputSttFile'].'" placeholder="Stt File" name="inputSttFile"/><input type="text" placeholder="Proxy" name="inputProxy" value=""/></br>';
echo '<button onclick="if(confirm('."'Do you want confirm Delete?'".')){deleteDatabase('.$_GET['inputSttFile'].')}" type="button" style="margin:10px 0px;font-size:20px;padding:5px 10px;color:white;background:red;box-shadow:5px 10px 15px #ccc;">Delete Database TV</button></br>';
//$lines = file("link_tv_detail.txt"); 
//echo '<form><input type="text" name="inputUrl" placeholder="URL"/><input type="text" placeholder="Proxy" name="inputProxy"/><input type="text" placeholder="Start Page" name="inputStartPage"/><input type="text" placeholder="End Page" name="inputEndPage"/><button style="padding:2px 4px;font-size:18px;" type="button" onclick="getLink(this)">Get</button></form>';
echo '<button type="button" id="btnGetAll" onclick="getAllEmbed()" style="padding:5px 10px;font-size:22px;">Get All Embed</button>';
echo '<button type="button" onclick="igroneLine=false">Next Line</button>';
echo '<div style="border:1px solid #ccc">';
/*
if(count($lines)>0){
	if ($file = fopen("link_tv_detail.txt", "r")) {
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
<a href="link_tv_<?php echo $_GET['inputSttFile']?>.txt" style="margin-right:10px;">link_tv_<?php echo $_GET['inputSttFile']?>.txt</a>
<a href="link_tv_detail_<?php echo $_GET['inputSttFile']?>.txt" style="margin-right:10px;">link_tv_detail_<?php echo $_GET['inputSttFile']?>.txt</a>
</div>
<div class="logs">

</div>
<script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
<script>
var igroneLine=true;
/*
	function getLink(el){
		$(el).attr('hide-onclick',$(el).attr('onclick')).removeAttr('onclick');
		for(var i=0;i<$(el).parents('form').find('input').length;i++){
			$($(el).parents('form').find('input')[i]).attr('disabled','true');
		}
		
		var url=$(el).parents('form').find('input[name="inputUrl"]').val();
		var proxy=$(el).parents('form').find('input[name="inputProxy"]').val();
		var startPage=$(el).parents('form').find('input[name="inputStartPage"]').val();
		var endPage=$(el).parents('form').find('input[name="inputEndPage"]').val();
		$.ajax({
			url:'index.php',
			method:'get',
			data:{
				_token:1,
				inputUrl:url,
				inputProxy:proxy,
				inputStartPage:startPage,
				inputEndPage:endPage
			},
			success:function(result){
				if(result=='success'){
					$(el).attr('onclick',$(el).attr('hide-onclick')).removeAttr('hide-onclick');
					for(var i=0;i<$(el).parents('form').find('input').length;i++){
						$($(el).parents('form').find('input')[i]).removeAttr('disabled');
					}
					$(el).parents('form').find('input[name="inputStartPage"]').val($(el).parents('form').find('input[name="inputEndPage"]').val());
					$(el).parents('form').find('input[name="inputEndPage"]').val(parseInt($(el).parents('form').find('input[name="inputEndPage"]').val())+1);
					setTimeout(function(){
						getLink(el);
					},5000);
				}
			},
			error:function(error){
				console.log(error);
			}
		});
	}
	*/
	function getEmbed(el){
		//console.log($(el).parent().find('span').text().split('##')[1]);
		var arr=$(el).parent().find('span').text().split('##');
		$.ajax({
			url:'index.php',
			method:'get',
			data:{
				inputUrl:arr[2].substr(0,arr[2].length-1),
				inputEpi:arr[1],
				inputId:arr[0],
				inputEmbed:true,
				inputTypeMovie:'tv',
				inputOgirinal:$(el).parent().find('span').text(),
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
	var i=0;
	function getAllEmbed(){
		
		/*
		for(var i=0;i<$('.btnGetEmbed').length;i++){
			$('.btnGetEmbed')[i].click();
		}
		*/
		$('input[name="inputSttFile"]').attr('disabled','true');
		$.ajax({
			method:'get',
			url:'index.php',
			data:{
				inputGetEmbed:$('input[name="inputSttFile"]').val(),
				inputProxy:$('input[name="inputProxy"]').val(),
				inputTypeMovie:'tv',
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
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:green;padding:10px 20px;">SUCCESS </span>'+Date(Date.now())+'- Get link Embed from '+(parseInt(result)+1)+' line - <b>FILE: <a href="link_tv_detail_<?php echo $_GET['inputSttFile'];?>.txt">link_tv_detail_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
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
				$('.logs').prepend('<p style="padding:15px 10px;border:1px solid #ccc"><span style="color:white;background:red;padding:10px 20px;">ERROR </span>'+Date(Date.now())+'- '+error.status+' '+error.statusText+'-Get link Embed from - <b>FILE: <a href="link_tv_detail_<?php echo $_GET['inputSttFile']; ?>.txt">link_tv_detail_<?php echo $_GET['inputSttFile']; ?>.txt</a></b></p>');
			}
		});
		i++;
	}
	function deleteDatabase(file){
		$.ajax({
			method:'get',
			url:'index.php',
			data:{
				inputConfirm:true,
				inputTypeMovie:'tv',
				sttFile:file
			},
			success:function(result){
				window.location.href='listurl_tv.php';
			},
			error:function(error){
				console.log(error);
			}
		});
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