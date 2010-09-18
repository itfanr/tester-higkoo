<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>演示 - Demo</title></head><body>
<?php
	if ($_POST['Submit']){ // 判断是否有点击登录
		$hide = ture; 	// 执行登录则隐藏表单
		$u = trim($_POST['username']); // 获取用户输入的用户名，清除首尾空格
		$p = $_POST['password']; // 获取用户输入的密码
		echo '<h1 align="center"><font color=" '; # 输出HTML标签，指定显示的样式
		if( $u == 'higkoo' &&  $p == 'admin') echo 'Green">恭喜你，登录成功 ！';
		else if($u == '' && $p == '' ) echo 'Orange">请输入帐号和密码 ！';
		else echo 'Red">登录失败！';
		echo '</font></h1>'; # 闭合上文HTML标签
	}
?>
<form <?php if ($hide) echo 'style="display:none"'; ?> id="form" name="form" method="post" action="login.php">
	<table width="200" height="100" border="0" align="center" cellpadding="0" cellspacing="0">
		<caption><div align="center"><strong>用户登录</strong></div></caption>
		<tr>
			<td><div align="right">帐号：</div></td>
			<td><div align="left"><input name="username" type="text" id="username" size="10" maxlength="10" /></div></td>
		</tr>
		<tr>
			<td><div align="right">密码：</div></td>
			<td><div align="left"><input name="password" type="password" id="password" size="10" maxlength="10" /></div></td>
		</tr>
		<tr>
			<td colspan="2"><div align="center"><input type="submit" name="Submit" value="提交" /></div></td>
		</tr>
	</table>
</form></body></html>