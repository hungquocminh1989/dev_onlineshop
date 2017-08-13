<?php /* Smarty version Smarty-3.1.14, created on 2017-07-28 18:01:58
         compiled from "E:\DEVELOPMENT\simple_framework\app\template\include\menu.html" */ ?>
<?php /*%%SmartyHeaderCode:20651597afd8625b852-11672724%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7cb26a13de998da26ad68a4a5b024d4696145fb6' => 
    array (
      0 => 'E:\\DEVELOPMENT\\simple_framework\\app\\template\\include\\menu.html',
      1 => 1483432719,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20651597afd8625b852-11672724',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'user_info' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_597afd8643fec0_67351486',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_597afd8643fec0_67351486')) {function content_597afd8643fec0_67351486($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['user_info']->value['user_kbn']=='1'){?>

<div class="header clear_fix">
	<p><?php echo htmlspecialchars(@constant('AKAGANE_TITLE'), ENT_QUOTES, 'UTF-8');?>
</p>
	<ul class="subnav">
	    <!-- Add Start LIXD-492 Tai VNIT 20160420 -->
		<li><a href="javascript:void(0);" class="gnav_0">ヘルプ</a>
		    <ul class="sub">
		      <li><a href="javascript:void(0);" class="helperdownload" id="manual">マニュアル</a></li>
              <li><a href="javascript:void(0);" class="helperdownload" id="guideline">ガイドライン</a></li>
		    </ul>
		</li>
		<!-- Add End LIXD-492 Tai VNIT 20160420 -->
		
		
		<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
login/delete">ログアウト</a></li>
		<li>ログイン　<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user_info']->value['user_name'], ENT_QUOTES, 'UTF-8');?>
</li>
	</ul>
	<ul class="gnav">
		<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
top" class="gnav_1">トップ</a></li>
		<li><a href="javascript:void(0);" class="gnav_2">商品情報管理</a>
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
iteminfo">商品情報</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
category">カテゴリ登録</a></li>

                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemcode/shohincode">商品コード検索</a></li><!--Add LIXD-422 hungtn VNIT 20160225-->
				<!-- //Edit - ZZZZ-765 - Hungtn VNIT - 2014/11/18 -->
				<!--<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemstop"><?php echo htmlspecialchars(@constant('AKAGANE_SERIES_STOP_WORD'), ENT_QUOTES, 'UTF-8');?>
登録</a></li> Remove - LIXD-175 - TrungVNIT - 2015/02/10-->
			</ul></li>
		<li><a href="javascript:void(0);" class="gnav_3">版管理</a>
			<ul class="sub">
				<!--<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
translate">翻訳一覧</a></li>-->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
comment">コメント一覧</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
history">履歴一覧</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
approval">承認一覧</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
pdf_status">PDF状況確認</a></li>
			</ul></li>

		<!-- //Add Start - ZZZZ-765 - Hungtn VNIT - 2014/11/18 -->
		<li><a href="javascript:void(0);" class="gnav_4">一括管理</a><!-- Edit - miyazaki U_SYS - 2015/01/09 -->
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemexport">商品エクスポート</a></li>
<!--				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
translateexport">翻訳エクスポート</a></li>--><!-- Remove - ZZZZ-928 - TrungVNIT - 2015/04/23 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemexporthistory">エクスポート履歴</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemimport">商品インポート</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemimporthistory">インポート履歴</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
inddpdf/downloadindd">Indd・PDF一括ダウンロード</a></li> <!--Edit ZZZZ-765 hungtn VNIT 2014/11/24 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
pdfdownloadhistory">Indd・PDF一括ダウンロード履歴</a></li> <!--Add ZZZZ-41 Phong VNIT 2015/01/15 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
inddpdf/uploadindd">Indd一括アップロード</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
pdfuploadhistory">Indd一括アップロード履歴</a></li> <!--Add ZZZZ-41 Phong VNIT 2015/01/16 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
approval2">一括承認</a></li> <!--Add ZZZZ-924 Tin VNIT 2015/01/26 -->
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
tableupdoad">表一括アップロード</a></li><!--Add LIXD-422 - TrungVNIT - 2015/01/12-->
			</ul></li>
		<!-- //Add End - ZZZZ-765 - Hungtn VNIT - 2014/11/18 -->
		
		<li><a href="javascript:void(0);" class="gnav_8">媒体管理</a>
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
media">媒体管理</a></li>
                                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
mediatemplate">テンプレート登録画面</a></li><!--Add - LIXD-18 - TrungVNIT - 2015/08/12-->
				<!-- <li><a href="#">媒体エクスポート</a></li>
				<li><a href="#">媒体インポート</a></li>-->
			</ul></li>
		<li><a href="javascript:void(0);" class="gnav_9">マスタ管理</a>
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
user">ユーザマスタ</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
spec">アイコンマスタ</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
mediatype">媒体種類マスタ</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
mscolor">背景色マスタ</a></li><!-- Add LIXD-79 hungtn VNIT 20150901 -->
			</ul></li>
	</ul>
</div><!-- / .header -->
<?php }else{ ?>

<div class="header clear_fix">
	<p><?php echo htmlspecialchars(@constant('AKAGANE_TITLE'), ENT_QUOTES, 'UTF-8');?>
</p>
	<ul class="subnav">
		<!-- Add Start LIXD-492 Tai VNIT 20160420 -->
        <li><a href="javascript:void(0);" class="gnav_0">ヘルプ</a>
            <ul class="sub">
              <li><a href="javascript:void(0);" class="helperdownload" id="manual">マニュアル</a></li>
              <li><a href="javascript:void(0);" class="helperdownload" id="guideline">ガイドライン</a></li>
            </ul>
        </li>
        <!-- Add End LIXD-492 Tai VNIT 20160420 -->
		<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
login/delete">ログアウト</a></li>
		<li>ログイン　<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user_info']->value['user_name'], ENT_QUOTES, 'UTF-8');?>
</li>
	</ul>
	<ul class="gnav">
		<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
top" class="gnav_1">トップ</a></li>
		<li><a href="javascript:void(0);" class="gnav_2">商品情報管理</a>
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
iteminfo">商品情報</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
category">カテゴリ登録</a></li>
							
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemcode/shohincode">商品コード検索</a></li><!--Add LIXD-422 hungtn VNIT 20160225-->
				<!-- //Edit - ZZZZ-765 - Hungtn VNIT - 2014/11/18 -->
				<!--<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemstop"><?php echo htmlspecialchars(@constant('AKAGANE_SERIES_STOP_WORD'), ENT_QUOTES, 'UTF-8');?>
登録</a></li> Remove - LIXD-175 - TrungVNIT - 2015/02/10-->
			</ul></li>
		<li><a href="javascript:void(0);" class="gnav_3">版管理</a>
			<ul class="sub">
				<!--<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
translate">翻訳一覧</a></li>-->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
comment">コメント一覧</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
history">履歴一覧</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
approval">承認一覧</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
pdf_status">PDF状況確認</a></li>
			</ul></li>

		<!-- //Add Start - ZZZZ-765 - Hungtn VNIT - 2014/11/18 -->
		<li><a href="javascript:void(0);" class="gnav_4">一括管理</a><!-- Edit - miyazaki U_SYS - 2015/01/09 -->
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemexport">商品エクスポート</a></li>
<!--				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
translateexport">翻訳エクスポート</a></li>--><!-- Remove - ZZZZ-928 - TrungVNIT - 2015/04/23 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemexporthistory">エクスポート履歴</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemimport">商品インポート</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
itemimporthistory">インポート履歴</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
inddpdf/downloadindd">Indd・PDF一括ダウンロード</a></li> <!--Edit ZZZZ-765 hungtn VNIT 2014/11/24 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
pdfdownloadhistory">Indd・PDF一括ダウンロード履歴</a></li> <!--Add ZZZZ-41 Phong VNIT 2015/01/15 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
inddpdf/uploadindd">Indd一括アップロード</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
pdfuploadhistory">Indd一括アップロード履歴</a></li> <!--Add ZZZZ-41 Phong VNIT 2015/01/16 -->
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
approval2">一括承認</a></li> <!--Add ZZZZ-924 Tin VNIT 2015/01/26 -->
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
tableupdoad">表一括アップロード</a></li><!--Add LIXD-422 - TrungVNIT - 2015/01/12-->
			</ul></li>
		<!-- //Add End - ZZZZ-765 - Hungtn VNIT - 2014/11/18 -->
		
		<!-- //Add End - ZZZZ-765 - Hungtn VNIT - 2014/11/18 -->
		<li><a href="javascript:void(0);" class="gnav_8">媒体管理</a>
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
media">媒体管理</a></li>
				<!-- <li><a href="#">媒体エクスポート</a></li>
				<li><a href="#">媒体インポート</a></li>-->
			</ul></li>
		<li><a href="javascript:void(0);" class="gnav_9">マスタ管理</a>
			<ul class="sub">
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
spec">アイコンマスタ</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
mediatype">媒体種類マスタ</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
mscolor">背景色マスタ</a></li><!-- Add LIXD-79 hungtn VNIT 20150901 -->
			</ul></li>
	</ul>
</div><!-- / .header -->
<?php }?>

<!-- Add Start LIXD-492 Tai VNIT 20160420 -->
<script type="text/javascript">
$(function() {
    $('a.helperdownload').click(function () {
        var json_ajax = new Argo_ajax("json");
        var id = $(this).attr('id');
        json_ajax.done_func = function(data) {
            if (data.success == "1") {
                   location.href = "<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
helper/download" + id;
            }
        }
        json_ajax.connect("POST", "Helper/index", {"file":id});
    })
})
</script>
<!-- Add End LIXD-492 Tai VNIT 20160420 --><?php }} ?>