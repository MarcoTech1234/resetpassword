<?php

?>

    <main>
        <div>
            <section>
                
            <?php
            $selector = $_GET["selector"];
            $validator = $_GET["validator"];

            // Verificando se os tokens estão dentro da URL

            if (empty($selector) || empty($validator)) {
                echo "Could not validate your request!";
            } else {
                // verificando se eles são tokens legitimos (EX: se esse token hexadecimal dentro da URL e realmente um hexadecimal token)
                if (ctype_xdigit($selector) !== false || ctype_xdigit($validator) !== false) {
                    // se realmente eles forem valores hexadecimais validos, mostre o form para resetar a senha
                    ?>

                    <!-- O form vai levar para uma pagina que vai verificar os tokens, e depois vão dar o update da senha no banco-->
                    <form action="includes/reset-password.inc.php" method="post">
                        <input type="hidden" name="selector" value="<?php echo $selector; ?>">
                        <input type="hidden" name="validator" value="<?php echo $validator; ?>">
                        <input type="password" name="pwd" placeholder="Escreva sua nova senha...">
                        <input type="password" name="pwd-repeat" placeholder="Rescreva sua nova senha...">
                        <button type="submit" name="reset-password-submit">Reset password</button>
                    </form>

                    <?php
                }
            }
            ?>

            </section>
        </div>
    </main>