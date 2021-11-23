<?php
require("conf.php");
global $connection;
session_start();

if (!empty($_POST['login']) && !empty($_POST['pass'])){
    $login=htmlspecialchars(trim($_POST['login']));
    $pass=htmlspecialchars(trim($_POST['pass']));

    $sool='tavalinetext';
    $krypt=crypt($pass, $sool);
    //kontroll, et andmebaasis on selline kasutaja
    $paring="SELECT nimi,onAdmin,koduleht FROM kasutajad WHERE nimi=? AND parool=?";
    $kask = $connection->prepare($paring);
    $kask->bind_param("ss", $login, $krypt);
    $kask->bind_result($kasutaja,$onAdmin, $koduleht);
    $kask->execute();
    $yhendus=mysqli_query($connection, $paring);

    if($kask->fetch()){
        $_SESSION['tuvastamine']='misiganes';
        $_SESSION['kasutaja'] = $kasutaja;
        $_SESSION['onAdmin'] = $onAdmin;
        if (isset($koduleht)) {
            header("location: $koduleht");
            exit();
        } else {
            header("location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style-login.css">
    <title>Login Page</title>
</head>
<body>
    <h1>Login Page</h1>
    <div class="content">
        <form action="" method="post">
            <label for="login">Kasutaja nimi:</label>
            <br>
            <input type="text" name="login" placeholder="Kasutaja nimi">
            <br>
            <label for="pass">Salasõna:</label>
            <br>
            <input type="password" name="pass" placeholder="Salasõna">
            <br>
            <input type="submit"value="Logi sisse">
        </form>
        <?php
        // Kontroll: parooli pikkus.
    
        global $count;
        if (!empty($_POST['login']) && (!empty($_POST['pass']))){
            $login=htmlspecialchars(trim($_POST['login']));
            $pass=htmlspecialchars(trim($_POST['pass']));
            $sool = 'tavalinetext';
            $krypt = crypt($pass, $sool);
            $result = mysqli_query($connection, "SELECT * FROM kasutajad WHERE nimi='$login' AND parool='$krypt'");
            $count = mysqli_num_rows($result);
            if(strlen($pass) < 5) {
                echo '<p>- Teie parool peab olema pikem kui 5 tähemärki.</p>';
            }
        // Kontroll: Kas parool ja kasutaja nimi on õige või vale.
            else if($count==0) {
                echo '<p>- Kasutaja või parool on valed! <br> Proovi uuesti.</p>';                }
            }
        
        ?>
    </div>
</body>
</html>

