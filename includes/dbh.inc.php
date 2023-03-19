<?php

$dBServername = "localhost";
$dBUsername = "root";
$dBName = "loginsystem";
$dBPassword = "";

$conn = mysqli_connect($dBServername, $dBUsername, $dBPassword, $dBName);

if(!$conn) {
    die("Connection failed ". mysqli_connect_error());
}