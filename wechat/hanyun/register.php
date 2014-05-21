<?php include 'includes/settings.php';
include '../../Util/DB/connector.php';
include '../hanyun_config.php';
$openid = isset($_COOKIE['OPENID']) ? $_COOKIE['OPENID'] : 0;
?>
<?php $CURRENT_PAGE = 'index';?>
<?php //var_dump($_POST);
if(isset($_POST['username']) && isset($_POST['gender'])&& isset($_POST['day']) && isset($_POST['year'])&& isset($_POST['month']))
{
	$username = $_POST['username'];
	$gender = $_POST['gender'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$time = strtotime("$month/$day/$year");
    $birthday = date('Y-m-d',$time);
	$db = new DBHelper($config);
    $db_instance = $db->getInstance();
    $sql_insert_user = "insert into user (name,gender,birthday,openid) values 
    ('$username', '$gender','$birthday', '$openid');";
    
    $db_instance->exec($sql_insert_user);
    $uid = $db_instance->lastInsertId ();
    setcookie('uid',$uid);
    echo "<div>$username " . ($gender == 'male' ? "壯士" : "女俠"). "，從此江湖中有了你的傳說。。</div>";
    
    //header("location:index.php");
}
else 
{
	if(isset($_POST['postback']))
	{
	  echo "<div>客官，貌似信息不太全啊。莫非您是官府通緝的江洋大盜？。。</div>";
	}
}
?>
<?php include 'includes/header.php'; ?>
<?php if(!isset($_POST['postback']) || (isset($uid) && $uid)){?>
<div class="container theme-showcase">
	<form class="form-horizontal" id='register' method='POST'>
	<input type='hidden' name='postback' value='1' />
    <fieldset>
      <div id="legend" class="">
        <legend class="">載入史冊</legend>
    <div class="control-group">
          <!-- Text input-->
          <label class="control-label" for="username">客官雅号</label>
          <div class="controls">
            <input type="text" placeholder="尊姓大名" class="input-xlarge" name='username' id='username'>
          </div>

          <!-- Select Basic -->
          <label class="control-label">一決雌雄</label>
          <div class="controls">
            <select class="input-xlarge" name='gender' id='gender'>
              <option value=''></option>
              <option value='male'>壯士</option>
              <option value='female'>女俠</option>
            </select>
          </div>

		<label class="control-label">生辰八字</label>
          <div class="controls">
            <select class="input-xlarge" name='year' id='year'>
            <option value=''></option>
              <?php for($i = 1970 ; $i < 2014 ; $i++)
              {
              	echo "<option value='$i'>$i</option>";
              }?>
            </select>
            <select class="input-xlarge" name='month' id='month'>
            <option value=''></option>
              <?php for($i = 1 ; $i < 13 ; $i++)
              {
              	echo "<option value='$i'>$i</option>";
              }?>
            </select>
            <select class="input-xlarge" name='day' id='day'>
            <option value=''></option>
              <?php for($i = 1 ; $i < 32 ; $i++)
              {
              	echo "<option value='$i'>$i</option>";
              }?>
            </select>
          </div>
    </div>
        
    <div class="control-group">
          <!-- Button -->
          <div class="controls">
            <button id='submit' class="btn btn-success">踏入江湖</button>
          </div>
        </div>
	</div>
    </fieldset>
  </form>

</div>
<!-- /container -->

<script type='text/javascript'>
function validate(event)
{
	
	var isValid = true;
	
  if(!$('#username').val())
  {
	  $('#username').addClass('my-error');
	  isValid = false;
	  }
  else
  {
	  $('#username').removeClass('my-error');
	  }
  if(!$('#gender').val())
  {
	  $('#gender').addClass('my-error');
	  isValid = false;
	  }
  else
  {
	  $('#gender').removeClass('my-error');
	  }
  if(!$('#year').val())
  {
	  $('#year').addClass('my-error');
	  isValid = false;
	  }
  else
  {
	  $('#year').removeClass('my-error');
	  }
  if(!$('#month').val())
  {
	  $('#month').addClass('my-error');
	  isValid = false;
	  }
  else
  {
	  $('#month').removeClass('my-error');
	  }
  if(!$('#day').val())
  {
	  $('#day').addClass('my-error');
	  isValid = false;
	  }
  else
  {
	  $('#day').removeClass('my-error');
	  }
  if(isValid)
  {
	  $('#register').submit();
	  }
  else
  {
	  event.preventDefault();
	  }
}

$(document).ready(function() {
	$('#submit').click(function(e){
		validate(e);
		});
});</script>
<?php }?>
<?php include 'includes/footer.php';?>
