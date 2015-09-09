<?php
$page_title = '留言板';
include('includes/header.html');    //头文件
 
//包含上一级目录的数据库连接文件
require_once('../mysqli_connect.php');
 
if(isset($_POST['submitted']))
{
    //获取用户的输入
    $n = mysqli_real_escape_string($dbc, trim($_POST['inputName']));
    $e = mysqli_real_escape_string($dbc, trim($_POST['inputEmail']));
    $c = mysqli_real_escape_string($dbc, trim($_POST['inputComment']));
     
    //插入语句
    $q = "insert into comment_list(name, email, comment, comment_date) values('$n', '$e', '$c', now())";
    //执行sql语句，同样需要在前头加上 @ 符号
    $r = @mysqli_query($dbc, $q);
}

echo '<div class="starter-template">
		<h1>留言板</h1>
		</div>
		<div class="col-lg-6">';
	  
$display = 3;	//每页留言数目

if(isset($_GET['p']) AND is_numeric($_GET['p']))//获得总页数
{
	$pages = $_GET['p'];
}
else
{
	$q = "select count(id) from comment_list";
	$r = @mysqli_query($dbc, $q);
	$row = @mysqli_fetch_array($r, MYSQLI_NUM);	//从结果集$r得到数字数组
	$record = $row[0];							//$row[0]即为count(id)
	$pages = ceil($record / $display);			//计算总页数，ceil函数向上舍入为最接近的整数
}
if(isset($_GET['s']) && is_numeric($_GET['s']))//获得起始留言编号
{
	$start = $_GET['s'];
}
else
{
	$start = 0;		//如果首次载入页面，则起始编号为0
}

$q = "select name, comment, DATE_FORMAT(comment_date, '%M %d, %Y')
	as dr from comment_list order by dr desc limit $start, $display";
$r = @mysqli_query($dbc, $q);

while($row = mysqli_fetch_array($r, MYSQLI_ASSOC))//从结果集$r得到关联数组
{
	echo '<div class="panel panel-primary">
		<div class="panel-heading">
		<h3 class="panel-title">' . $row['name'] . '</h3>
		</div>
		<div class="panel-body">' . $row['comment'] . '</div>
		<div class="panel-footer">'. $row['dr'] . '</div></div>';
}
  
//释放结果集
mysqli_free_result($r);

//关闭数据库
mysqli_close($dbc);

//如果页数大于1，则显示分页
if($pages > 1)
{
	$current_page = ($start / $display) + 1;

	echo '<ul class="pager">';
	if($current_page != 1)	//当前页不是第一页，则显示向前连接
	{
		echo '<li><a href="Comments.php?s=' . ($start - $display) . '&p=' . $pages . '">Previous</a></li>';
	}
	if($current_page != $pages)	//当前页不是最后一页，则显示向后连接
	{
		echo '<li><a href="Comments.php?s=' . ($start + $display) . '&p=' . $pages . '">Next</a></li>';
	}
	echo '</ul>';
}

echo '</div>';//col-lg-6
?>
  
<div class="col-lg-6">
<form role="form" action="Comments.php" method="post">
  <label for="inputName" class="sr-only">Name</label>
  <input type="text" name="inputName" class="form-control" placeholder="Name" maxlength="20" required autofocus>
  <label for="inputEmail" class="sr-only">Email address</label>
  <input type="email" name="inputEmail" class="form-control" placeholder="Email address" maxlength="80" required>
  <label for="inputComment" class="sr-only">Comment</label>
  <textarea class="form-control" name="inputComment" rows="5" placeholder="Comment" maxlength="100" required></textarea>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
  <!-- 隐藏输入框，用于判断用户是否点击提交 -->
  <input type = "hidden" name="submitted" value="TRUE">
</form>
</div>
	  
<?php
include('includes/footer.html');	//尾文件
?>