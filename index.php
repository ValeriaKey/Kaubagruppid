<?php
require("conf.php");
global $connection;
require("functions.php");
session_start();
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: ab_login.php');
    exit();
}
$sort = "kaubanimi";
$search_term = "";
if(isset($_REQUEST["sort"])) {
    $sort = $_REQUEST["sort"];
}
if(isset($_REQUEST["search_term"])) {
    $search_term = $_REQUEST["search_term"];
}
if(isset($_REQUEST["kaubagrupi_lisamine"]) && ($_REQUEST["kaubagrupi_nimi"])) {
    $name = $_REQUEST["kaubagrupi_nimi"];
    $result = mysqli_query($connection,"SELECT kaubagrupp FROM kaubagrupid WHERE kaubagrupp='" . $name . "'");
    $row_cnt = mysqli_num_rows($result);
    if($row_cnt >= 1) {
        header("Refresh:0, index.php");
        echo '<script>alert("Kaubagrupp on juba lisatud!")</script>';
    } 
    else {
        addGroup($_REQUEST["kaubagrupi_nimi"]);
        header("Refresh:0, index.php");
        echo '<script>alert("Uus kaubagrupp oli lisatud")</script>';
    }
} 
if(isset($_REQUEST["kauba_lisamine"])) {
    if($_REQUEST["kaubanimi"]) {
        addProduct($_REQUEST["kaubanimi"], $_REQUEST["hind"], $_REQUEST["kaubagrupp_id"]);
        header("Refresh:0, index.php");
        echo '<script>alert("Uus kaup on lisatud")</script>';
        
    } else {
        echo '<script>alert("Sisesta kauba nimi!")</script>';
    }
}
if(isset($_REQUEST["delete"])) {
    deleteProduct($_REQUEST["delete"]);
}
if(isset($_REQUEST["save"])) {
    saveProduct($_REQUEST["changed_id"], $_REQUEST["kaubanimi"], $_REQUEST["hind"], $_REQUEST["kaubagrupp_id"]);
}
$products = productData($sort, $search_term);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        <title>Kaubad ja kaubagrupid</title>
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
                </div>
                <div class="text-header">
                    <h1>KODUTÖÖ</h1>
                    <h2>Tabelid | Kaubad ja kaubagruppid
                    </h2>
                </div>
            </div>
        </header>
        <main class="main">
            <form action="index.php" class="form-otsi2">
                <input type="text" name="search_term" placeholder="Otsi...">
            </form>
            <?php if(isset($_REQUEST["edit"])): ?>
            <?php foreach($products as $product): ?>
            <?php if($product->id == intval($_REQUEST["edit"])): ?>
            <form action="index.php" class="form-edit2">
                <input type="hidden" name="changed_id" value="<?=$product->id ?>"/>
                <input type="text" name="kaubanimi" value="<?=$product->kaubanimi?>">
                <input type="text" name="hind" value="<?=$product->hind?>">
                <?php echo createSelect("SELECT id, kaubagrupp FROM kaubagrupid", "kaubagrupp_id"); ?>
                <a title="Katkesta muutmine" class="cancelBtn" href="index.php" name="cancel">X</a>
                <input type="submit" name="save" value="&#10004;">
            </form>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>
        <div class="container">
            <div class="container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>
                                <a href="index.php?sort=kaubanimi">KAUBANIMI</a>
                            </th>
                            <th>
                                <a href="index.php?sort=hind">HIND</a>
                            </th>
                            <th>
                                <a href="index.php?sort=kaubagrupp">KAUBAGRUPP</a>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td>
                                <strong><?=$product->id ?></strong>
                            </td>
                            <td><?=$product->kaubanimi ?></td>
                            <td><?=$product->hind ?></td>
                            <td><?=$product->kaubagrupp ?></td>
                            <td>
                                <a
                                    title="Kustuta kaup"
                                    class="deleteBtn"
                                    href="index.php?delete=<?=$product->id?>"
                                    onclick="return confirm('Oled kindel, et soovid kustutada?');">X</a>
                                <a title="Muuda kaupu" class="editBtn" href="index.php?edit=<?=$product->id?>">&#9998;</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="container">
                <form action="index.php">
                    <h2>Kauba lisamine:</h2>
                    <dl>
                        <dt>Kaubanimi:</dt>
                        <dd><input type="text" name="kaubanimi" placeholder="Sisesta kaubanimi..."></dd>
                        <dt>Hind:</dt>
                        <dd><input type="text" name="hind" placeholder="Sisesta hind..."></dd>
                        <dt>Kaubagrupp:</dt>
                        <dd><?php
                    echo createSelect("SELECT id, kaubagrupp FROM kaubagrupid", "kaubagrupp_id");
                    ?></dd>
                        <input type="submit" name="kauba_lisamine" value="Lisa kaup">
                    </dl>
                </form>
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