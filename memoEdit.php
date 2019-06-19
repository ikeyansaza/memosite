<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「「 メモ詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// 商品IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから商品データを取得
$viewData = getMemoOne($p_id);
// パラメータに不正な値が入っているかチェック
if(empty($viewData)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php");
}
debug('取得したDBデータ：'.print_r($viewData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

if(!empty($_POST['delete'])){
  debug('バリデーションOKです。');

  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    // 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
    debug('MEMOを削除します');
    $sql = 'DELETE FROM memo WHERE id = :p_id';
    $data = array(':p_id' => $p_id);

    debug('SQL：'.$sql);
    debug('流し込みデータ：'.print_r($data,true));
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if($stmt){
      $_SESSION['msg_success'] = SUC04;
      debug('TOPページへ遷移します。');
      header("Location:index.php"); //TOPページへ
      }
      } catch (Exception $e) {
          error_log('エラー発生:' . $e->getMessage());
          $err_msg['common'] = MSG07;
      }
}

if(!empty($_POST['update'])){
  debug('バリデーションOKです。');

  $memo = $_POST['updateForm'];
  //画像をアップロードし、パスを格納
  $pic1 = $viewData['pic1'];
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    // 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
    debug('MEMOを更新します');
    $sql = 'UPDATE memo SET memo = :memo, pic1 = :pic1, create_date = :date WHERE user_id = :u_id AND id = :p_id';
    $data = array(':memo' => $memo, ':pic1' => $pic1, ':date' => date('Y-m-d H:i:s'),  ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);

    debug('SQL：'.$sql);
    debug('流し込みデータ：'.print_r($data,true));
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ成功の場合
    if($stmt){
      $_SESSION['msg_success'] = SUC04;
      debug('TOPページへ遷移します。');
      header("Location:index.php"); //TOPページへ
      }
      } catch (Exception $e) {
          error_log('エラー発生:' . $e->getMessage());
          $err_msg['common'] = MSG07;
      }
}
?>

<?php require('header.php'); ?>

<div id="contents" class="site-width">
  <!-- メインコンテンツ -->
  <section id="main">
    <form action="" method="post" class="post">
      <!-- メモ内容の閲覧&編集画面 -->
      <div class="memoEditFormWrap">
      <p><textarea id="textarea" class="memoEditForm" name="updateForm" rows="3" cols="41"><?php echo sanitize($viewData['memo']); ?></textarea>
      <img src="<?php echo sanitize($viewData['pic1']); ?>" alt="" class="prev-img" style="<?php if(empty(sanitize($viewData['pic1']))) echo 'display:none;' ?>">
          <!-- メモ詳細画面での画像登録はなし、cssで非表示に -->
          <label>
            <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
            <input type="file" name="pic1" class="input-file">
          </label>
      <span class="memoEditSub">
        <?php echo sanitize($viewData['create_date']); ?><br>
        <input type="submit" name="update" value="&#xf044; 更新する" class="far">
        <input type="submit" name="delete" value="&#xf2ed; 削除する" class="far">
      </span>
      </p></div>
    </form>
  </section>
</div>

<footer id="footer">
  Copyright <a href="index.php">MEMO</a>. All Rights Reserved.
</footer>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
new function(){
  //フッターの位置固定
	var footerId = "footer";
	//メイン
	function footerFixed(){
		//ドキュメントの高さ
		var dh = document.getElementsByTagName("body")[0].clientHeight;
		//フッターのtopからの位置
		document.getElementById(footerId).style.top = "0px";
		var ft = document.getElementById(footerId).offsetTop;
		//フッターの高さ
		var fh = document.getElementById(footerId).offsetHeight;
		//ウィンドウの高さ
		if (window.innerHeight){
			var wh = window.innerHeight;
		}else if(document.documentElement && document.documentElement.clientHeight != 0){
			var wh = document.documentElement.clientHeight;
		}
		if(ft+fh<wh){
			document.getElementById(footerId).style.position = "relative";
			document.getElementById(footerId).style.top = (wh-fh-ft-1)+"px";
		}
	}

	//文字サイズ
	function checkFontSize(func){

		//判定要素の追加
		var e = document.createElement("div");
		var s = document.createTextNode("S");
		e.appendChild(s);
		e.style.visibility="hidden"
		e.style.position="absolute"
		e.style.top="0"
		document.body.appendChild(e);
		var defHeight = e.offsetHeight;

		//判定関数
		function checkBoxSize(){
			if(defHeight != e.offsetHeight){
				func();
				defHeight= e.offsetHeight;
			}
		}
		setInterval(checkBoxSize,1000)
	}

	//イベントリスナー
	function addEvent(elm,listener,fn){
		try{
			elm.addEventListener(listener,fn,false);
		}catch(e){
			elm.attachEvent("on"+listener,fn);
		}
	}

	addEvent(window,"load",footerFixed);
	addEvent(window,"load",function(){
		checkFontSize(footerFixed);
	});
	addEvent(window,"resize",footerFixed);

}
</script>

<script>
  //テキストエリアの高さ調整
  function resize(Tarea){
   var areaH = Tarea.style.height;
   areaH = parseInt(areaH) - 54;
   if(areaH < 30){ areaH = 30; }
   Tarea.style.height = areaH + "px";
   Tarea.style.height = parseInt(Tarea.scrollHeight + 10) + "px";
  }
  // ドキュメント内の全てのテキストエリアを走査して高さ調整関数を適用します
  onload = function(){
   var els = document.getElementsByTagName('textarea');
   for (var i = 0; i < els.length; i++){
     var obj = els[i];
     resize(obj);
     obj.onkeyup = function(){ resize(this); }
   }
  }
</script>
</body>
</html>
