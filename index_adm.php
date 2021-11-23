<?php
require("conf.php");
require("functions.php");
global $connection;
session_start();
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: ab_login.php');
    exit();
}
// grupi lisamine.
// kontoll : kas grupp on andmebaasis või ei...
// kui grupp on lisatud ja veel ei ole tabelis, siis on "alert" - Uus kaubagrupp on lisatud.
// kui grupp on juba lisatud, siis on "alert" - Kaubagrupp on juba lisatud.
if(isset($_REQUEST["kaubagrupi_lisamine"]) && ($_REQUEST["kaubagrupi_nimi"])) {
    $name = $_REQUEST["kaubagrupi_nimi"];
    $result = mysqli_query($connection,"SELECT kaubagrupp FROM kaubagrupid WHERE kaubagrupp='" . $name . "'");
    $row_cnt = mysqli_num_rows($result);
    if($row_cnt >= 1) {
        header("Refresh:0, index_adm.php");
        echo '<script>alert("Kaubagrupp on juba lisatud!")</script>';
    } 
    else {
        addGroup($_REQUEST["kaubagrupi_nimi"]);
        header("Refresh:0, index_adm.php");
        echo '<script>alert("Uus kaubagrupp on lisatud")</script>';
    }
} 
// Kui "input" kaubagrupi_nimi on tühi, siis on alert "Sisesta kaubagrupi nimi!".
if(isset($_REQUEST["kaubagrupi_lisamine"]) && (empty($_REQUEST["kaubagrupi_nimi"]))) {
    header("Refresh:0, index_adm.php");
    echo '<script>alert("Sisesta kaubagrupi nimi!")</script>';
}

// grupi kustutamine.
if(isset($_REQUEST["delete"])) {
    deleteGroup($_REQUEST["delete"]);
}
$groups = dataGroups();?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <title>Tabelid | Kaubad ja kaubagruppid</title>
    </head>
    <body>
        <header class="header">
            <div class="container">
                <div class="nav-bar">
                    <div class="adm-btn">
                        <form action="logout.php" method="post">
                            <input type="submit" id="adm-btn" value="LOGI VÄLJA" name="logout">
                            <p class="adm"><span><?=$_SESSION["kasutaja"]?></span> on sisse logitud</p>
                        </form>
                    </div>
                    <nav>
                </div>
                <div class="text-header">
                    <h1>KODUTÖÖ</h1>
                    <h2>Tabelid | Kaubad ja kaubagruppid
                    </h2>
                </div>
            </div>
        </header>
        <main class="main">
            <div class="container">
            <form action="index_adm.php">
                    <h2>Kaubagrupi lisamine:</h2>
                    <dl>
                        <dt>Kaubagrupi nimi:</dt>
                        <dd><input type="text" name="kaubagrupi_nimi" placeholder="Sisesta nimi..."></dd>
                        <input type="submit" name="kaubagrupi_lisamine" value="Lisa kaubagrupp">
                    </dl>
                </form>
                <div class="container">
            <div class="container">
            <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>
                                KAUBAGRUPP
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($groups as $group): ?>
                        <tr>
                            <td>
                                <strong><?=$group->id ?></strong>
                            </td>
                            <td><?=$group->kaubagrupp ?></td>                          <td>
                                <a
                                    title="Kustuta kaubagrupp"
                                    class="deleteBtn"
                                    href="index_adm.php?delete=<?=$group->id?>"
                                    onclick="return confirm('Oled kindel, et soovid kustutada?');">X</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </main>
    <footer>
        <p>
            © 2021 - Tabelid. All Rights Reserved
        </p>
    </footer>
</body>
</html>