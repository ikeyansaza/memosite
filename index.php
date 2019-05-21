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
// 画面処理
//================================

// 画面表示用データ取得
//================================
// GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから商品データを取得
$dbFormData = (!empty($p_id)) ? getProduct($_SESSION['user_id'], $p_id) : '';
//変数にユーザー情報を代入
$u_id = $_SESSION['user_id'];

debug('メモID：'.$p_id);
debug('フォーム用DBデータ：'.print_r($dbFormData,true));

// カレントページのGETパラメータを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページめ
// パラメータに不正な値が入っているかチェック
if(!is_int((int)$currentPageNum)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
// 表示件数
$listSpan = 10;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan); //1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
// DBから商品データを取得
$dbMemoData = getMemoList($currentMinNum, $listSpan, $u_id);

debug('現在のページ：'.$currentPageNum);

// パラメータ改ざんチェック
//================================
// GETパラメータはあるが、改ざんされている（URLをいじくった）場合、正しい商品データが取れないのでTOPページへ遷移させる
if(!empty($p_id) && empty($dbFormData)){
  debug('GETパラメータのメモIDが違います。TOPページへ遷移します。');
  header("Location:index.php"); //TOPページへ
}

// POST送信時処理
//================================
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  //変数にユーザー情報を代入
  $memo = $_POST['memo'];
  //画像をアップロードし、パスを格納
  $pic1 = (!empty($_FILES['pic1']['name']) ) ? uploadImg($_FILES['pic1'],'pic1') : '';
  // 画像をPOSTしてない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
  $pic1 = (empty($pic1) && !empty($dbFormData['pic1']) ) ? $dbFormData['pic1'] : $pic1;
  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($dbFormData)){
    //未入力チェック
    validRequired($memo, 'memo');
    //最大文字数チェック
    validMaxLen($memo, 'memo');
  }else{
    if($dbFormData['memo'] !== $memo){
      //未入力チェック
      validRequired($memo, 'memo');
      //最大文字数チェック
      validMaxLen($memo, 'memo');
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
        debug('DB新規登録です。');
        $sql = 'INSERT INTO memo (memo, pic1, user_id, create_date) values (:memo, :pic1, :u_id, :date)';
        $data = array(':memo' => $memo, ':pic1' => $pic1, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

        debug('SQL：'.$sql);
        debug('流し込みデータ：'.print_r($data,true));
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('TOP画面へ遷移します。');
        header("Location:index.php"); //TOP画面へ
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
      <form action="" method="post" class="post" enctype="multipart/form-data">
        <div class="memoWrap">
          <div class="memoMain">
            <div class="area-msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
          <label class="<?php if(!empty($err_msg['memo'])) echo 'err'; ?>">
            <textarea class="memo_area" name="memo" rows="3" cols="41" placeholder="メモを入力してください"></textarea>
          </label>
          <div class="area-msg">
            <?php
            if(!empty($err_msg['memo'])) echo $err_msg['memo'];
            ?>
          </div>
            </div>
          <div class="memoSub">
            <div class="memoImg">
              <label>
                <i class="far fa-image fa-2x icon"></i>
                <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
                <input type="file" name="pic1" class="input-file">
              </label>
              <div class="preview">

              </div>
              <div class="area-msg">
                <?php
                if(!empty($err_msg['pic1'])) echo $err_msg['pic1'];
                ?>
              </div>
            </div>
            <div class="memoSubmit">
              <input class="submit" type="submit" name="" value="投稿">
            </div>
          </div>
          </div>
        </form>

        <div class="panel-list">

         <?php
            foreach($dbMemoData as $key => $val):
          ?>
          <div id="page_top"><a href="index.php"></a></a></div>
          <div class="panel-body">
            <a href="memoEdit.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>" class="panel">

                <p><?php echo nl2br(sanitize($val['memo'])); ?><br><br>
                  <img src="<?php echo sanitize($val['pic1']); ?>" alt="">
                  <span class="time"><?php echo sanitize($val['create_date']); ?></span>
                </p>
            </a>
          </div>
          <?php
            endforeach;
          ?>
        </div>
        <div class="navigation">
          <a class="link" href="?p=<?php echo $currentPageNum+1; ?>"></a>
        </div>

      </section>
    </div>

<?php require('footer.php'); ?>
