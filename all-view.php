<?php
// タスク広場ページ
error_reporting(E_ALL);
ini_set('display_errors','On');
include('function.php');
session_start();
// ログインしていない状態でアクセスした場合トップページにリダイレクトする
if(!$_SESSION['login']){
 header('Location:index.php');
}


$dbh = dbConnection();
// タスク一覧を取得し、タスクの作成時間が新しい順にならべる
$stmt = $dbh->prepare('SELECT tasks.*,users.name FROM tasks  inner join users on users.id = tasks.user_id WHERE finish_flg = 0 ORDER BY update_time desc;');
$stmt->execute();
$task_array = array();
while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $task_array[] = $result;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タスク広場</title>
  <link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
  <link rel="stylesheet" href="css/all-view.css">
</head>
<body>
  <div class="main">  
    <h1 class="site-width">タスク広場</h1>
    <nav class="site-width">
      <ul class="nav-wrap">
        <li class="nav-item"> <a href="mypage.php">マイページ</a></li>
        <li class="nav-item"><a href="all-view.php">タスク広場</a></li>
      </ul>
    </nav>
    <div class="task-container site-width">
    <?php 
    if(!empty($task_array)){
      echo '<ul>';
    foreach($task_array as $task) {
      echo '<li class="task-item">';
      echo '<p class="task-name">'.htmlspecialchars($task['name'], ENT_QUOTES, "UTF-8").'</p>';
      echo '<p class="task-content">'.htmlspecialchars($task['content'], ENT_QUOTES, "UTF-8").'</p>';
      echo '</li>';
    }
    echo '</ul>';
    }
    ?>
    </div>
  </div>
</body>
</html>