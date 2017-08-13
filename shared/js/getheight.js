$(function()
{
setDivSize();
$(window).resize(function(){
setDivSize();
});
});
function setDivSize()
{
var wh = $(window).height();
$(".height1").height(wh - 283);
$(".height2").height(wh - 210);
$(".height3").height(wh - 305);//Add - Trung VNIT - 2014/09/11
}
