<?php /* Smarty version Smarty-3.1.14, created on 2017-07-28 18:02:35
         compiled from "E:\DEVELOPMENT\simple_framework\app\template\top.html" */ ?>
<?php /*%%SmartyHeaderCode:20019597afc15331f78-17938126%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8062d7a6124dfb8b7634ebd01c84e6079ae079bf' => 
    array (
      0 => 'E:\\DEVELOPMENT\\simple_framework\\app\\template\\top.html',
      1 => 1501232551,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20019597afc15331f78-17938126',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_597afc153a7287_69016084',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597afc153a7287_69016084')) {function content_597afc153a7287_69016084($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>トップ</title>
<!-- InstanceEndEditable -->
<?php echo $_smarty_tpl->getSubTemplate ('include/head.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/top.css" rel="stylesheet" type="text/css" />
</head>
<body class="column_1"><!--  ■■■■■　通常はcolumn_2で、比較表示の時はcolumn_3へ　■■■■■  -->
<div class="bg">
<div class="container">
<?php echo $_smarty_tpl->getSubTemplate ('include/menu.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="contents_wrap"> 
	<?php echo $_smarty_tpl->getSubTemplate ('include/footer.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div><!-- / .contents_wrap -->
</div><!-- / .container -->
</div><!-- / .bg -->
<?php echo $_smarty_tpl->getSubTemplate ('include/argo_ajax.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type='text/javascript'>

</script>
</body>
<!-- InstanceEnd --></html><?php }} ?>