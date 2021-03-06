<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nautilus Social Manager - Export CSV Instagram</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">

</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php
            include 'view/private/inc/sidebar.php'
        ?>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <?php
                    include 'view/private/inc/navbar.php'
                ?>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Generation CSV Instagram</h1>
                    </div>
                    <form id='bilan'>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Pages disponible</label>
                            <div class="col-lg-10">
                                <select class="form-control" id='choixPageInsta' style="max-width: 300px;" required>
                                <option value="null" data-nom=""> </option>
                                    <?php
                                        echo $selectPageInsta;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Date minimum</label>
                            <div class="col-lg-10">
                                <input class="form-control" type="date" id="dateDebut" style="max-width: 300px;">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Date maximum</label>
                            <div class="col-lg-10">
                                <input class="form-control" type="date" value="<?php echo date('Y-m-d'); ?>" id="dateFin" style="max-width: 300px;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Generer CSV
                            <section>
                                <progress value="0" max="100" id="progress_bar"></progress>
                            </section>
                        </button>
                    </form>
                    <div id="erreur"></div>
                    <div id="result" class="row mt-2"></div>
                    <script>
                        $('#bilan').submit(function (e) { 
                            e.preventDefault();

                            /* Initialisation à zero des erreurs et des affichages */
                            $('#result').html('');
                            $('#erreur').html('');
                            $("#progress_bar").val("0");
                            
                            /* Récuperation de l'id de la page Instagram choisi */
                            var idPageInsta = $('#choixPageInsta').val();

                            /* Récuperation du token utilisateur */
                            var token = $('#choixPageInsta').find('option:selected').data('value');

                            /* Récuperation de la date de debut choisi */
                            var dateDebut = $('#dateDebut').val();
                            
                            /* Récuperation de la date de Fin choisi */
                            var dateFin = $('#dateFin').val();

                            function msgErreur(texte) {
                                var alert = `
                                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert" >
                                    <strong>Erreur !</strong> ${texte}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>`;
                                return alert;
                            }

                            $("#progress_bar").val("10");
                            /* Verification de la validité de la période choisi */
                            if (dateFin > dateDebut) {
                                /* Récuperation des post de la page Instagram */
                                $.get(`https://graph.facebook.com/v8.0/${idPageInsta}?fields=id,media{id,caption,like_count,media_type,comments_count,thumbnail_url,media_url,timestamp}&access_token=${token}`, function (data, textStatus) {
                                    /* Vérification de la reponse de l'API */
                                    switch (textStatus) { 
                                        case 'success':
                                            $("#progress_bar").val("25");
                                            nbMedia = 0
                                            var itemsNotFormatted = []
                                            data.media.data.forEach(media => {
                                                var date = new Date(media.timestamp);
                                                var dateFormatte = ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear() + ' à ' + date.getHours() + 'h' +  ((date.getMinutes() > 9) ? date.getMinutes() : ('0' + date.getMinutes()));
                                                /* Vérification de la date du post */
                                                if (media.timestamp >= dateDebut && media.timestamp <= dateFin) {
                                                    nbMedia++;
                                                    /* Récuperation des données */
                                                    var msg = media.caption;
                                                    var dateMedia = dateFormatte;
                                                    var nbLike = media.like_count;
                                                    var nbComments = media.comments_count;
                                                    switch (media.media_type) {
                                                        case 'VIDEO':
                                                            var url =`https://graph.facebook.com/v8.0/${media.id}/insights?metric=impressions,reach,video_views&access_token=${token}`;
                                                            
                                                            break;
                                                        default:
                                                            var url =`https://graph.facebook.com/v8.0/${media.id}/insights?metric=impressions,reach&access_token=${token}`;
                                                            break;
                                                    }
                                                    $.ajax({
                                                        url: url,
                                                        dataType: "json",
                                                        async: false,
                                                        success: function (response) {
                                                            /* Récuperation des insights du post */
                                                            var impression = response.data[0].values[0].value;
                                                            var reach = response.data[1].values[0].value;
                                                            var nbVue = 0;
                                                            if (response.data.length >= 3) {
                                                                nbVue = response.data[2].values[0].value;
                                                            }
                                                            /* Push des données du post dans un tableau */
                                                            itemsNotFormatted.push({
                                                                type: media.media_type,
                                                                date: dateFormatte,
                                                                nom: '"' + msg.replace(/,/g, '.').replace(/\n/g, '').replace(/;/g, '.').substr(0, 50) + '"',
                                                                depense: "",
                                                                interet: "",
                                                                age: "",
                                                                reachTotal: reach,
                                                                objectif: "",
                                                                impression: impression,
                                                                engagement: (((nbLike + nbComments) / reach) * 100).toFixed(2).replace(/,/g, '.'),
                                                                like: nbLike,
                                                                com: nbComments,
                                                                nbVues: nbVue
                                                            });
                                                        }
                                                    });
                                                }
                                            });
                                            $("#progress_bar").val("50");
                                            if (nbMedia == 0) {
                                                $('#erreur').html(msgErreur('Aucun post pour cette periode, veuillez choisir une date plus ancienne'));
                                            } else {
                                                function convertToCSV(objArray) {
                                                    var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
                                                    var str = '';

                                                    for (var i = 0; i < array.length; i++) {
                                                    var line = '';
                                                    for (var index in array[i]) {
                                                        if (line != '') line += ','

                                                        line += array[i][index];
                                                    }

                                                    str += line + '\r\n';
                                                    }

                                                    return str;
                                                }
                                                function exportCSVFile(headers, items, fileTitle) {
                                                    if (headers) {
                                                        items.unshift(headers);
                                                    }

                                                    // Convert Object to JSON
                                                    var jsonObject = JSON.stringify(items);

                                                    var csv = convertToCSV(jsonObject);

                                                    var exportedFilenmae = fileTitle + '.csv' || 'export.csv';

                                                    var blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
                                                    if (navigator.msSaveBlob) { // IE 10+
                                                        navigator.msSaveBlob(blob, exportedFilenmae);
                                                    } else {
                                                        var link = document.createElement("a");
                                                        if (link.download !== undefined) { // feature detection
                                                            // Browsers that support HTML5 download attribute
                                                            var url = URL.createObjectURL(blob);
                                                            link.setAttribute("href", url);
                                                            link.setAttribute("download", exportedFilenmae);
                                                            link.style.visibility = 'hidden';
                                                            document.body.appendChild(link);
                                                            link.click();
                                                            document.body.removeChild(link);
                                                        }
                                                    }
                                                }
                                                /* Définition des titres de colonnes du fichier csv */
                                                var headers = {
                                                    type: "Type",
                                                    date: 'Date de campagne',
                                                    nom: "Nom de la campagne",
                                                    depense: "Dépensé",
                                                    interet: "Centre d'intérêt",
                                                    age: "Âge",
                                                    reachTotal: "Reach Total",
                                                    objectif: "Objectif",
                                                    impression: "Impression",
                                                    engagement: "Taux engagement",
                                                    like: "Nb Like.",
                                                    com: "Nb com.",
                                                    nbVues: "Nb vues (vidéo)"
                                                };
                                                var itemsFormatted = [];

                                                // format the data
                                                itemsNotFormatted.forEach((item) => {
                                                    itemsFormatted.push({
                                                    type: item.type,
                                                    date: item.date,
                                                    nom: item.nom,
                                                    depense: item.depense,
                                                    interet: item.interet,
                                                    age: item.age,
                                                    reachTotal: item.reachTotal,
                                                    objectif: item.objectif,
                                                    impression: item.impression,
                                                    engagement: item.engagement,
                                                    like: item.like,
                                                    com: item.com,
                                                    nbVues: item.nbVues
                                                    });
                                                });
                                                
                                                /* Définition du nom de fichier */
                                                var fileTitle = 'orders';
                                                $("#progress_bar").val("75");
                                                /* Export CSV */
                                                exportCSVFile(headers, itemsFormatted,fileTitle); // call the exportCSVFile() function to process the JSON and trigger the download
                                                $("#progress_bar").val("100");
                                            }
                                            break;
                                        
                                        default:
                                            $('#erreur').html(msgErreur("Serveur indisponible ! Veuillez réessayer plus tard ou reliez votre compte à l'application"));      
                                            break;
                                    }
                                });
                            } else {
                                $('#erreur').html(msgErreur("Veuillez selectionner une periode valide"));
                            }
                        });
                    </script>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Nautilus 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <?php
        include 'view/private/inc/footer.php';
    ?>
    <!-- Logout Modal-->
    <?php
        include 'view/private/inc/modalDeconnexion.php'
    ?>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>