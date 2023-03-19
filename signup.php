<?php

if (isset($_GET["newpwd"])) {
    if ($_GET["newpwd"] == "passwordupdate") {
        echo "<p>SUCESSO, SUA SENHA FOI RESETADA</p>";
    }
}