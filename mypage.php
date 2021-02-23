<?php 
 error_reporting(E_ALL);
 ini_set('display_errors','On');
 include('function.php');

session_start();
// ログインしていない状態でアクセスした場合トップページにリダイレクトする
if(!$_SESSION['login']){
  header('Location:index.php');
}

$dbh = dbConnection();

if(!empty($_POST)){
// A. POST内容がタスクの追加だった場合
if($_POST['form_type'] === 'add') {
  $task_content = $_POST['task_content'];
  $stmt_add = $dbh->prepare('INSERT INTO tasks (user_id,content,update_time) VALUES(:user_id,:content,:update_time)');
  $stmt_add->execute(array(':user_id'=>$_SESSION['id'],':content'=>$task_content,':update_time'=>date("Y/m/d H:i:s")));

}

// B.POST内容がタスクの削除（完了）だった場合
if($_POST['form_type'] == 'delete') {
  $stmt = $dbh->prepare('UPDATE tasks SET finish_flg = 1 WHERE user_id = :user_id AND id = :task_id;');
  $stmt->execute(array(':user_id' => $_SESSION['id'],':task_id'=>$_POST['task_id']));
}


}


// タスクの一覧をDBから取得し、表示する
// finish_flgで完了済みか判定
  $stmt = $dbh->prepare('SELECT * from tasks WHERE user_id = :id;');
  $stmt->execute(array(':id' => $_SESSION['id']));
  // tasksテーブルの取得した各レコードを表示
  $row = array();
  $row_task_ids = array();
  while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // 完了済みタスクは非表示しない
    if(!$result['finish_flg']){
      $row[] = $result['content'];
      $row_task_ids[] = $result['id'];
    }
  }


 ?>

  <!DOCTYPE html>
  <html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
    <link rel="stylesheet" href="css/mypage.css">
  </head>
  <body>
    <div class="main">
    <h1 class="site-width">マイページ</h1>
    <nav class="site-width">
      <ul class="nav-wrap">
        <li class="nav-item"> <a href="mypage.php">マイページ</a></li>
        <li class="nav-item"><a href="all-view.php">タスク広場</a></li>
      </ul>
    </nav>
    <div class="mytask-container site-width">
    <!-- タスクの追加フォーム -->
    <form action="" method="post" class="add-form">
    <input type="hidden" name="form_type" value="add" >
    <input type="text" name="task_content" placeholder="新規のタスク">
    <input type="submit" value="追加">
    </form>
    <?php
    if(!empty($row)){ 
      $count = 0;
      echo '<ul>';
      foreach($row as $val) {
        echo '<li class="task-item">';
        echo '<p class="task-text">'.htmlspecialchars($val, ENT_QUOTES, "UTF-8").'</p>';
        echo '<form method="post" class="finish-form">';
        echo '<input type="hidden" name="form_type" value="delete">';
        echo '<input type="hidden" name="task_id" value ="'.$row_task_ids[$count].'">';
        echo '<input type="submit" value = "完了" class="finish-btn">';
        echo '</form>';
        echo '</li>';
        $count++;
      }
      echo '</ul>';
    }
    ?>
    <a href="logout.php" class="logout-btn">ログアウト</a>
    </div>
    </div>
    
  </body>
  </html>