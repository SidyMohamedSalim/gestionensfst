<?php    if (!defined("_VALID_PHP"))        die('Direct access to this location is not allowed.');define("HOST", "localhost"); // The host you want to connect to.define("USER", "root"); // The database username.define("PASSWORD", "12345"); // The database password. define("DATABASE", "gs"); // The database name.  //repartition    // Ensure reporting is setup correctly    mysqli_report(MYSQLI_REPORT_STRICT);    $ERR=NULL;    try {        $bdd = new mysqli(HOST, USER, PASSWORD, DATABASE);        $connected = true;        /* change character set to utf8 */        mysqli_set_charset($bdd, "utf8");    } catch (Exception  $e) {        $ERR= $e->getMessage();        if(!isset($install)){            include_once('error.php');            die();        }    }?>