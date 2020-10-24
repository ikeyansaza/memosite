<footer id="footer">
  Copyright <a href="index.php">MEMO</a>. All Rights Reserved.<br>
  <a href="rule.php"><i class="far fa-arrow-alt-circle-right"></i> 利用規約</a>
  <a href="privacypolicy.php"><i class="far fa-arrow-alt-circle-right"></i> プライバシーポリシー</a>
</footer>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
  $(function(){
    // 1.フッターの高さを固定（コンテンツが少ない場合にもデザインが崩れないよう）
    var $ftr = $('#footer');
    if($('#footer').length){
      if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
        $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
      }
    }

    // 2.画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
    $(function(){
    $('form').on('change', 'input[type="file"]', function(e) {
      var file = e.target.files[0],
          reader = new FileReader(),
          $preview = $(".preview");
          t = this;

      // 2.1画像ファイル以外の場合は何もしない
      if(file.type.indexOf("image") < 0){
        return false;
      }

      // 2.2ファイル読み込みが完了した際のイベント登録
      reader.onload = (function(file) {
        return function(e) {
          //既存のプレビューを削除
          $preview.empty();
          // .prevewの領域の中にロードした画像を表示するimageタグを追加
          $preview.append($('<img>').attr({
                    src: e.target.result,
                    width: "150px",
                    class: "preview",
                    title: file.name
                }));
        };
      })(file);

      reader.readAsDataURL(file);
    });

      // 3.無限スクロール機能
      // 3.1使用する要素名
      var IScontentItems = '.panel-body';
      var IScontent = '.panel-list';
      var ISlink = '.link';
      var ISlinkarea = '.navigation';
      var loadingFlag = false;

      $(window).on('load scroll', function() {

          if(!loadingFlag) {
              var winHeight = $(window).height();
              var scrollPos = $(window).scrollTop();
              var linkPos = $(ISlink).offset().top;

              if(winHeight + scrollPos > linkPos) {
                  loadingFlag = true;

                  var nextPage = $(ISlink).attr('href');
                  $(ISlink).remove();

                  $.ajax({
                      type: 'GET',
                      url: nextPage,
                      dataType: 'html'
                  }).done(function(data) {
                      var nextLink = $(data).find(ISlink);
                      var contentItems = $(data).find(IScontentItems);

                      $(IScontent).append(contentItems);

                      if(nextLink.length > 0) {
                          $(ISlinkarea).append(nextLink);
                          loadingFlag = false;
                      }
                  }).fail(function () {
                      alert('ページの取得に失敗しました。');
                  });
              }
          }
      });

        // 4.TOPへ戻るボタン
        var appear = false;
        var pagetop = $('#page_top');
        $(window).scroll(function () {
          if ($(this).scrollTop() > 100) {  //100pxスクロールしたら
            if (appear == false) {
              appear = true;
              pagetop.stop().animate({
                'bottom': '50px' //下から50pxの位置に
              }, 300); //0.3秒かけて現れる
            }
          } else {
            if (appear) {
              appear = false;
              pagetop.stop().animate({
                'bottom': '-50px' //下から-50pxの位置に
              }, 300); //0.3秒かけて隠れる
            }
          }
        });
        pagetop.click(function () {
          $('body, html').animate({ scrollTop: 0 }, 500); //0.5秒かけてトップへ戻る
          return false;
        });
      });
    });
</script>
</body>
</html>
