<?php

?>

    <main>
        <div>
            <section>
                <h1>Reset your password</h1>
                <p>email vai ser inviado para você com as intruções para resetar sua senha</p>
                <form action="includes/reset-request.inc.php" method="post">
                    <input type="text" name="email" placeholder="coloque seu email">
                    <button type="submit" name="reset-request-submit">Receba uma nova senha pelo email</button>
                </form>
                <!--Mostrar o Resultado do reset-request.inc.php -->
                <?php
                if (isset($_GET["reset"])) {
                    if($_GET["reset"] == "success") {
                        echo '<p class="signupsuccess">Check your e-mail</p>';
                    }
                }
                ?>
            </section>
        </div>
    </main>
