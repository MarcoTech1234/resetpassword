<?php

if (isset($_POST["reset-password-submit"])) {

    $selector = $_POST["selector"];
    $validator = $_POST["validator"];
    $password = $_POST["pwd"];
    $passwordRepeat = $_POST["pwd-repeat"];

    if (empty($password) || empty($passwordRepeat)) {
        header("Location: ../create-new-password.php?newpwd=empty");
        exit();
    } else if ($password != $passwordRepeat) {
    header("Location: ../create-new-password.php?newpwd=pwdnotsame");
    exit();
    }

    // Verificando se o Token Expirou, pegando o tempo atual
    $currentDate = date("U");

    require 'dbh.inc.php';

        /* Selecionando o token dentro do database usando o selectorToken, servindo para rodar o select do database
        e o validator token servindo para validar se e o usuario apropriado, dando uma maior segurança*/
        $sql = "SELECT * FROM pwdReset WHERE pwdResetSelector = ? AND pwdResetExpires >= ?";
        $stmt = mysqli_stmt_init($conn); // inicializa o conexão
        // Verifica se o stmt falhou
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            echo "There was an error!";
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $selector); 
            mysqli_stmt_execute($stmt);
        
            $result = mysqli_stmt_get_result($stmt);
            if (!$row = mysqli_fetch_assoc($result)) {
                // Se não encontrar nenhuma coluna, ele dar um erro
                echo "Voce precisa re-enviar seu pedido de reset";
                exit();
            } else {

                // Tranformando o token da URL para binario para fazer a comparação do token com o token binario do banco
                $tokenBin = hex2bin($validator);
                $tokenCheck = password_verify($tokenBin, $row["pwdResetToken"]); // verificase o hash do token armazenada e igual ao tokenBin

                if ($tokenCheck === false) {
                    echo "Voce precisa re-enviar seu pedido de reset";
                    exit();
                } else if ($tokenCheck === true) {
                    // agora vamos update os dados no banco
                    // vamos nos referenciar qual usuario quer mudar a senha atraves do email

                    $tokenEmail = $row['pwdResetEmail']; /* Aqui pegamos o email referente ao token que foi criado pelo usuario para mudar sua
                    mudar sua senha, usamos token para referenciar o email que deseja mudar a senha*/

                    // pegamos os dados do usuario referente ao email
                    $sql = "SELECT * FROM users WHERE emailUsers = ?;";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo "There was an error!";
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if (!$row = mysqli_fetch_assoc($result)) {
                            // Se não encontrar nenhuma coluna, ele dar um erro
                            echo "Tal email não existe no banco!";
                            exit();
                        } else {

                            // Aqui vamos update os dados na tabela users
                            $sql = "UPDATE users SET pwdUsers = ? WHERE emailUsers = ?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                echo "A um Erro";
                                exit();
                            } else {
                                $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                                mysqli_stmt_bind_param($stmt, "ss", $newPwdHash, $tokenEmail); 
                                mysqli_stmt_execute($stmt);

                                // Deletando o token do usuario do banco
                                $sql = "DELETE FROM pwdReset WHERE pwdResetEmail = ?";
                                $stmt = mysqli_stmt_init($conn); // inicializa o conexão
                                if(!mysqli_stmt_prepare($stmt, $sql)) {
                                    echo "There was an error!";
                                    exit();
                                } else {
                                    mysqli_stmt_bind_param($stmt, "s", $tokenEmail); // "s" e o parametro do tipo do valor inserido q é uma String
                                    mysqli_stmt_execute($stmt);
                                    header("Location: ../signup.php/newpwd=passwordupdate");
                                }
                            }
                        }
                    }
                }

            }
        }

} else {
    header("Location: ../index.php");
}