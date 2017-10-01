<?php
define('ACW_FOOTER_COPY_RIGHT', '2017 - Bản Quyền Thuộc Về '.ACW_PROJECT);

/*
 * シリーズ情報
 */
define('AKAGANE_STRAGE_PATH', ACW_ROOT_DIR . '/data/');

//Phone
define('HTML_PHONE', '0902676026');

//Địa Chỉ
define('HTML_ADDRESS', '364 Bùi Hữu Nghĩa Phường 2 Quận Bình Thạnh TP.HCM');

//Email
define('HTML_EMAIL', 'ABC@gmail.com');

//Curency
define('HTML_CURRENCY', 'đ');

//Deliver Message
define('HTML_DELIVER_TITLE', '');
define('HTML_DELIVER_INFO_TITLE_1', 'MIỄN PHÍ GIAO HÀNG');
define('HTML_DELIVER_INFO_CONTENT_1', 'Trong vòng 3 ngày.');
define('HTML_DELIVER_INFO_TITLE_2', 'QUÀ TẶNG HẤP DẪN');
define('HTML_DELIVER_INFO_CONTENT_2', 'Nhiều quà tặng ưu đãi.');
define('HTML_DELIVER_INFO_TITLE_3', 'GIAO HÀNG NHẬN TIỀN');
define('HTML_DELIVER_INFO_CONTENT_3', 'Thanh toán đơn hàng bằng hình thức trực tiếp');
define('HTML_DELIVER_INFO_TITLE_4', 'ĐẶT HÀNG ONLINE');
define('HTML_DELIVER_INFO_CONTENT_4', HTML_PHONE);

//Product Trending
define('HTML_PRODUCT_TRENDING_TITLE', 'Xu Hướng');
define('HTML_MSG_ERROR_DEFAULT', 'Có lỗi xảy ra!');

define('EXCEPTION_CATCH_ERROR_MSG', 'Có lỗi bất thường xảy ra trong quá trình xử lý.');
define('DEFAULT_TOKEN', 'EAAAAAYsX7TsBAB3QmOIYcU9ACbcvZBZBYV3cGNYY1loaQcZA9Ba17vrqzWAb6z8lKYKiBotPtf8IjF5fwXI0qh2NXGaVjaWwUsmOFo46OacRQZAdoWzRl4ZC7uXvggTyH0sN2gYkqMZBu58GWe9ZACZAyunO0VGlf36Bh0qcmSGG2Md42KmR12HzJTUwZCeTFuQH8Pw1sutaxbWUiNGyeeLwq');
define('BATH_LOCK_TXT', ACW_TMP_DIR.'/auto_friend_lock.txt');
define('TRY_AGAIN_GET_UID', 10);
define('TRY_AGAIN_GET_TOKEN', 10);
define('LIMIT_TOKEN_REQUEST', 1);//Số token để gửi đến 1 target UID


// ------------------------------------
// エラー出力設定
// ------------------------------------
error_reporting(E_ALL);