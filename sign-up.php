<?php
  error_reporting(E_ALL);
  ini_set('display_errors','On');
  include('function.php');
  $ERR_MSG = array(
    '入力必須です。',
    '正しいメールアドレスを入力してください。',
    'パスワードが一致しません。',
    'パスワードは半角英数で入力してください。',
    'パスワードは6文字以上12文字以内で入力してください。',
    '既に使用されているメールアドレスです。'
  );

// ポスト送信されてきたら
  if(!empty($_POST)) {
    $help_msg = array();

    $email = $_POST['email'];
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $re_pass = $_POST['re-pass'];

    // バリデーションチェック
    // 1.各項目が入力されているかチェックする
    if(empty($email)) {
      $help_msg['email'] = $ERR_MSG[0];
    }
    if(empty($name)) {
      $help_msg['name'] = $ERR_MSG[0];
    }    
    if(empty($pass)) {
      $help_msg['pass'] = $ERR_MSG[0];
    }
    if(empty($re_pass)) {
      $help_msg['re_pass'] = $ERR_MSG[0];
    }

    // 2.emailは正しい形式で入力されているかチェック
    if(empty($help_msg)) {
      if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$email)) {
        $help_msg['email'] = $ERR_MSG[1];
      }
      // 3.パスワードが再入力パスワードと同じかチェック
      if($pass !== $re_pass) {
        $help_msg['pass'] = $ERR_MSG[2];
      }
    }

    // 4.パスワードは半角英数かチェック
    if(empty($help_msg)) {
      if(!preg_match("/^[a-zA-Z0-9]+$/",$pass)) {
        $help_msg['pass'] = $ERR_MSG[3];
      }
    }
    // 5.パスワードが6文字以上12文字以下かチェック
    if(empty($help_msg)) {
      if(mb_strlen($pass) < 6 || mb_strlen($pass) > 12) {
        $help_msg['pass'] = $ERR_MSG[4];
      }
    }
    // ここまでで問題がなければDBに接続してemailの重複チェックを行う。
    if(empty($help_msg)) {
    $dbh = dbConnection();
      // まずはメールアドレスの重複がないかチェックする

      $stmt_check = $dbh->prepare('SELECT id FROM users WHERE email = :email;');
      $result_check = 0;
      $stmt_check->execute(array(':email' => $email));
      // フェッチして検索結果を取り出す
      $result_check = $stmt_check -> fetch(PDO::FETCH_ASSOC); 
      if(!empty($result_check)) {
        $help_msg['email'] = $ERR_MSG[5];
      }else {// 重複ユーザが存在しなかった場合
        $stmt_save_user = $dbh->prepare('INSERT INTO users (name,email,pass) VALUES(:name,:email,:pass);');
        $result_save_user = $stmt_save_user->execute(array(':name'=> $name,':email'=> $email,':pass' => $pass));
        // ユーザーのidを取得
        $stmt_selct_id = $dbh->prepare('SELECT id FROM users WHERE email = :email');
        $stmt_selct_id->execute(array(':email' => $email));
        $result_select_id = $stmt_selct_id->fetch(PDO::FETCH_ASSOC);
        if($result_save_user) { 
          // セッションを開始
          session_start();
          $_SESSION['login'] = true;
          // 連想配列を文字列に変換しsessionのidという添え字の要素に代入
          $_SESSION['id'] = implode($result_select_id);
          header('Location:mypage.php');
        } else {
          echo '会員登録が正常に完了しませんでした。';
        }
      }
    }

  }
  

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー登録</title>
  <link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
  <link rel="stylesheet" href="css/sign-up.css">
</head>
<body>
<div class="main">
  <h2>新規ユーザー登録</h2>
  <form action="" method = "post">
  <label>メールアドレス＊<span class="help-block"><?php if(!empty($help_msg['email'])){ echo $help_msg['email'];} ?></span>
  <input type="text" name="email" placeholder="xxx@yyy.com" value="<?php if(!empty($email)) echo $email; ?>">
  </label>
  <label>ニックネーム＊<span class="help-block"><?php if(!empty($help_msg['name'])) echo $help_msg['name']; ?></span>
  <input type="text" name="name" value="<?php if(!empty($name)) echo $name; ?>">
  </label>
  <label >パスワード＊<span class="help-block"><?php if(!empty($help_msg['pass'])) echo $help_msg['pass']; ?></span>
  <input type="password" name="pass" placeholder="半角英数（6～12文字）">
  </label>
  <label>パスワード（確認用）＊<span class="help-block"><?php if(!empty($help_msg['re_pass'])) echo $help_msg['re_pass']; ?></span>
  <input type="password" name="re-pass">
  </label>
  <input type="submit" name="登録">
  </form>
  </div>
</body>
</html>