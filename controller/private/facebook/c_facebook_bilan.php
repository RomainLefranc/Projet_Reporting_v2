<?php

    /* Dependances de la page */
    include 'model/m_pagesFB.php';
    include 'model/m_compteFB.php';

    /* Récuperation de tout les pages Facebook */
    $listePageFB = getPagesFB_BDD();
?>