<?php
/**
 * wechat php test
 */
include("wechat.class.php");
include("hanyun_config.php");
include("../Util/DB/connector.php");


$db = new DBHelper($config);
$db_instance = $db->getInstance();
$QUESTION_TYPE_ID = 1;

//define your token
define("TOKEN", "hanyungufeng"); //thjcj
//$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();

/* $options = array(
 'token'=>'thjcj', //填写你设定的key
 'appid'=>'wx5007aebe43f266ac', //填写高级调用功能的app id, 请在微信开发模式后台查询
 'appsecret'=>'9a1783aac69883afbe0012978fe40155', //填写高级调用功能的密钥
 //'debug'=>true,
 //'logcallback'=>'logdebug',
 // 'partnerid'=>'88888888', //财付通商户身份标识，支付权限专用，没有可不填
 // 'partnerkey'=>'', //财付通商户权限密钥Key，支付权限专用
 //'paysignkey'=>'' //商户签名密钥Key，支付权限专用
 ); */
$options = array(
		'token'=>'hanyungufeng', //填写你设定的key
		'appid'=>'wx5007aebe43f266ac', //填写高级调用功能的app id, 请在微信开发模式后台查询
		'appsecret'=>'9a1783aac69883afbe0012978fe40155', //填写高级调用功能的密钥

);
$weObj = new Wechat($options);
$weObj->valid();
//$menu = $weObj->getMenu();
//var_dump($menu);

function str_split_unicode($str, $l = 0) {
	if ($l > 0) {

		$ret = array();

		$len = mb_strlen($str, "UTF-8");

		for ($i = 0; $i < $len; $i += $l) {

			$ret[] = mb_substr($str, $i, $l, "UTF-8");

		}

		return $ret;

	}

	return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);

}

