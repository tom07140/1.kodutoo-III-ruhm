<?php
	
	require_once("../config.php");
	$database = "if15_toomloo_3";
	$mysqli = new mysqli($servername, $username, $password, $database);
	
	//echo $_POST["email"];
	$email_error = "";
	$password_error = "";
	$cname_error = "";
	$cemail_error = "";
	$cpassword_error = "";
	
	//muutujad
	$email = "";
	$password = "";
	$cemail = "";
	$cpassword = "";
	
	//kontrollime, et keegi vajutas input nuppu
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		
		//echo "keegi vajutas nuppu";
		
		if (isset($_POST["login"])) { // vajutati login nuppu
			
			//kontrollin, et e-post ei ole tühi
			
			if ( empty($_POST["email"])){
				$email_error = "See väli on kohustuslik";
					
			}else{
				$email = cleanInput($_POST["email"]);
			}
			
			//kontrollin, et password ei ole tühi
			
			if ( empty($_POST["password"])){
				$password_error = "See väli on kohustuslik";
				
			} else {
				$password = cleanInput($_POST["password"]);
				
			}
				
			if ($password_error == "" && $email_error == ""){
				echo "Võib sisse logida! Kasutajanimi on ".$email." ja parool on ".$password;
				
				$hash = hash("sha512", $password);
				
				$stmt = $mysqli->prepare("SELECT id, email FROM login WHERE email=? AND password=?");
				$stmt->bind_param("ss", $email, $hash);
				
				$stmt->bind_result($id_from_db, $email_from_db);
				$stmt->execute();
				
				if($stmt->fetch()){
					echo "Email ja parool on õiged, kasutaja id=".id_from_db;
				}else{
					echo "Wrong credentials!";
				}
				$stmt->close();
			}
			
		
		} 
	if(isset($_POST["create"])){ 
		if ( empty($_POST["cname"])){
			$cname_error = "See väli on kohustuslik";
			
		}
		if ( empty($_POST["cemail"])){
			$cemail_error = "See väli on kohustuslik";
				
		}else{
			$cemail = cleanInput($_POST["cemail"]);
		}
		
		if ( empty($_POST["cpassword"])){
			$cpassword_error = "See väli on kohustuslik";
			
		} else {
			
			//kui oleme siia jõudnud, siis parool ei ole tühi
			//kontrollin, et oleks vähemalt 8 sümbolit pikk
			if(strlen($_POST["cpassword"]) < 8) {
				
				$cpassword_error = "Peab olema vähemalt 8 tähemärki pikk";
				
			}else{
				$cpassword = cleanInput($_POST["cpassword"]);
			}
			
		}
		if ($cname_error == "" && $cemail_error == "" && $cpassword_error == ""){
			
			$hash = hash("sha512", $cpassword);
			
			echo "Võib kasutajat luua! Kasutajanimi on ".$cemail." ja parool on ".$cpassword." ja räsi on".$hash;
			$stmt = $mysqli->prepare("INSERT INTO login (nimi, email, password) VALUES (?,?,?)");
			$stmt->bind_param("sss", $cname, $cemail, $hash);
			$stmt->execute();
			$stmt->close();
		}
	}
		
	}

	function cleanInput($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
		
	}
	
		$mysqli->close();
	
?>
<html>
<head>
	<title>Login page</title>
</head>
<body>
	<h2>Log in</h2>
	
		<form action="login.php" method="post" >
			<input name="email" type="email" placeholder="E-post" value="<?php echo $email; ?>">  <?php echo $email_error; ?><br><br>
			<input name="password" type="password" placeholder="parool" value="<?php echo $password; ?>">  <?php echo $password_error; ?><br><br>
			<input type="submit" name="login" value="Login">  <br><br>
		</form>
	
	<h2>Create user</h2>
	
		<form action="login.php" method="post"> 
			<input name="cname" type="text" placeholder="Eesnimi Perekonnanimi"> <?php echo $cname_error; ?> <br><br>
			<input name="cemail" type="email" placeholder="E-post" value="<?php echo $cemail; ?>"> <?php echo $cemail_error; ?> <br><br>
			<input name="cpassword" type="password" placeholder="parool"> <?php echo $cpassword_error; ?> <br><br> 
			<input type="submit" name="create" value="Registreeru"> <br><br>
		</form>
	
	<h2>MVP idee</h2>
	<p>Internetilehekülg, kus näidatakse League of Legends'i turniire ja kus saab kihla vedada, milline tiim, millise
	turniiri võidab. </p>
</body>

</html>