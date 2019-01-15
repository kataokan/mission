<!DOCTYPE html>
<html>
<html lang="ja" dir=ltr>
<meta charset="utf-8">
	<head>
		<title></title>
	</head>
	
	<body>
	<?php
		//db接続
		$dsn='mysql:dbname=tt_633_99sv_coco_com;host=localhost';
		$user='tt-633.99sv-coco';
		$password='t3RJ5Svc';
		$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
		
		//テーブル作成mission4
		$sql="CREATE TABLE IF NOT EXISTS mission4"
		."("
		."num INT,"
		."name char(32),"
		."comment TEXT,"
		."date TEXT,"
		."pass1 TEXT"
		.");";
		$stmt=$pdo->query($sql);
		
		//テーブル作成mission4_num(カウント用)
		$sql="CREATE TABLE IF NOT EXISTS mission4_num"
		."("
		."num TEXT"
		.");";
		$stmt=$pdo->query($sql);
		
		
		$name=$_POST['name'];
		$comment=$_POST['comment'];
		$hensyuNUM=$_POST['hensyuNUM'];
		$pass1=$_POST['pass1'];
		$pass2=$_POST['pass2'];
		$pass3=$_POST['pass3'];
		$hensyu=$_POST['hensyu'];
		$date=date('Y/m/j H:i:s');
		
		//編集番号とパス3が入力されたら
		if(!empty($hensyuNUM) && !empty($pass3)){
			
			$sql='SELECT*FROM mission4';
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();
			foreach($results as $row){
			//編集番号と投稿番号が一致しパスもあっていたら
			if($hensyuNUM==$row['num'] && $pass3==$row['pass1']){
			
				//入力済の投稿内容を表示する値置き換え
				$namae=$row['name'];
				$komento=$row['comment'];
				$kaeru=$row['num'];
				}
			}
		}
	?>

		<form action="mission_4-1_kataoka.php" method="post">
		名前：<input type="text" name="name" value="<?php  echo "$namae"; ?>"><br>
		コメント：<input type="text" name="comment" value="<?php echo "$komento"; ?>"><br>
		<input type="hidden" name="hensyu" value="<?php echo "$kaeru"; ?>">
		パスワード：<input type="text" name="pass1" >
		<input type="submit" name="button" value="送信"><br><br>
		
		削除対象番号：<input type="text" name="sakujo"><br>
		パスワード：<input type="text" name="pass2">
		<input type="submit" name="button2" value="削除"><br><br>
		
		編集対象番号：<input type="text" name="hensyuNUM"><br>
		パスワード：<input type="text" name="pass3">
		<input type="submit" name="button3" value="編集"><br><br>

<?php
		$name=$_POST['name'];
		$comment=$_POST['comment'];
		$date=date('Y/m/j H:i:s');
		$sakujo=$_POST['sakujo'];

		//送信ボタンが押されたら
		if(!empty($_POST["button"])){
		
		//追加モード
		if(empty($hensyu)){
			/*連番は、mission4_numのデータベース参照*/
			$sql='SELECT*FROM mission4_num';
			$stmt=$pdo->query($sql);
			$results=$stmt->fetchAll();
			foreach($results as $row){
					$num= $row['num'];
				
				}

			//エラーチェック
			if((empty($name))||(empty($comment))||(empty($pass1))){
				$error = "すべての欄を埋めてください。";
				}
			else{
				$sql=$pdo->prepare("INSERT INTO mission4(num,name,comment,date,pass1) VALUES(:num,:name,:comment,:date,:pass1)");
				$sql->bindParam(':num',$num,PDO::PARAM_INT);
				$sql->bindParam(':name',$name,PDO::PARAM_STR);
				$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
				$sql->bindParam(':date',$date,PDO::PARAM_STR);
				$sql->bindParam(':pass1',$pass1,PDO::PARAM_STR);
				$sql->execute();
				
				/*＋1したnumをmission4_numに編集して代入*/
				$num++;
				$sql = 'update mission4_num set num=:num';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':num',$num,PDO::PARAM_INT);
				$stmt->execute();
				
				}
			}
			
		//編集モード
		else if(!empty($hensyu)){
			$num = $hensyu;

			/*編集後のパスワードは入力されれば更新され、入力されなければもとのパスワード*/
			if($pass1==NULL){
				$sql='SELECT*FROM mission4';
				$stmt=$pdo->query($sql);
				$results=$stmt->fetchAll();
				foreach($results as $row){
					if($hensyu==$row['num']){
						$pass1 = $row['pass1'];
					}
				}
			}
			
			$date = date("Y/m/d H:i:s");
			$sql = 'update mission4 set name=:name,comment=:comment,date=:date,pass1=:pass1 where num=:num';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name',$name,PDO::PARAM_STR);
			$stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
			$stmt->bindParam(':date',$date,PDO::PARAM_STR);
			$stmt->bindParam(':pass1',$pass1,PDO::PARAM_STR);
			$stmt->bindParam(':num',$num,PDO::PARAM_INT);
			$stmt->execute();	
		}
	}
	//編集のパスワードが間違っていたら
	if(!empty($hensyuNUM) && !empty($pass3)){

			$sql='SELECT*FROM mission4';
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();
			foreach($results as $row){
			//編集番号と投稿番号が一致したがパスが違う
			if($hensyuNUM==$row['num'] && $pass3!==$row['pass1']){
				echo "パスワードが間違っていますよ。<br>";
				}
			}
		}

	//削除モード
	if(!empty($sakujo) && !empty($pass2)){
		$sql='SELECT*FROM mission4';
		$stmt=$pdo->query($sql);
		$results=$stmt->fetchAll();
		foreach($results as $row){
			if($sakujo==$row['num']){
				$pass1 = $row['pass1'];
			}
		}
		
		/*パスワード確認*/
		if($pass1 == $pass2){
			$num=$sakujo;
			$sql='delete from mission4 where num=:num';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':num',$num,PDO::PARAM_INT);
			$stmt->execute();
		}
		
		else{
			$error = "パスワードが違っています。";
		}
	}
	
	
	//ブラウザ表示
	echo $error."<br/>\n";
	$sql='SELECT*FROM mission4 ORDER BY num';
	$stmt=$pdo->query($sql);
	$results=$stmt->fetchAll();
	foreach($results as $row){
		echo $row['num'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	}
?>
		
		</form>
	</body>
</html>