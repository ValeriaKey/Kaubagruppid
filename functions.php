<?php
require ('conf.php');

function countyData($sort_by = "eesnimi", $search_term = "") {
    global $connection;
    $sort_list = array("eesnimi", "perekonnanimi", "maakonna_nimi");
    if(!in_array($sort_by, $sort_list)) {
        return "Seda tulpa ei saa sorteerida";
    }
    $request = $connection->prepare("SELECT inimene.id, eesnimi, perekonnanimi, maakond.maakonna_nimi
    FROM inimene, maakond 
    WHERE inimene.maakonna_id = maakond.id 
    AND (eesnimi LIKE '%$search_term%' OR perekonnanimi LIKE '%$search_term%' OR maakonna_nimi LIKE '%$search_term%')
    ORDER BY $sort_by");
    $request->bind_result($id, $eesnimi, $perekonnanimi, $maakonna_nimi);
    $request->execute();
    $data = array();
    while($request->fetch()) {
        $person = new stdClass();
        $person->id = $id;
        $person->eesnimi = htmlspecialchars($eesnimi);
        $person->perekonnanimi = htmlspecialchars($perekonnanimi);
        $person->maakonna_nimi = $maakonna_nimi;
        array_push($data, $person);
    }
    return $data;
}

function createSelect($query, $name) {
    global $connection;
    $query = $connection->prepare($query);
    $query->bind_result($id, $data);
    $query->execute();
    $result = "<select name='$name'>";
    while($query->fetch()) {
        $result .= "<option value='$id'>$data</option>";
    }
    $result .= "</select>";
    return $result;
}

    function addCounty($county_name, $county_centre)
    {
        global $connection;
        $query = $connection->prepare("INSERT INTO maakond (maakonna_nimi, maakonna_keskus)
    VALUES (?, ?)");
        $query->bind_param("ss", $county_name, $county_centre);
        $query->execute();
    }

function addPerson($first_name, $last_name, $county_id) {


    global $connection;
    $query = $connection->prepare("INSERT INTO inimene (eesnimi, perekonnanimi, maakonna_id)
    VALUES (?, ?, ?)");
    $query->bind_param("ssd", $first_name, $last_name, $county_id);
    $query->execute();
}

function deletePerson($person_id) {
    global $connection;
    $query = $connection->prepare("DELETE FROM inimene WHERE id=?");
    $query->bind_param("i", $person_id);
    $query->execute();
}

function savePerson($person_id, $first_name, $last_name, $county_id) {
    global $connection;
    $query = $connection->prepare("UPDATE inimene
    SET eesnimi=?, perekonnanimi=?, maakonna_id=?
    WHERE inimene.id=?");
    $query->bind_param("ssii", $first_name, $last_name, $county_id, $person_id);
    $query->execute();
}

/* Funktsioonid kauba ja kaubagrupi tabelitele */

// kaubade otsing ja väjund
function productData($sort_by = "kaubanimi", $search_term = "") {
    global $connection;
    $sort_list = array("kaubanimi", "hind", "kaubagrupp");
    if(!in_array($sort_by, $sort_list)) {
        return "Seda tulpa ei saa sorteerida";
    }
    $request = $connection->prepare("SELECT kaubad.id, kaubanimi, hind, kaubagrupid.kaubagrupp
    FROM kaubad, kaubagrupid 
    WHERE kaubad.kaubagrupp_id = kaubagrupid.id 
    AND (kaubanimi LIKE '%$search_term%' OR hind LIKE '%$search_term%' OR kaubagrupp LIKE '%$search_term%')
    ORDER BY $sort_by");
    $request->bind_result($id, $kaubanimi, $hind, $kaubagrupp);
    $request->execute();
    $data = array();
    while($request->fetch()) {
        $product = new stdClass();
        $product->id = $id;
        $product->kaubanimi = htmlspecialchars($kaubanimi);
        $product->hind = htmlspecialchars($hind);
        $product->kaubagrupp = $kaubagrupp;
        array_push($data, $product);
    }
    return $data;
}
// kaubagrupi lisamine
    function addGroup($product_name)
    {
        global $connection;
        $query = $connection->prepare("INSERT INTO kaubagrupid (kaubagrupp)
    VALUES (?)");
        $query->bind_param("s", $product_name);
        $query->execute();
    }

// kauba lisamine
    function addProduct($name, $price, $product_id) {
        global $connection;
        $query = $connection->prepare("INSERT INTO kaubad (kaubanimi, hind, kaubagrupp_id)
        VALUES (?, ?, ?)");
        $query->bind_param("ssd", $name, $price, $product_id);
        $query->execute();
    }

// kauba kustutamine
function deleteProduct($product_id) {
    global $connection;
    $query = $connection->prepare("DELETE FROM kaubad WHERE id=?");
    $query->bind_param("i", $product_id);
    $query->execute();
}

// kauba andmete salvestamine pärast muutmist.
function saveProduct($product_id, $product_name, $product_price, $group_id) {
    global $connection;
    $query = $connection->prepare("UPDATE kaubad
    SET kaubanimi=?, hind=?, kaubagrupp_id=?
    WHERE kaubad.id=?");
    $query->bind_param("ssii", $product_name, $product_price, $group_id, $product_id);
    $query->execute();
}
// maakonna kustutamine ( admin )
function deleteCounty($maakonna_id) {
    global $connection;
    $query = $connection->prepare("DELETE FROM maakond WHERE id=?");
    $query->bind_param("i", $maakonna_id);
    $query->execute();
}

// maakondade tabeli kuvamiseks ( admin )
function dataCounty() {
    global $connection;
    $request = $connection->prepare("SELECT id, maakonna_nimi,maakonna_keskus
    FROM maakond");
    $request->bind_result($id, $maakonna_nimi, $maakonna_keskus);
    $request->execute();
    $data = array();
    while($request->fetch()) {
        $maakond = new stdClass();
        $maakond->id = $id;
        $maakond->maakonna_nimi = htmlspecialchars($maakonna_nimi);
        $maakond->maakonna_keskus = htmlspecialchars($maakonna_keskus);
        array_push($data, $maakond);
    }
    return $data;
} 

// grupide tabeli kuvamiseks ( kasutaja )
function dataGroups() {
    global $connection;
    $request = $connection->prepare("SELECT id, kaubagrupp
    FROM kaubagrupid");
    $request->bind_result($id, $kaubagrupp);
    $request->execute();
    $data = array();
    while($request->fetch()) {
        $group = new stdClass();
        $group->id = $id;
        $group->kaubagrupp = htmlspecialchars($kaubagrupp);
        array_push($data, $group);
    }
    return $data;
} 

// grupi kustutamine
function deleteGroup($group_id) {
    global $connection;
    $query = $connection->prepare("DELETE FROM kaubagrupid WHERE id=?");
    $query->bind_param("i", $group_id);
    $query->execute();
}