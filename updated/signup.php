<?php

function h($s){
  return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}
//sessionを開始
session_start();
//以下にログイン済の場合の記述
if (isset($_SESSION['EMAIL'])) {
  echo 'ようこそ' .  h($_SESSION['EMAIL']) . "さん<br>";
  echo "<a href='/logout.php'>ログアウトはこちら。</a>";
  exit;
}
//外部ファイルの取り込み
require_once("config.php");

try{
  $pdo=new PDO(DSN,DB_USER,DB_PASS);
 // 例外を投げる？とは
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);;
 $pdo->exec("create table if not exists userData(
   id int not null auto_increment primary key,
   email varchar(255),
   password varchar(255),
   created timestamp not null default current_timestamp
 )");
}catch(Exception $e){
 // 例外の場合にエラーメッセージを受け取る？
 echo $e->getMessage().PHP_EOL;
}

//パスワードチェック
function checkpassword($chkpass)
{
 $passlength=($_POST['password']);
if($passlength<8){
 echo 'パスワードが短すぎます。パスワードは8文字以上で設定してください。';
 return false;
}else{
 $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
}
}

$email = $_POST['email'];
$password =  $_POST['password'];

//登録処理
try{
  
 $stmt=$pdo->prepare('INSERT INTO userData(email,password) value(?,?)');
  var_dump($email); 
  var_dump($password);   
 $stmt->execute([$email, $password]);

 echo '登録完了';
}catch(\Exception $e){
  echo '登録済のメールアドレスです。';
 }
 




?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup画面</title>
</head>
<body>

<h1>ログインしてください</h1>
<form  action="login.php" method="post">
  <label for="email">email</label>
     <input type="email" name="email" placeholder="email">
     <label for="password">password</label>
     <input type="password" name="password" placeholder="passsword">
     <button type="submit">Sign In!</button>
</form>

   <h1>初めての方はこちら</h1>
   <form action="signup.php" method="post">
   <label for="email">email</label>
     <input type="email" name="email">
     <label for="password">password</label>
     <input type="password" name="password">
     <button type="submit">Sign Up!</button>
     <p>※パスワードは、８文字以上で設定してください。</p>
   </form>
  

</body>
</html>