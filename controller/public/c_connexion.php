<?php

    include "model/m_utilisateurs.php";

    if (isset($_SESSION['reporting_connecté']) && htmlspecialchars($_SESSION['reporting_connecté']) == true) {
        /* Redirection admin */
        header ('location: index.php?a=a'); 

    } elseif (isset($_POST["id"]) && isset($_POST['mdp'])) {

        require_once 'vendor/google/recaptcha/src/autoload.php';
        $recaptcha = new \ReCaptcha\ReCaptcha('6LeQn-EZAAAAAJIPWyMdK0NjEGgmIGbIEWti3_Ee');
        $cleCaptcha = htmlspecialchars($_POST['g-recaptcha-response']);
        $resp = $recaptcha->verify($cleCaptcha);

        if ($resp->isSuccess()) {

            /* Nettoyage des données reçu */
            $id = htmlspecialchars($_POST['id']);
            $mdp = htmlspecialchars($_POST['mdp']);
            /* Récuperation de l'utilisateur */
            $user = getUser($id);

            if (!empty($user) && password_verify($mdp,$user[0]['mdp'])) {

                $_SESSION["reporting_connecté"] = true;
                $_SESSION["reporting_user"] = $user[0]['pseudo'];
                /* Redirection admin */
                header ('location: index.php?a=a');

            } else {
                $_POST["erreur"] = 1;
            }
        } else {
            $_POST["erreur"] = 2;
        }
    }
?>