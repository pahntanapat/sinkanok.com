<?php
ob_start();
//ini_set('display_errors', '1');
require_once("simple_html_dom.php");
header($_SERVER['SERVER_PROTOCOL']." 200 OK",true,200);
if(isset($_REQUEST['action']))
	switch($_REQUEST['action']){
		/*
		case 'setcookie':
			setcookie('sinkanok-ajax','yes',time()+3600*24*30);
			if(!isset($_REQUEST['ajax'])) header("location: /ajax.php");
		case 'getcookie':
			if(isset($_COOKIE['sinkanok-ajax']))
				echo $_COOKIE['sinkanok-ajax'];
			break;
		case 'clearcookie':
			setcookie('sinkanok-ajax',NULL,time()-3600*24*30);
			unset($_COOKIE['sinkanok-ajax']);
			break;
		*/
		case 'loadpage':
			if($_REQUEST['page']==NULL) $_POST['page']="home.php";
			$c=ob_get_clean();
			$p=@file_get_contents("caching/$_POST[page].json");
			if($p===false){
				$p=readDOMtoJSON($_POST['page']);
				if($p==false) header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
				if(writeCaching($_POST['page'],$p)===false) header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			}else{
				$json=json_decode($p,true);
				if(filemtime($_POST['page'])>$json['modified'] || time()-filemtime($_POST['page'])>3600*24*90 ){
					$p=readDOMtoJSON($_POST['page']);
					if(writeCaching($_POST['page'],$p)===false) header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
				}
				unset($json);
			}
			ob_end_clean();ob_start(); echo $c;unset($c);
			echo $p;
			header('Content-type: text/html',true);
			break;
		default: //loadpage
			header("location: http://sinkanok.com");
	}
ob_end_flush();
function readDOMtoJSON($filepath){
	$html = file_get_html($filepath);
	$assoc['modified']=filemtime($filepath);
	
	$e=$html->find("title",0);$assoc['pageTitle']=$e->innertext;
	$e=$html->find("#title",0);$assoc['title']=$e->innertext;
	$e=$html->find("#article",0);$assoc['article']=$e->innertext;
	unset($dom,$filepath);
	return json_encode($assoc);
}
/*function JSONtoTag($json){
	$tag=json_decode($json);
	return <<<HTML
<title>$assoc[pageTitle]</title>

<div class="content">
<div id="title">$tag[title]</div>
<hr>
<div id="article">$tag[article]</div>

<div class="center"><script type="text/javascript"><!--
google_ad_client = "ca-pub-5867850403191105";
// 468x60, ถูกสร้างขึ้นแล้ว 10/24/08 
google_ad_slot = "1116775658";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>
<div class="right"><a href="#" title="go to top">go to top</a></div>
</div>
	HTML;
}*/
function writeCaching($filename,$str){
	$fp=@fopen("caching/$filename.json","w+");
	if($fp===NULL) return $fp;
	$r=fwrite($fp,$str);
	fclose($fp);
	return $r;
}
?>