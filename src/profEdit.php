<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数にユーザー情報を代入
  $username = $_POST['username'];
  $email = $_POST['email'];
  $comments = $_POST['comments'];
  //画像をアップロードし、パスを格納
  $pic = ( !empty($_FILES['pic']['name']) ) ? uploadImg($_FILES['pic'],'pic') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $pic = ( empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;

  //DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['username'] !== $username){
    //名前の最大文字数チェック
    validMaxLen($username, 'username');
  }
  //DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['comments'] !== $comments){
    //一言の最大文字数チェック
    validMaxLen($comments, 'username');
  }
  if($dbFormData['email'] !== $email){
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    if(empty($err_msg['email'])){
      //emailの重複チェック
      validEmailDup($email);
    }
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの未入力チェック
    validRequired($email, 'email');
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users SET username = :u_name, email = :email, comments = :comments, pic = :pic WHERE id = :u_id';
      $data = array(':u_name' => $username , ':email' => $email, ':comments' => $comments, ':pic' => $pic, ':u_id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC02;
        debug('プロフィールへ遷移します。');
        header("Location:profEdit.php"); //プロフィールへ
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php require('header.php'); ?>

    <div id="contents" class="site-width">
      <!-- メインコンテンツ -->
      <section id="main">
        <form action="" method="post" class="info profEdit" enctype="multipart/form-data">
          <h1 class="title">プロフィール</h1>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
            名前 <br>
            <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['username'])) echo $err_msg['username'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            Email<br>
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['common'])) echo 'err'; ?>">
            一言<br>
            <textarea name="comments" rows="8" cols="80"><?php echo getFormData('comments'); ?></textarea>
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <label class="img">
            プロフ画像<br>
            <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="12000000">
            <input type="file" name="pic" class="input-file02">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pic'])) echo $err_msg['pic'];
            ?>
          </div>
          <div class="btn-container">
            <input type="submit" class="withdraw-btn" name="submit" value="更新">
          </div>

          <a href="passEdit.php">※パスワードを変更する</a>
        </form>
      </section>
    </div>

<footer id="footer">
  Copyright <a href="index.php">MEMO</a>. All Rights Reserved.
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
      });
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
