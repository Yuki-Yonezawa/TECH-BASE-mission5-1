<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
 <h2>テーマ</h2>
 <br>
 <?php
  //データベース接続設定
  $dsn = 'データベース名';
  $user = 'ユーザー名';
  $password = 'パスワード';
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//新規設定で接続
  //テーブルを作成(もし存在していなければ)
   $sql = 'CREATE TABLE IF NOT EXISTS tbtest5(
     id INT AUTO_INCREMENT PRIMARY KEY,
     name char(32),
     comment TEXT,
     passkey TEXT,
     dt datetime
     )';
   $stmt = $pdo->query($sql);
  //テーブルデータを配列として抽出しておく
   $sql = 'SELECT*FROM tbtest5';
   $stmt = $pdo->query($sql);
   $results = $stmt->fetchAll();
  //ここからは投稿・削除・編集の３つに分岐
  //投稿機能
   if(isset($_POST["submit"])){
     $name = $_POST["name"];
     $com = $_POST["comment"];
     $date = date("Y/m/d H:i:s");
     $pass1 =$_POST["password1"];
     //さらに投稿が編集番号の有無で分岐
     if(empty($_POST["editnumber2"])){
       if(!empty($com) && !empty($name) && !empty($pass1)){
         $sql = $pdo->prepare("INSERT INTO tbtest5(name,comment,passkey,dt)VALUES(:name,:comment,:passkey,:dt)");
         $sql->bindParam(':name',$name, PDO::PARAM_STR);
         $sql->bindParam(':comment',$com, PDO::PARAM_STR);
         $sql->bindParam(':passkey',$pass1, PDO::PARAM_STR);
         $sql->bindParam(':dt',$date, PDO::PARAM_STR);
         $sql -> execute();
       }
     }else{
      $editnumber2 = $_POST["editnumber2"];
      //$editnumber2のidのデータを編集する
      $sql = 'UPDATE tbtest5 SET name=:name,comment=:comment,passkey=:passkey,dt=:dt WHERE id=:id';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':id',$editnumber2, PDO::PARAM_INT);
      $stmt->bindParam(':name',$name, PDO::PARAM_STR);
      $stmt->bindParam(':comment',$com, PDO::PARAM_STR);
      $stmt->bindParam(':passkey',$pass1, PDO::PARAM_STR);
      $stmt->bindParam(':dt',$date, PDO::PARAM_STR);
      $stmt->execute();
     }
  //削除機能
   }elseif(isset($_POST["delete"])){
     $deletenumber = $_POST["number"];
     $pass2 = $_POST["password2"];
     foreach($results as $row){
        if($row['id']==$deletenumber && $row['passkey']==$pass2){
          $sql = 'DELETE from tbtest5 WHERE id=:id';
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id',$deletenumber, PDO::PARAM_INT);
          $stmt->execute();
       }
     }
  //編集機能
   }elseif(isset($_POST["edit"])){
     $editnumber = $_POST["editnumber"];
     $pass3 = $_POST["password3"];
     foreach($results as $row){
       if($row['id']==$editnumber && $row['passkey']==$pass3){
         $editname = $row['name'];
         $editcomment = $row['comment'];
       }
     }
   }
  //分岐終了後に表示
      $sql = 'SELECT*FROM tbtest5';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll();
      foreach($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['dt'].',';
        echo $row['passkey'].'<br>';
        echo "<hr>";
      }
 ?>
  <!--投稿フォーム-->
  <form action = "" method = "post">
   <input type = "text" name = "name" placeholder ="太郎" value = "<?php if(isset($editname)){echo $editname;}?>">
   <input type = "text" name = "comment" placeholder ="こんにちは" value = "<?php if(isset($editcomment)){echo $editcomment;}?>">
   <input type = "text" name = "password1" placeholder = "パスワード" value = "<?php if(isset($editname) && isset($pass3)){echo $pass3;}?>">
   <input type = "submit" name = "submit">
   <br>
   <input type = "number" name = "number" placeholder ="削除したい番号">
   <input type = "text" name = "password2" placeholder = "パスワード">
   <input type = "submit" name = "delete" value = "削除">
   <br>
   <input type = "number" name = "editnumber" placeholder ="編集したい番号">
   <input type = "text" name = "password3" placeholder = "パスワード">
   <input type = "submit" name = "edit" value = "編集">
   <input type = "hidden" name = "editnumber2" value = "<?php if(isset($editnumber)){echo $editnumber;}?>"> 
  </form>
</body>
</html>