<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/site_define.php";
require $_SERVER['DOCUMENT_ROOT'] . "/include/sql_connect.php";
?>
<?php
session_start();

if ($_SESSION['authenticated'] != '1')
{
    header("Location: login.php");
    exit;
}
?>
<?php
$sql = sprintf("SELECT `title` FROM `article` WHERE `id` = '%s'",
        mysqli_real_escape_string($connection, $_GET['id']));

require $_SERVER['DOCUMENT_ROOT'] . "/include/sql_result_array.php";

$title = $object[1]['title'];
$title = htmlspecialchars($title);

unset($object);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w4.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<link rel="stylesheet" type="text/css" href="/css/1.css" />
<title><?php echo BLOGNAME; ?>-管理-文章-<?php echo $title; ?></title>
</head>
<body>
<div id='container'>
<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/header_admin.php";
?>

<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/side_admin.php";
?>

<!--主体开始-->
<div id='main' class='container_div'>
<div id='main_content' class='normal_div'>
<?php
$sql = sprintf("SELECT * FROM `article` WHERE `id` = '%s'",
        mysqli_real_escape_string($connection, $_GET['id']));

require $_SERVER['DOCUMENT_ROOT'] . "/include/sql_result_array.php";

$id         = $object[1]['id'];
$done       = $object[1]['done'];
$class      = $object[1]['class'];
$title      = $object[1]['title'];
$item       = $object[1]['item'];
$date       = $object[1]['date'];
$time       = $object[1]['time'];
$deadline   = $object[1]['deadline'];

$title      = htmlspecialchars($title);
$class      = htmlspecialchars($class);
//$item       = htmlspecialchars($item);

// 纯文本格式，原样
//$item = str_replace(' ', '&nbsp;', $item);
//$item = str_replace(PHP_EOL, "<br />\n", $item);

// 把"换行"或"回车"或"回车换行"之间的东西放进<p></p>中，但是有些时候间距太大，
// 如代码中。
//$item = str_replace(PHP_EOL, "\n</p>\n<p>\n", $item);
//$item = "<p>\n" . "$item" . "\n</p>";

// PHP Markdown, conver markdown text to html
require_once $_SERVER['DOCUMENT_ROOT'] . "/Michelf/MarkdownExtra.inc.php";
$item = \Michelf\MarkdownExtra::defaultTransform($item);

unset($object);
?>
<div id='article'>
<h1 id='article_title'><?php echo $title; ?></h1>
<div id="infomation">
<span class="little_block">
<span>完成:</span>
<span class="description"><?php echo $done; ?></span>
</span>
<span class="little_block">
<span>截止日期:</span>
<span class="description"><?php echo $deadline; ?></span>
</span>
<span class="little_block">
<span>分类:</span>
<span class="description"><?php echo $class; ?></span>
</span>

<span class="little_block">
<span>时间:</span>
<span class="description"><?php echo $date . " " . $time; ?></span>
</span>

<span class="little_block">
<a href='write.php?id=<?php echo $id; ?>'>编辑</a>
</span>

<span class="little_block">
<a href='delete_confirm.php?id=<?php echo $id; ?>'>删除</a>
</span>

<hr />
</div>

<div id="maintext"><?php echo $item; ?></div>
</div>

<div id='write_comment'>
<h1>发表评论(* 为必填项)</h1>
<form action='comment.php' method='post' id=''>
<table>
<tr>
<td class='td_left'>称呼(*):</td>
<td><input type="text" name="user" value='' id="" /></td>
</tr>

<tr>
<td class='td_left'>邮箱(*)(不会公开):</td>
<td><input type="text" name="email" value='' /></td>
</tr>

<tr>
<td class='td_left'>网站:</td>
<td><input type="text" name="website" value='' /></td>
</tr>

<tr>
<td class='td_left vertical_top'>评论(*):</td>
<td><textarea name="content"></textarea></td>
</tr>

<tr>
<td class='td_left'></td>
<td>
<input type="hidden" name="article_id" value="<?php echo $_GET['id'];?>" />
<input type="submit" name="submit" value="发表" />
</td>
<tr>
</table>
</form >
</div>

<div id='comment_list'>
<h1>评论列表</h1>
<?php
$sql = sprintf("SELECT `id`, `user`, `email`, `website`, `date`, `time`, `content`
        FROM `comment`
        WHERE `article_id` = '%s'
        ORDER BY `date`, `time`",
        mysqli_real_escape_string($connection, $_GET['id'])) ;

require $_SERVER['DOCUMENT_ROOT'] . "/include/sql_result_array.php";

$i = 1;
$ii = count($object);
for($i =1; $i <= $ii; $i = $i + 1)
{
    $id         = $object[$i]['id'];
    $user       = $object[$i]['user'];
    $email      = $object[$i]['email'];
    $website    = $object[$i]['website'];
    $date       = $object[$i]['date'];
    $time       = $object[$i]['time'];
    $content    = $object[$i]['content'];

    $user = htmlspecialchars($user);
    $content = htmlspecialchars($content);

    echo "<div id='" . $id . "' class='comment_entry'>\n";

    echo "<div class='comment_entry_header'>\n";
    echo "<span class='user'>" . $user . "</span>\n";
    echo "<span class=''>[" . $email . "]</span>\n";
    echo "<span class=''>[" . $website . "]</span>\n";
    echo "<span class='floor'>" . $i . "#</span>\n";
    echo "</div>\n";

    echo "<div class='comment_content'>" . $content . "</div>\n";

    echo "<div class='comment_entry_information'>\n";
    echo "<span class='description'>" . $date . " " . $time . "</span>\n";
    echo "</div>\n";

    echo "</div>\n";
}

unset($object);
?>
</div>

</div>
</div>
<!--主体结束-->

<!--清除浮动-->
<div class='clear'></div>

<?php
require $_SERVER['DOCUMENT_ROOT'] . "/include/footer.php";
?>
</div>
</body>
</html>