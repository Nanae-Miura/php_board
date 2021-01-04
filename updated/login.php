<?php

require_once('config.php');

session_start();
//入力されたpostのvalidate
if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
    echo'入力された値が不正です';
    return false;
}
//DB内でPOSTされたメールアドレスを検索
try{
    $pdo=new PDO(DSN,DB_USER,DB_PASS);
    //＄stmtというのはstatementの略
    //SQLの実行結果に関する情報を扱いたい時に使う
    $stmt=$pdo->prepare('SELECT*FROM userData where email=?');
    $stmt->execute([$_POST['email']]);
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
}catch(\Exception $e){
    echo $e->getMessage().PHP_EOL;
}
//emailがDB内に存在しているか確認
if(!isset($row['email'])){
    echo 'メールアドレスが間違っています';
    return false;
}
//パスワード確認後sessionにメールアドレスを渡す
if(password_verify($_POST['password'],$row['password'])){
    //session_idを新しく生成し、置き換える
    session_regenerate_id(true);
    echo 'ログインしました';
}else{
    echo 'メールアドレスまたはパスワードが間違っています。';
    return false;
}