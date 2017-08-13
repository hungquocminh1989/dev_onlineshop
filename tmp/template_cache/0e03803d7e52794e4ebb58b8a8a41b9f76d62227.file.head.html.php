<?php /* Smarty version Smarty-3.1.14, created on 2017-07-28 18:01:58
         compiled from "E:\DEVELOPMENT\simple_framework\app\template\include\head.html" */ ?>
<?php /*%%SmartyHeaderCode:29434597afd86220ec7-80102848%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0e03803d7e52794e4ebb58b8a8a41b9f76d62227' => 
    array (
      0 => 'E:\\DEVELOPMENT\\simple_framework\\app\\template\\include\\head.html',
      1 => 1483432719,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29434597afd86220ec7-80102848',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_597afd86253b57_32586933',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597afd86253b57_32586933')) {function content_597afd86253b57_32586933($_smarty_tpl) {?><meta http-equiv="X-UA-Compatible" content="IE=100" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/main_contents.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/side_contents.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/series.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/js/jquery.droppy.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/js/jquery.lazyload-any.js"></script> <!-- Add LIXD-355 hungtn VNIT 20151118 -->
<script type='text/javascript'>
  $(function() {
	//Add Start - Minh VNIT - 2014/08/04
	$(".gnav_0,.gnav_2,.gnav_3,.gnav_4,.gnav_5,.gnav_6,.gnav_7,.gnav_8, .gnav_9").click(function() {//Edit - ZZZZ-925 - Tin VNIT - 2015/2/2 　Add - miyazaki U_SYS - 2015/01/05 //Edit LIXD-492 TAI 20160420
		$(this).next(".sub").slideToggle("fast");
		$(this).toggleClass("active");
		return false;
	});
	$(".gnav_0,.gnav_2,.gnav_3,.gnav_4,.gnav_5,.gnav_6,.gnav_7,.gnav_8, .gnav_9").mouseover(function() {//Edit - ZZZZ-925 - Tin VNIT - 2015/2/2　Add - miyazaki U_SYS - 2015/01/05 //Edit LIXD-492 TAI 20160420
		$(".sub").hide();
	});
	$(":not([class='sub'],[class*='gnav_'])").click(function() {
		$(".sub").hide();
	});
	//Add End - Minh VNIT - 2014/08/04	
	//Add Start - Trung VNIT - 2014/08/27
	$.fn.enterKey = function (fnc) {
	    return this.each(function () {
	        $(this).keypress(function (ev) {
	            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
	            if (keycode == '13') {
	                fnc.call(this, ev);
	            }
	        })
	    })
	}
	//Add End - Trung VNIT - 2014/08/27
  });
</script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/mustache.js"></script>
<?php }} ?>