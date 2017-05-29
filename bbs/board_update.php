<?php
  // データベースに接続
  $db_user = 'root';      // ユーザー名
  $db_pass = 'fxtrg25x';  // パスワード
  $db_name = 'bbs';       // データベース名

  // MySQLに接続
  $mysqli = new mysqli('localhost', $db_user, $db_pass, $db_name);
  $result = $mysqli->query('select * from `messages`');
  $result_message = '';

  // データベース操作
  if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // 書き込み編集
    if(!empty($_POST['name']) and !empty($_POST['body']) and !empty($_POST['pass'])){
      // SQLインジェクション対策
      $id = $mysqli->real_escape_string($_POST['upd']);
      $name = $mysqli->real_escape_string($_POST['name']);
      $body = $mysqli->real_escape_string($_POST['body']);
      $pass = $mysqli->real_escape_string($_POST['pass']);

      $mysqli->query("update `messages` set `name` = '{$name}', `body` = '{$body}' where `id` = '{$id}' and `pass` = '{$pass}'");
      $count = $mysqli->affected_rows;
      if($count == 1){
        header("Location: board_update_comp.php");
      }else{
        $result_message = 'パスワードが違います';
      }
    }
  }

  // データベースから該当の書き込みを取得
  $result = $mysqli->query("select * from `messages` where `id` = {$_POST['upd']}");

?>

<html>
  <head>
    <meta charset="utf-8">
    <title>書き込み編集 - 掲示板</title>
  </head>
  <body>
    <h1>掲示板</h1>

    <!-- データベース操作時、コメント表示 -->
    <?php echo $result_message ?>

    <h2>書き込み編集</h2>
    <!-- 書き込み編集 -->
    <form action="" method="post">
      <input type="hidden" name="upd" value="<?php echo $_POST['upd'] ?>">
      名前　　　：<input type="text" name="name"><br>
      本文　　　：<input type="text" name="body"><br>
      パスワード：<input type="password" name="pass">
      <input type="submit" value="編集" onclick="brank_check()">
    </form>

    <!-- 該当の書き込みを表示 -->
    <table border="1">
      <caption>該当の書き込み</caption>
      <tr style="background:#BDBDBD">
        <th>名前</th>
        <th>本文</th>
        <th>作成時間</th>
      </tr>
      <?php foreach($result as $row) : ?>
      <tr>
        <td>
          <?php
            $name = htmlspecialchars($row['name']);   // XSS対策
            echo $name;
           ?>
        </td>
        <td>
          <?php
            $body = htmlspecialchars($row['body']);   // XSS対策
            echo $body;
           ?>
        </td>
        <td><?php echo $row['timestamp'] ?></td>
      </tr>
      <?php endforeach; ?>
    </table><br>

    <form action="board.php" method="post">
      <input type="hidden" name="th_id" value="<?php echo $_POST['th_id'] ?>">
      <input type="hidden" name="th_name" value="<?php echo $_POST['th_name'] ?>">
      <input type="submit" value="戻る" >
    </form>
  </body>
</html>
