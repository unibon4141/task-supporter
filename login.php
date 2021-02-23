<?php
  error_reporting(E_ALL);
  ini_set('display_errors','On');
  include('function.php');

// ポスト送信されてきたら
  if(!empty($_POST)) {
 
    
      $email = $_POST['email'];
      $pass = $_POST['pass'];
      $dbh = dbConnection();
      // パスワードとメールアドレスが一致するレコードがあるかチェック
      $stmt = $dbh->prepare('SELECT id from users where email = :email and pass = :pass');
      $stmt->execute(array(':email' => $email,':pass' => $pass));
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!empty($result)){
        session_start();
        $_SESSION['login'] = true;
        $_SESSION['id'] = implode($result);
        header('Location:mypage.php');
      } else {
        echo '一致するアカウントがありません。';
      }
  }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>
  <link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
<div class="main">
  <h2>ログイン</h2>
  <form action="" method="post">
  <label>メールアドレス<span class="help-block"></span>
  <input type="text" name="email" placeholder="xxx@yyy.com">
  </label>
  <label>パスワード<span class="help-block"></span>
  <input type="password" name="pass">
  </label>
  <input type="submit" value='ログイン'>
  </form>
  </div>
</body>
</html>