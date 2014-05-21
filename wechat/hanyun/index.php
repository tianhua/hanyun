<?php $openid = $_GET['id'];setcookie('OPENID', $openid)?>
<?php include 'includes/settings.php'; ?>
<?php $CURRENT_PAGE = 'index';?>
<?php include 'includes/header.php'; ?>


<div class="container theme-showcase">

	<div class='upper-banner' style='margin-bottom: 15px;'>
		<img src='assets/images/index.jpg' alt='banner'
			style='max-width: 100%;' />
	</div>
	<div class="well" id='notification'>
		<p>
		我家主人昨天责怪我了，既然大家来了，肿么木有留下尊姓大名，他日江湖再见 也好有个照应不是~ 嘿嘿 点击下方链接，
		把客官的雅号载入史册吧。
		<a href="register.php">载入史册</a>
		</p>

	</div>
	<div class="well">
		<p>
		大家好， 欢迎来到我们的小窝。 记得时常来看我哦。发送‘小二’喊我一声 我就会把到这里来的地址
		发给你咯。欢迎关注 汉韵古风 微信号 hanyungufeng
		</p>

	</div>
	

</div>
<!-- /container -->
<script type='text/javascript'>
function higlight(id)
{
$(id).toggleClass('my-highlight');
}

$(document).ready(function() {
	setInterval("higlight('#notification')",1000);
});</script>

<?php include 'includes/footer.php';?>