$rev = $weObj->getRev();
$type = $rev->getRevType();
$fromName = $rev->getRevFrom();
$userInfo = $weObj->getUserInfo($fromName);
switch($type) {

	case Wechat::MSGTYPE_TEXT:

		$content = $rev->getRevContent();
		$strArr = array('恩恩 听君一席话 胜读十年书。ちょっとまって 你说的啥？','松下问童子 言师采药去。 客官 我家主人不在哦，有什么话要我转达咩？');

		if(strpos($content, '你好')!== false || strpos($content, 'hello')!== false){
			$strArr = array('我是人间惆怅客 知君何事泪纵横','同是天涯沦落人 相识何必曾相逢');
		}
		else if(strpos($content, '考试')!== false || strpos($content, '挂科')!== false){
			$strArr = array('春风不改旧时波 祝君今年不挂科','把酒问青天 高数不胜寒');
		}
		else if(strpos($content, '再见')!== false || strpos($content, '拜拜')!== false){
			$strArr = array('此地一为别 孤蓬万里征。。客官 走好','不要啊，没有你，臣妾做不到啊','哼 友尽', '此去经年，应是良辰好景虚设，纵便有千总风情，更与和人说');
		}
		else if(strpos($content, '小二')!== false || strpos($content, '打探')!== false || strpos($content, '更多')!== false){
			$strArr = array('客官 我来啦~ 发送"小二"喊我一声 我就来陪大侠聊天唱曲儿咯。' . "\n"
			. '发送春花雪月等关键字 我就会背诗给你听哦' . "\n" .  '欢迎关注 汉韵古风 微信号 hanyungufeng。 更多信息 请访问<a href="http://121.199.55.129/andy/wechat/yanhe/index.php?id=' . $fromName . '" >汉韵古风</a>');

		}
		else
		{
			//process conversation
			$validTime = strtotime('-2 hour');
			$sql_conversation = "select * from conversation where openid = '$fromName' and status = 1 and timestamp >= $validTime order by timestamp desc limit 1";
			$sql_conversation_rst = $db_instance->query($sql_conversation);
			if($sql_conversation_rst && $rows = $sql_conversation_rst->fetchAll())
			{
				foreach($rows as $row)
				{
					$typeid = $row['typeid'];
					$cid = $row['id'];
					$question = $row['content'];
					if($typeid == $QUESTION_TYPE_ID)
					{
						if($content == '滾蛋' || $content == '滚蛋')
						{
							$response = "切，小氣鬼。。。";
						}
						else {
						$response = "哎呀 真膩害，我怎麼沒想到呢。。";}
						$weObj->text($userInfo['nickname'] . ' ' . $response)->reply();
						if($content == '滾蛋' || $content == '滚蛋')
						{
							$sql_update_talk = "update talk set output = '$content', answererid = '$fromName' where input = '$question'
	 							and output is null";
							$db_instance->exec($sql_update_talk);
						}
						$sql_update_conversation = "update conversation set status = 0 where id = $cid";
						$db_instance->exec($sql_update_conversation);
						exit;

					}
				}
			}
			$isAsk = false;
			//GET FROM TALK
			$sql_talk = "select * from talk where input = '$content' and output is not null";
			$sql_talk_rst = $db_instance->query($sql_talk);
			if($sql_talk_rst && $rows = $sql_talk_rst->fetchAll())
			{
				$strArr = array();
				$isAsk = true;
				 foreach($rows as $row)
				 {
				 	$response = $row['output'];
				 	$strArr[] = $response;
				 }
			}
			else {
				$tempStrArr = array();
				$len = mb_strlen($str, "UTF-8");
				$step = 0;
				if($len > 4 )
				{
					$step = 2;
				}
				$contentArr = str_split_unicode($content, $step);
				$query_select_post = "select distinct p.title, p.content, a.name from post p left join author a on p.authorid = a.id
			 where false ";
				foreach($contentArr as $char)
				{
					$query_select_post .=	" or title like '%" . $char . "%' or content like '%" . $char . "%'";
				}

				$select_post_rst = $db_instance->query($query_select_post);
				if($select_post_rst && $rows = $select_post_rst->fetchAll())
				{
					foreach($rows as $row)
					{
						if(!empty($row))
						{
							$title = $row['title'];
							$author =$row['name'];
							$body = $row['content'];

							$tempStrArr[] = $body . ' ' . $author . '的《' . $title . '》大侠觉得如何？';
							$tempStrArr[] = $body . ' ' . $author . '的《' . $title . '》可道出了客官的心声？';
							$tempStrArr[] = '刚刚一席话 让我想起了 '  . $author . '的《' . $title . '》 ' . $body .  ' 客官可曾读过？';

						}
					}
				}
				if(count($tempStrArr) > 0 )
				{
					$strArr = $tempStrArr;
					$isAsk = true;
				}
			}
			if($isAsk)
			{

				$rand = rand(0,9);
				//randomly reply question
				if($rand < 10)
				{
					//select talk
					$sql_talk = "select input from talk where (output is  null or output = '') and input <> '' order by timestamp desc limit 3";
					$sql_talk_rst = $db_instance->query($sql_talk);
					if($sql_talk_rst && $rows = $sql_talk_rst->fetchAll())
					{
						$row = $rows[array_rand($rows)];
						$questionContent = $row['input'];
						$question = "看客官谈吐不凡 正有一事请教。。 最近有人和小二说 '$questionContent', 在下愚钝 竟无言以对， 客官可否赐教一二？直接回复回答的内容就好咯~發送'滾蛋'結束對話";
						$sql_insert_conversation = "insert into conversation (openid,content,typeid) values ('$fromName','$questionContent',$QUESTION_TYPE_ID)";
						$db_instance->exec($sql_insert_conversation);
					}
						
				}

			}
			else
			{
				$sql_insert_talk = "insert into talk (input) values ('$content')";
				$db_instance->exec($sql_insert_talk);
			}

		}

		$str = $strArr[array_rand($strArr)];
		//ask question
		if(isset($question))
		{
			$str .= "\n\n 。。。。。" . $question;
		}
		$weObj->text($str)->reply();
		
		exit;
		break;
	case Wechat::MSGTYPE_EVENT:
		//$weObj->text("正在响应")->reply();
		$keyArr = $weObj->getRev()->getRevEvent();
		if($keyArr && isset($keyArr['key']) && !empty($keyArr['key']))
		{
			$key = $keyArr['key'];
			switch($key) {
				case 'MENU_KEY_NEWS':
					$weObj->text( "羽书科技最新消息：No news is good news")->reply();
					break;
				default:break;
			}
		}
		if($keyArr && isset($keyArr['event']) && !empty($keyArr['event']))
		{
			$key = $keyArr['event'];
			switch($key) {
				case 'subscribe':
					$weObj->text('欢迎关注汉韵古风，你的关注证明你很有思想。' . "\n" . '发送"小二"或"打探"呼叫小二 获取使用说明。' . "\n" . '更多信息 请访问<a href="http://121.199.55.129/andy/wechat/yanhe/index.php?id=' . $fromName .'">汉韵古风</a>')->reply();
					break;
				case 'unsubscribe':
					$weObj->text("不要啊，没有你，臣妾做不到啊")->reply();
					break;
				default:break;
			}
		}

		break;
	case Wechat::MSGTYPE_IMAGE:
		$weObj->text("恭喜你 都会传图片了")->reply();
		break;
	default:
		$weObj->text("help info")->reply();
}

class wechatCallbackapiTest
{
	public function valid()
	{
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
	}

	public function responseMsg()
	{
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		//extract post data
		if (!empty($postStr)){

			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$keyword = trim($postObj->Content);
			$time = time();
			$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
			if(!empty( $keyword ))
			{
				$msgType = "text";
				$contentStr = "Welcome to wechat world!";
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;
			}else{
				echo "Input something...";
			}

		}else {
			echo "";
			exit;
		}
	}

	private function checkSignature()
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>