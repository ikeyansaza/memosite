<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//post送信されていた場合
if(!empty($_POST)){

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if(empty($err_msg)){

    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //email重複チェック
    validEmailDup($email);

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');

    //パスワード（再入力）の最大文字数チェック
    validMaxLen($pass_re, 'pass_re');
    //パスワード（再入力）の最小文字数チェック
    validMinLen($pass_re, 'pass_re');

    if(empty($err_msg)){

      //パスワードとパスワード再入力が合っているかチェック
      validMatch($pass, $pass_re, 'pass_re');

      if(empty($err_msg)){

        //例外処理
        try {
          // DBへ接続
          $dbh = dbConnect();
          // SQL文作成
          $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
          $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                        ':login_time' => date('Y-m-d H:i:s'),
                        ':create_date' => date('Y-m-d H:i:s'));
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          // クエリ成功の場合
          if($stmt){
            //ログイン有効期限（デフォルトを１時間とする）
            $sesLimit = 60*60;
            // 最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            // ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身：'.print_r($_SESSION,true));

            header("Location:index.php"); //マイページへ
          }

        } catch (Exception $e) {
          error_log('エラー発生:' . $e->getMessage());
          $err_msg['common'] = MSG07;
        }

      }
    }
  }
}

?>

<!-- ヘッド -->
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <!--viewportの設定-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
    <title>MEMO</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="./js/drawr.js"></script>
  </head>

  <body class="page-home">
    <!-- ヘッダー -->
    <header>
      <div class="site-width">
        <h1><i class="fas fa-pencil-alt"></i> MEMO</h1>
      </div>
    </header>
        <div class="header-padding"></div>

<div id="contents" class="site-width">
      <!-- メインコンテンツ -->
      <section id="main">
        <form action="" method="post" class="post info">
          <h2 class="title">ユーザー登録</h2>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            Email <br>
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            <br>パスワード <span style="font-size:12px">※英数字６文字以上</span><br>
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pass'])) echo $err_msg['pass'];
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
            <br>パスワード（再入力） <br>
            <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
            ?>
          </div>

          <div class="btn-container">
            <input type="submit" class="signup-btn" value="登録する">
          </div>

          <br><a href="login.php" class="non-register">登録がお済みの方はこちら</a>
        </form>

      </section>
    </div>

<!-- フッター -->
<?php require('footer.php'); ?>
