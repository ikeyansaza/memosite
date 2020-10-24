<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
// post送信されていた場合
if (!empty($_POST)) {
    debug('POST送信があります。');

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false; //ショートハンド（略記法）という書き方

    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');
    //未入力チェック
    validRequired($email, 'email');
    validRequired($pass, 'pass');

    if (empty($err_msg)) {
        debug('バリデーションOKです。');

        //例外処理
        try {
            // DBへ接続
            $dbh = dbConnect();
            // SQL文作成
            $sql = 'SELECT password,id  FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            // クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
            // クエリ結果の値を取得
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            debug('クエリ結果の中身：'.print_r($result, true));

            // パスワード照合
            if (!empty($result) && password_verify($pass, array_shift($result))) {
                debug('パスワードがマッチしました。');

                //ログイン有効期限（デフォルトを１時間とする）
                $sesLimit = 60*60;
                // 最終ログイン日時を現在日時に
        $_SESSION['login_date'] = time(); //time関数は1970年1月1日 00:00:00 を0として、1秒経過するごとに1ずつ増加させた値が入る

        // ログイン保持にチェックがある場合
                if ($pass_save) {
                    debug('ログイン保持にチェックがあります。');
                    // ログイン有効期限を30日にしてセット
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                } else {
                    debug('ログイン保持にチェックはありません。');
                    // 次回からログイン保持しないので、ログイン有効期限を1時間後にセット
                    $_SESSION['login_limit'] = $sesLimit;
                }
                // ユーザーIDを格納
                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身：'.print_r($_SESSION, true));
                debug('マイページへ遷移します。');
                header("Location:index.php"); //マイページへ
            } else {
                debug('パスワードがアンマッチです。');
                $err_msg['common'] = MSG09;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
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
          <h2 class="title">ログイン</h2>
          <div class="area-msg">
            <?php
             if (!empty($err_msg['common'])) {
                 echo $err_msg['common'];
             }
            ?>
          </div>
          <label class="<?php if (!empty($err_msg['email'])) {
                echo 'err';
            } ?>">
            Email <br>
            <input type="text" name="email" value="<?php if (!empty($_POST['email'])) {
                echo $_POST['email'];
            } ?>">
          </label>
          <div class="area-msg">
            <?php
            if (!empty($err_msg['email'])) {
                echo $err_msg['email'];
            }
            ?>
          </div>
          <label class="<?php if (!empty($err_msg['pass'])) {
                echo 'err';
            } ?>">
            <br>パスワード<br>
            <input type="password" name="pass" value="<?php if (!empty($_POST['pass'])) {
                echo $_POST['pass'];
            } ?>">
          </label>
          <div class="area-msg">
            <?php
            if (!empty($err_msg['pass'])) {
                echo $err_msg['pass'];
            }
            ?>
          </div>
          <label>
            <input type="checkbox" name="pass_save"> 次回ログインを省略する
          </label>
          <div class="btn-container">
            <input type="submit" class="signup-btn" value="ログイン">
          </div>

          <br><h3><a href="guest.php"><i class="fas fa-hand-point-right"></i>ゲストユーザーとしてログインする</a></h3><br>
          <a href="passRemindSend.php">パスワードをお忘れの方はこちら</a><br>
          <a href="signup.php">初めての方はこちら</a>

        </form>
      </section>
    </div>

    <!-- フッター -->
    <?php require('footer.php'); ?>
