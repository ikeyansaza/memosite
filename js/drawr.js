//ドロワーメニュー
$(function($) {
	WindowHeight = $(window).height();
	$('.drawr').css('height', WindowHeight); //メニューをwindowの高さいっぱいにする

	$(document).ready(function() {
		$('.btn').click(function(){ //クリックしたら
      if($('.drawr').is(":animated")){
        return false;
      }else{
        $('.drawr').animate({width:'toggle'}); //animateで表示・非表示
        $(this).toggleClass('peke'); //toggleでクラス追加・削除
        return false;
      }
		});
	});

  $(document).click(function(event) {
    if (!$(event.target).closest('.drawr').length) {
			$('.btn').removeClass('peke');
      $('.drawr').hide();
    }
  });

});
