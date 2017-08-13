<?php /* Smarty version Smarty-3.1.14, created on 2017-07-28 18:05:57
         compiled from "E:\DEVELOPMENT\simple_framework\app\template\error.html" */ ?>
<?php /*%%SmartyHeaderCode:16970597afe758d67b5-61560314%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5cd9f06d3467e1f0a69e7791435f0ae09bbb4dd6' => 
    array (
      0 => 'E:\\DEVELOPMENT\\simple_framework\\app\\template\\error.html',
      1 => 1480477030,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16970597afe758d67b5-61560314',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_597afe759bcf74_19233006',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597afe759bcf74_19233006')) {function content_597afe759bcf74_19233006($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ログインエラー</title>
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/login.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
<script src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/jquery-ui-1.10.3.custom.js"></script>
</head>
<body class="column_2">
<div class="bg">
<div class="container">
	
	<div class="header clear_fix">
		<p><?php echo htmlspecialchars(@constant('AKAGANE_TITLE'), ENT_QUOTES, 'UTF-8');?>
</p>
		<img class="logo" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/img/logo.jpg" alt="株式会社LIXIL" />
	</div>
	<div class="error_banner">
		<h3 class="ui-state-error">
			<span class="ui-icon ui-icon-alert"></span>
			<span class="error_msg">一定時間操作されなかったため、再度、ログインが必要です。</span>
		<br />
		<input type="button" id="btn_login" value="ログイン画面へ" style="margin-top: 15px;"/>
		</h3>

	</div>
</div>
</div>
<?php echo $_smarty_tpl->getSubTemplate ('include/footer.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<style>
.error_banner {
	width: 100%;
	margin: 10px auto;
	text-align: center;
}

.error_msg {
	font-size: 15px;
}

h3.ui-state-error {
    margin: auto;
	padding: 5px;
	width: 500px;
}
span.ui-icon {
	border: outset 2px;
	margin: 2px auto;
}

</style>
<script type="text/javascript">

$(function() {
	
	$(document).off("click", "#btn_login");
	$(document).on("click", "#btn_login", function () {
		location.href = "<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
login";
	});
});

</script>
</body>
</html>
<?php }} ?>