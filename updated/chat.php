<?php

//リロードすると何故か前回投稿したメッセージが再び投稿されるバグが

date_default_timezone_set('Asia/Tokyo');

define('DB_HOST','localhost');
define('DB_USER','hoge');
define('DB_PASS','6ZaDwHpqEGQHR8RG');
define('DB_NAME','board');
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

//変数の初期化
//変数の初期化とは、
//変数をあらかじめ空の値で宣言しておくことで存在しない変数を参照するエラーを防いだり、型をあらかじめ設定しておくことで意図しない動作を防ぐ
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message=null;
$error_message=array();
//サニタイズ機能
$clean=array();

session_start();



//これだと名前が表示されない
//if(!empty($_POST["btn_submit"])){
//    if(empty($_POST['view_name']))
//    $error_message[]='表示名を入力してください。';
//}else{
//    $clean['view_name']=htmlspecialchars($_POST['view_name'],ENT_QUOTES);
//    //$clean['view_name']=preg_replace( '/\\r\\n|\\n|\\r/', '', $clean['view_name']);
//}   
//    if(empty($_POST['message'])){
//        $error_message[]='ひとことメッセージを入力してください。';
//    }else{
//        //空じゃなかった場合にサニタイズを行う
//        $clean['message']=htmlspecialchars($_POST['message'],ENT_QUOTES);
//        //↓/\\r\\n|\\n|\\r/'があれば'<br>'に置き換えて！
//        $clean['message'] = preg_replace( '/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
//    }
//    

if (!empty($_POST["btn_submit"])) {
    if (empty($_POST['view_name'])) {
      $error_message[] = '表示名を入力してください。';
    } else {
      $clean['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);

      $_SESSION['view_name'] = $clean['view_name'];
    }
    if (empty($_POST['message'])) {
      $error_message[] = 'ひとことメッセージを入力してください。';
    } else {
      //空じゃなかった場合にサニタイズを行う
      $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
      //↓/\\r\\n|\\n|\\r/'があれば'<br>'に置き換えて！
      $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
    }
  }

if(empty($error_message)){


//データベースに接続する場合、’’は不要
//定数で記述
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
//接続エラーがないか確認
if( $mysqli->connect_errno ) {
    $error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} 
else {
    //文字コード設定
    $mysqli->set_charset('utf8');
			
    // 書き込み日時を取得
    $now_date = date("Y-m-d H:i:s");
    
    // データを登録するSQL作成
 
    $sql = "INSERT INTO message (view_name, message,post_date) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date')";
    
    // データを登録
    $res = $mysqli->query($sql);

    if( $res ) {
        $success_message = 'メッセージを書き込みました。';
    } else {
        $error_message[] = '書き込みに失敗しました。';
    }

    // データベースの接続を閉じる
    $mysqli->close();
}
    }

//データベースへ接続
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);
// 接続エラーの確認
if( $mysqli->connect_errno ) {
	$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
    //SQLとは、Structured Query Language。リレーショナルデータベース(RDB)のデータを操作するための言語です。
    //DESC→降順にデータを取得する
    //ASC→昇順 小さい方から表示
	$sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
	$res = $mysqli->query($sql);
	
	if( $res ) {
		$message_array = $res->fetch_all(MYSQLI_ASSOC);
	}
	//echo view_name;
	$mysqli->close();
}



?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
<title>ひと言掲示板</title>

</head>
<body>
<h1>ひと言掲示板</h1>
<!-- empty関数は値が入っているかどうかを確認することができる -->
<!-- 入っていない場合にtrue -->

<?php if(!empty($success_message)):?>
    <p class="success_message"><?php echo $success_message;?></p>
<?php endif;?>
<!-- 入力されているかのチェック -->
<?php if(!empty($error_message)):?>
<ul class="error_message">
    <?php foreach($error_message as $value): ?>
        <li>・<?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
<?php endif; ?>








<form method="post">
	<div>
        <!-- label for とid -->
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($_SESSION['view_name']) ){ echo $_SESSION['view_name']; } ?>">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"></textarea>
	</div>
	<input type="submit" name="btn_submit" value="書き込む">
</form>
<hr>
<section>
<?php if( !empty($message_array) ): ?>
<!-- $message_arrayをそれぞれvalueとして取り出す -->
<?php foreach( $message_array as $value ): ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    </div>
    <p><?php echo $value['message']; ?></p>
    <div id=icon>
        <span class="material-icons">
        favorite
        </span>
    </div>
</article>


<?php endforeach; ?>
<?php endif; ?>


</section>
</body>
</html>
