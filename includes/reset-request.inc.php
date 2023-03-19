<?php

if (isset($_POST["reset-request-submit"])) {

    // Servem para validar o usuario (Aqui estamos fazendo ele criptograficamente/criptografando seguro)
    $selector = bin2hex(random_bytes(8)); // Verificar as informações no banco para autenticar se é o usuario junto ao token (selecte para achar os usuarios juntos do token)
    $token = random_bytes(32); // Serve para autenticar se esse e mesmo o usuario correto
    
    /* Não vou transformar o token para hexadecimal para enviar ao banco como byte 
    tranformando ele em hexadecimal para poder mandar pelo link, MinhaOP = adicionando uma segurança mesmo se roubarem o
    token pela url ele não vai conseguir resetar a senha do usuario sem o select ou o token em bytes*/

    $url = "forgottenpwd/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token); // Link para ser enviado pelo email (isso e um valor de dependo de qual é o website)

    // Função para expirar o token

    $expires = date("U") + 1800; // pega a data de hj e adiciona o tempo de validade/ até quando vai durar o token

    // inserindo os dados no database
    
    require 'dbh.inc.php';

    $userEmail = $_POST['email'];

    // Deletando os tokens do mesmo usuario no database (resetando o password se por acaso existir algum Ex: o token expirou e o usuario não conseguiu mudar fazendo ele ter que pedir um novo link)

    $sql = "DELETE FROM pwdReset WHERE pwdResetEmail = ?";
    $stmt = mysqli_stmt_init($conn); // inicializa o conexão
        // Verifica se o stmt falhou
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error!";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $userEmail); // "s" e o parametro do tipo do valor inserido q é uma String
        mysqli_stmt_execute($stmt);
    }

    // INSERINDO OS DADOS NO BANCO
    $sql = "INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn); // inicializa o conexão
    // Verifica se o stmt falhou
if(!mysqli_stmt_prepare($stmt, $sql)) {
    echo "There was an error!";
    exit();
} else {
    /* inserindo os dados de forma hash para proteger as informações senviveis, assim, mesmo se hackarem o nosso database
    ele não vai conseguir usar o nosso token ou selector para resetar a sua senha*/
    $hashedToken = password_hash($token, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires); 
    mysqli_stmt_execute($stmt); // Inserimos o token no banco
}

mysqli_stmt_close($stmt); // Fechando o stmt
mysqli_close($conn); // Fechando a conexão do banco

    // Enviando o email pelo metodo MAIL (AVISO ESSE METODO NÃO E RECOMENDADO, VAMOS FAZER PELO PHPMAILER)
    $to = $userEmail;

    $subject = 'Reset sua sennha para o mmtuts';

    $message = '<p>voce recebeu um pedido de reset password. O link para resetar sua senha esta
    abaixo, se voce não fazer esse pedido, voce pode ignorar esse email</p>';
    // Continuação da menssage
    $message .= '<p>Aqui esta seu link para resetar sua senha: </br>';
    $message .= '<a href="' . $url . '">' . $url . '</a></p>';

    $headers = "From: nomeSite <siteEmail@gmail.com>\r\n";  // \r\n estão falando para ir para a proxima linha dentro do php
    $headers .= "Reply-to: usernamedosite@gmail.com\r\n";
    $headers .= "Content-type: text/html\r\n"; // incluindo o HTML para se tornar parte do email

    // Enviando os dados para o usuario (precisa de um servidor mail para rodar esse codigo)
    mail($to, $subject, $message, $headers);

    header("Location: ../reset-password.php?reset=success");

} else {
    header("Location: ../index.php");
}