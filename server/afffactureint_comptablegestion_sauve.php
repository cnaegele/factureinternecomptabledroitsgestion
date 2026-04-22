<?php
require 'gdt/gautentificationf5.php';
require_once '/data/dataweb/GoelandWeb/webservice/employe/clCNWSEmployeSecurite.php';
require_once 'gdt/cldbgoeland.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers:  *");
header("Access-Control-Allow-Methods:  POST, OPTIONS");

// Si c'est une requête preflight OPTIONS, on répond 200 et on s'arrête
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$idCaller = 0;
if (array_key_exists('empid', $_SESSION)) {
    $idCaller = $_SESSION['empid'];
}
if ($idCaller > 0) {
    $pseudoWSEmployeSecurite = new CNWSEmployeSecurite();
    if ($pseudoWSEmployeSecurite->isInGroupe($idCaller, 'GoelandManager')) {
        $jsonData = file_get_contents('php://input');
        $oData = json_decode($jsonData);
        $idOrgunit = $oData->idorgunit;
        $idEmploye = $oData->idemploye;
        $sSql = "cn_afffactureint_dico_employecpt_droitedition_sauve $idEmploye, $idOrgunit";
        $dbgo = new DBGoeland();
        $dbgo->queryRetNothing($sSql, 'W');
        unset($dbgo);
        echo '{"message":"ok"}';
    } else {
        echo '{"message":"ERREUR GoelandManager requis"}';
    }
} else {
    echo '{"message":"ERREUR athentification F5"}';
}
