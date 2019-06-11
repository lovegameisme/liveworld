<style>
	input{
		padding:5px 10px;
		font-size:18px;
	}
</style>
<?php
$sttFile=$_GET['inputSttFile'];
echo '<button onclick="deleteDatabase('.$sttFile.')" type="button">Delete Database</button>';
//$lines = file("link_detail_".$_GET['inputSttFile'].".txt"); 
$stt=explode('##',file_get_contents('recentPos_'.$sttFile.'.txt'));
echo '<form><input type="text" name="inputUrl" placeholder="URL" value="'.$stt[0].'"/><input type="text" name="inputStt" placeholder="Nth-child" value="'.$stt[4].'"/><input type="text" name="inputSttFile" placeholder="Stt File" value="'.$sttFile.'"/><input type="text" placeholder="Proxy" name="inputProxy" value="'.$stt[1].'"/><input type="text" placeholder="Start Page" name="inputStartPage" value="'.$stt[2].'"/><input type="text" placeholder="End Page" name="inputEndPage" value="'.$stt[3].'"/><input type="text" placeholder="Stop At" name="inputStopAt"/><button style="padding:2px 4px;font-size:18px;" type="button" onclick="getLink(this)">Get</button></form>';
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
<script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
<script>
	var cancelInterval=false;
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
					$(el).parents('form').find('input[name="inputStt"]').val(parseInt($(el).parents('form').find('input[name="inputStt"]').val())+1);
					if($(el).parents('form').find('input[name="inputStt"]').val()==41){
						if($(el).parents('form').find('input[name="inputStartPage"]').val()==$(el).parents('form').find('input[name="inputEndPage"]').val()){
							cancelInterval=true;
						}
						$(el).parents('form').find('input[name="inputStt"]').val(1);
						$(el).parents('form').find('input[name="inputStartPage"]').val(parseInt($(el).parents('form').find('input[name="inputStartPage"]').val())+1);
						//$(el).parents('form').find('input[name="inputEndPage"]').val(parseInt($(el).parents('form').find('input[name="inputEndPage"]').val())+1);
					}
					var timeOutVar=setTimeout(function(){
						if($(el).parents('form').find('input[name="inputStartPage"]').val()<=$(el).parents('form').find('input[name="inputEndPage"]').val()){
							getLink(el);
						}
						if(cancelInterval){
							clearTimeout(timeOutVar);
						}
					},1000);
				}
			},
			error:function(error){
				console.log(error);
				$(el).attr('onclick',$(el).attr('hide-onclick')).removeAttr('hide-onclick');
				for(var i=0;i<$(el).parents('form').find('input').length;i++){
					$($(el).parents('form').find('input')[i]).removeAttr('disabled');
				}
				$(el).parents('form').find('input[name="inputStt"]').val(parseInt($(el).parents('form').find('input[name="inputStt"]').val())+1);
				if($(el).parents('form').find('input[name="inputStt"]').val()==41){
					$(el).parents('form').find('input[name="inputStt"]').val(1);
					$(el).parents('form').find('input[name="inputStartPage"]').val(parseInt($(el).parents('form').find('input[name="inputStartPage"]').val())+1);
					//$(el).parents('form').find('input[name="inputEndPage"]').val(parseInt($(el).parents('form').find('input[name="inputEndPage"]').val())+1);
				}
				setTimeout(function(){
					if($(el).parents('form').find('input[name="inputStartPage"]').val()!=$(el).parents('form').find('input[name="inputStopAt"]').val()){
						getLink(el);
					}
				},1000);
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
				inputTypeMovie:'movies'
			},
			success:function(result){
				if(result>0){
					$('input[name="inputSttFile"]').removeAttr('disabled');
					setTimeout(function(){
						getAllEmbed();
					},500);
				}
				else{
					alert('Complete!');
				}
			},
			error:function(error){
				$('input[name="inputSttFile"]').removeAttr('disabled');
				setTimeout(function(){
					$('#btnGetAll').click();
					//getAllEmbed();
				},500);
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
</script>