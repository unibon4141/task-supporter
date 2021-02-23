<!-- ログアウト処理 -->
<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

session_start();
// ログインしていない場合はトップページへ遷移
if(!$_SESSION['login']){
  header('Location:index.php');
} else {
  $_SESSION['login'] = false;
  header('Location:index.php');
}

?>