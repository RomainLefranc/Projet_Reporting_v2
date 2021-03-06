<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Nautilus Social Manager - Export CSV Facebook</title>

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
                        <h1 class="h3 mb-0 text-gray-800">Generation CSV Facebook</h1>
                    </div>

                    <div class="form-group row">
                        <label for="example-page-input" class="col-lg-2 col-form-label">Pages disponible</label>
                        <div class="col-lg-10">
                            <select class="form-control monForm" name="selectedValue" style="max-width: 300px;" id="example-page-input">
                            <option value="null" data-nom=""> </option>
                            <?php
                                foreach($listePageFB as $pageFB){
                                    $token = getComptesFB( $pageFB['id_comptes']);
                                    echo '<option value="' . $pageFB['id'] . '" data-value="' . $token . '" data-nom="' . $pageFB['nom'] . '">' . $pageFB['nom'] . '</option>';
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="example-date-input" class="col-lg-2 col-form-label">Date de début</label>
                        <div class="col-lg-10">
                            <input class="form-control monForm" type="date" value="<?php echo date('Y-m-d'); ?>" id="example-date-input"
                            style="max-width: 300px;">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-date-input2" class="col-lg-2 col-form-label">Date de fin</label>
                        <div class="col-lg-9">
                            <input class="form-control monForm" type="date" value="<?php echo date('Y-m-d'); ?>" id="example-date-input2"
                            style="max-width: 300px;">
                        </div>
                    </div>

                    <section>
                        <progress value="0" max="100" id="progress_selectionne"></progress>
                    </section>

                    <button class="btn btn-warning" id="cmd2" type="button">Générer le CSV</button>

                    <div id="erreur"></div>

                    <div id="result" class="container row" style="max-width: initial;"></div>

                    <script>
                    //a chaque changement de mes select dans mon formulaire, on execute la fonction
                    $( "#cmd2" ).click(function() {
                        var progress = document.querySelector('#progress_selectionne');
                        //on récupère l'id de la page en value
                        var str = $("#example-page-input").val();
                        //on récupère le token correspondant a la page en data-value
                        var selected = $('#example-page-input').find('option:selected');
                        var token = selected.data('value'); 
                        //on récupère la date sélectionné par l'utilisateur
                        var date1 = $("#example-date-input").val();
                        var date2 = $("#example-date-input2").val();

                        $("#progress_selectionne").val("5");

                        //on converti les dates dans un format yyyy-mm-dd
                        var dateUn = new Date(date1);
                        var maDate1 = dateUn.getFullYear() + '-' + ((dateUn.getMonth() > 8) ? (dateUn.getMonth() + 1) : ('0' + (dateUn.getMonth() + 1))) + '-' + ((dateUn.getDate() > 9) ? dateUn.getDate() : ('0' + dateUn.getDate()));

                        var dateDeux = new Date(date2);
                        var maDate2 = dateDeux.getFullYear() + '-' + ((dateDeux.getMonth() > 8) ? (dateDeux.getMonth() + 1) : ('0' + (dateDeux.getMonth() + 1))) + '-' + ((dateDeux.getDate() > 9) ? dateDeux.getDate() : ('0' + dateDeux.getDate()));
                        
                        $("#progress_selectionne").val("15");
                        var monUrl = 'https://graph.facebook.com/v4.0/' + str + '?fields=id,name,posts.since(' + maDate1 + ').until(' + maDate2 + '){id,full_picture,message,reactions.summary(true),created_time,comments.summary(true),shares,attachments},fan_count&access_token=' + token;               
                        $.ajax(
                            {
                            url : monUrl,
                            complete :
                                function(xhr, textStatus){
                                if (textStatus == "success") {
                                    var response = JSON.parse(xhr.responseText);                        
                                    var htm = "";
                                    var nbPost = 0;
                                    $("#progress_selectionne").val("25");
                                    //si l'utilisateur n'a pas selectionné de page, on lui propose de selectionner une page
                                    if(selected.data("nom") == ""){
                                    htm += "<p>Veuillez selectioner une page</p>";
                                    }
                                    else if(response.posts === undefined || str == "null"){
                                    htm += "<p>Il n'y a aucun post sur cette page !</p>";
                                    }else{
                                    $("#progress_selectionne").val("35");
                                    var progressActuel = 35;
                                    var progressCount = 60 / response.posts.data.length;
                                    var itemsNotFormatted = []
                                    //boucle for pour afficher les posts de la page en prenant en compte la date sélectionné
                                    for (var pos = 0; pos < response.posts.data.length; pos++) {
                                        //on converti la date dans un format lisible et compréhensible
                                        var date = new Date(response.posts.data[pos].created_time);
                                        var maDate = ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear() + ' ' + date.getHours() + 'h' +  ((date.getMinutes() > 9) ? date.getMinutes() : ('0' + date.getMinutes()));

                                        //on fait un tri et on selectionne uniquement les posts qui ont été postés après la date selectionné
                                        if(response.posts.data[pos].created_time > date1){
                                        nbPost += 1;

                                        

                                        var reactions = response.posts.data[pos].reactions.summary.total_count;
                                        var commentaires = response.posts.data[pos].comments.summary.total_count;
                                        
                                        if(response.posts.data[pos].message === undefined) {
                                            var message = ""; 

                                        }else{
                                            var message = response.posts.data[pos].message; 
                                        }


                                        if(response.posts.data[pos].shares === undefined) {
                                            var shares = 0; 

                                        }else{
                                            var shares = response.posts.data[pos].shares.count; 
                                        }

                                        if(response.posts.data[pos].attachments.data[0].title === undefined) {
                                            var title = "";

                                        }else{
                                            var title = response.posts.data[pos].attachments.data[0].title
                                        }

                                        if(response.posts.data[pos].attachments.data[0].type === undefined) {
                                            var type = "";

                                        }else{
                                            var type = response.posts.data[pos].attachments.data[0].type
                                        }
                                        //obtient un jeton d'accès de page et on affiche les insights de la page (clics et personnes atteintes) avec une succession de 2 appels ajax
                                        var idPage = response.posts.data[pos].id;
                                        
                                        ///////////////////////////////////////////////////////////////////////////////////////////////////
                                        var monUrl2 = 'https://graph.facebook.com/me/accounts?access_token=' + token;
                                        $.ajax(
                                            {
                                                url : monUrl2,
                                                async: false,
                                                complete :
                                                function(xhr, textStatus){
                                                    var response = JSON.parse(xhr.responseText);

                                                    for (var i = 0; i < response.data.length; i++){

                                                        if (response.data[i].id == idPage.substr(0,15)){
                                                            var tokenPage = response.data[i].access_token;
                                                        }
                                                    }
                                                    //recuperationDesInsights(idPage,pos,tokenPage);
                                                    var monUrl2 = 'https://graph.facebook.com/v4.0/' + idPage + '/insights/post_clicks,post_impressions_organic,post_impressions_paid,post_impressions,post_engaged_users,post_video_views,post_impressions_paid_unique,post_impressions_unique,post_video_views_10s,post_video_views_15s?access_token=' + tokenPage;
                                                    $.ajax(
                                                        {
                                                            url : monUrl2,
                                                            async: false,
                                                            complete :
                                                            function(xhr, textStatus){
                                                                var response = JSON.parse(xhr.responseText);
                                                                var htm = "";

                                                                if(response.data[5].values[0].value === undefined) {
                                                                    var nbVues = 0;

                                                                }else{
                                                                    var nbVues = response.data[5].values[0].value
                                                                }

                                                                if(response.data[8].values[0].value === undefined) {
                                                                    var nbVues10s = 0;

                                                                }else{
                                                                    var nbVues10s = response.data[8].values[0].value
                                                                }

                                                                
                                                                //$( "#post" + pos + "" ).append( htm );
                                                                itemsNotFormatted.push({
                                                                    type: type,
                                                                    date: maDate,
                                                                    nom: '"' + message.replace(/,/g, '.').replace(/\n/g, '').replace(/;/g, '.').substr(0, 50) + '"',
                                                                    depense: "",
                                                                    interet: "",
                                                                    age: "",
                                                                    reachOrganique: (response.data[7].values[0].value - response.data[6].values[0].value),
                                                                    reachPublicitaire: response.data[6].values[0].value,
                                                                    reachTotal: response.data[7].values[0].value,
                                                                    objectif: "",
                                                                    impression: response.data[3].values[0].value,
                                                                    interaction: response.data[0].values[0].value + reactions + shares + commentaires,
                                                                    engagement: (( response.data[0].values[0].value + reactions + shares + commentaires)/response.data[7].values[0].value*100).toFixed(2).replace(/,/g, '.'),
                                                                    react: reactions,
                                                                    com: commentaires,
                                                                    partages: shares,
                                                                    clics: response.data[0].values[0].value,
                                                                    nbVues: nbVues,
                                                                    nbVues10s: nbVues10s
                                                                });
                                                            } 
                                                                        
                                                        }
                                                    );
                                                }
                                                
                                            }
                                        );
                                        ///////////////////////////////////////////////////////////////////////////////////////////////////
                                        htm += '</div>';
                                        
                                        }
                                        progressActuel += progressCount;
                                        $("#progress_selectionne").val(progressActuel);

                                    }
                                    if(nbPost == 0){
                                        htm += "<p>Aucun post pour cette periode, veuillez choisir une date plus ancienne</p>";
                                    }
                                    }

                                    $( "#result" ).html( htm );
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

                                    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
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

                                var headers = {
                                    type: "Type",
                                    date: 'Date de campagne',
                                    nom: "Nom de la campagne",
                                    depense: "Dépense",
                                    interet: "Centre d'interet",
                                    age: "Age",
                                    reachOrganique: "Reach Organique",
                                    reachPublicitaire: "Reach publicitaire",
                                    reachTotal: "Reach Total",
                                    objectif: "Objectif",
                                    impression: "Impression",
                                    interaction: "Nb Interaction",
                                    engagement: "Taux engagement",
                                    react: "Nb react.",
                                    com: "Nb com.",
                                    partages: "Nb partages",
                                    clics: "Nb Clics ",
                                    nbVues: "Nb vues (video)",
                                    nbVues10s: "Vues video 10s"

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
                                        reachOrganique: item.reachOrganique,
                                        reachPublicitaire: item.reachPublicitaire,
                                        reachTotal: item.reachTotal,
                                        objectif: item.objectif,
                                        impression: item.impression,
                                        interaction: item.interaction,
                                        engagement: item.engagement,
                                        react: item.react,
                                        com: item.com,
                                        partages: item.partages,
                                        clics: item.clics,
                                        nbVues: item.nbVues,
                                        nbVues10s: item.nbVues10s
                                    });
                                });

                                var fileTitle = 'orders'; // or 'my-unique-title'
                                $("#progress_selectionne").val("99");
                                exportCSVFile(headers, itemsFormatted, fileTitle); // call the exportCSVFile() function to process the JSON and trigger the download
                                $("#progress_selectionne").val("100");
                                }else{
                                    if(selected.data("nom") != ""){
                                    $( "#erreur" ).addClass( "erreur" );
                                    $('#erreur').html("Serveur indisponible ! Veuillez réessayer plus tard ou reliez votre compte à l'application");
                                    }
                                }
                                
                                }
                            }
                        );
                    

                    })
                    .trigger( "change" );
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