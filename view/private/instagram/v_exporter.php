<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nautilus Social Manager - Export PPTX Instagram</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Bundle: Easiest to use, supports all browsers -->
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@2.6.0/dist/pptxgen.bundle.js"></script>

    <!-- Individual files: Add only what's needed to avoid clobbering loaded libraries -->
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@2.6.0/libs/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@2.6.0/libs/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/pptxgenjs@2.6.0/dist/pptxgen.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>

    <script type="module" src="js/html2canvas.esm.js"></script>
    <script src="js/html2canvas.min.js"></script>

    <script src="https://rawgit.com/gitbrent/PptxGenJS/master/dist/pptxgen.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/PptxGenJS/dist/pptxgen.shapes.js"></script>

    <script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/charts.js"></script>
    <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
    
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
                        <h1 class="h3 mb-0 text-gray-800">Generation PowerPoint Instagram</h1>
                    </div>
                    <form id='bilan'>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Pages disponible</label>
                            <div class="col-lg-10">
                                <select class="form-control" id='choixPageInsta' style="max-width: 300px;" required>
                                    <option value=""  data-nom=""> </option>
                                    <?php
                                        echo $selectPageInsta;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" >
                            <label class="col-lg-2 col-form-label">De</label>
                            <div class="col-lg-10">
                                <input class="form-control" type="month" id="dateDebut" style="max-width: 300px;" required>
                                <small class="form-text text-muted">
                                Sélectionner le mois de départ, la période sera de 3 mois à compter du mois choisi (ex : je choisis janvier 2020, le bilan se fera sur Janvier, Février et Mars)
                                </small>
                            </div>
                        </div>
                        <div class="form-group row" >
                            <label class="col-lg-2 col-form-label">à</label>
                            <div class="col-lg-10">
                                <input class="form-control" type="month" id="dateFin" style="max-width: 300px;" required readOnly>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Generer PowerPoint
                            <section>
                                <progress value="0" max="100" id="progress_bar"></progress>
                            </section>
                        </button>
                    </form>
                    <div id="erreur"></div>

                    <div class="card mb-3 mt-2">
                        <div class="card-header">
                            <i class="fas fa-chart-area"></i>
                            Nombre d'interaction par post</div>
                        <div class="card-body audience">
                            <canvas id="myAreaChart2" width="100%" height="30"></canvas>
                        </div>
                    </div>

                    <div class="card mb-3 mt-2">
                        <div class="card-header">
                            <i class="fas fa-chart-area"></i>
                            Audience</div>
                        <div class="card-body">
                            <div id="chartdiv" style='width: 100%;height: 500px;'></div>
                        </div>
                    </div>
                    <script>
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
                        function formatterDateAPI(date) {
                            return date.getFullYear() + '-' + ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '-' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate()));
                        }
                        function formatterDate(date) {
                            return ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + date.getFullYear() + ' à ' + date.getHours() + 'h' +  ((date.getMinutes() > 9) ? date.getMinutes() : ('0' + date.getMinutes()));
                        }
                        $('#dateDebut').change(function () { 
                            var mois = $('#dateDebut').val();
                            mois = new Date(mois);
                            var dernierMois = new Date(mois.getFullYear(), mois.getMonth() + 2)
                            dernierMois = dernierMois.getFullYear() + '-' + ((dernierMois.getMonth() > 8) ? (dernierMois.getMonth() + 1) : ('0' + (dernierMois.getMonth() + 1)))
                            $('#dateFin').val(dernierMois);
                        });
                        $('#bilan').submit(function (e) {
                            e.preventDefault();

                            /* Initialisation à zero des affichages et des messages */
                            $('#erreur').html('');
                            $("#progress_bar").val("0");
                            $("#chartdiv").html('');
                            $("#myAreaChart2").remove();
                            $(".audience").append('<canvas id="myAreaChart2" width="100%" height="30"></canvas>');


                            /* Récuperation de l'id de la page Instagram choisi */
                            var idPageInsta = $('#choixPageInsta').val();

                            /* Récuperation du nom de la page Instagram choisi */
                            var nomPageInsta = $('#choixPageInsta').find('option:selected').data('nom');

                            /* Récuperation du token utilisateur */
                            var token = $('#choixPageInsta').find('option:selected').data('value');

                            /* Récuperation du 1er mois choisi */
                            var mois = $('#dateDebut').val();
                            mois = new Date(mois);

                            var tabMois = [];
                            /* Récuperation de la periode trimestriel à partir du 1er mois choisi */
                            for (let index = 0; index < 3; index++) {

                                tabMois[index] = new Date(mois.getFullYear(), mois.getMonth() + index)
                                listeMois = [
                                        'Janvier',
                                        'Février',
                                        'Mars',
                                        'Avril',
                                        'Mai',
                                        'Juin',
                                        'Juillet',
                                        'Août',
                                        'Septembre',
                                        'Octobre',
                                        'Novembre',
                                        'Decembre'
                                        ]
                                
                                nomMois = listeMois[mois.getMonth() + index];
                                
                                var dateDebut = new Date(mois.getFullYear(), mois.getMonth() + index, 1);
                                var dateSince = formatterDateAPI(dateDebut);

                                var dateFin = new Date(mois.getFullYear(), mois.getMonth() + index + 1, 0);
                                var dateUntil = formatterDateAPI(dateFin);

                                tabMois[index].mois = nomMois;
                                tabMois[index].dateDebut = dateDebut;
                                tabMois[index].dateSince = dateSince;
                                tabMois[index].dateFin = dateFin;
                                tabMois[index].dateUntil = dateUntil;
                            }

                            var tabDateMedia = [];
                            var TabLikeMedia = [];
                            var trimestre = [];
                            var tabPost = [];
                            var tabStorie = [];

                            var nbMediaTrimestre = 0;
                            var nbStoriesTrimestre = 0;
                            var totalInteractionTrimestre = 0;
                            var totalReachTrimestre = 0
                            var totalImpressionTrimestre = 0;
                            var totalFollowerGagneTrimestre = 0;
                            var totalReachStorieTrimestre = 0;

                            tabMois.forEach((mois,index) => {
                                /* Récuperation des posts */
                                $.ajax({
                                    type: "GET",
                                    async: false,
                                    url: `https://graph.facebook.com/v8.0/${idPageInsta}?fields=id,media{id,caption,like_count,media_type,comments_count,thumbnail_url,media_url,timestamp}&access_token=${token}`,
                                    dataType: "json",
                                    success: function (response) {
                                        var nbMediaMensuel = 0;
                                        var totalInteractionMensuel = 0;
                                        var totalReachMensuel = 0
                                        var totalImpressionMensuel = 0;
                                        var followergagneMensuel = 0;
                                        response.media.data.reverse();
                                        response.media.data.forEach(media => {
                                            var dateMedia = new Date(media.timestamp);
                                            if (dateMedia >= mois.dateDebut && dateMedia <= mois.dateFin) {
                                                nbMediaTrimestre++;
                                                nbMediaMensuel++;
                                                nbMediaTrimestre++;
                                                media.date = formatterDate(dateMedia)
                                                tabDateMedia.push(media.date);
                                                TabLikeMedia.push(media.like_count);
                                                totalInteractionTrimestre += media.like_count + media.comments_count;
                                                var idMedia = media.id;
                                                var url = "";
                                                switch (media.media_type) {
                                                    case 'VIDEO':
                                                        media.media_url = media.thumbnail_url;
                                                        url =`https://graph.facebook.com/v8.0/${idMedia}/insights?metric=impressions,reach,engagement,video_views&access_token=${token}`;
                                                        break;
                                                
                                                    default:
                                                        url =`https://graph.facebook.com/v8.0/${idMedia}/insights?metric=impressions,reach,engagement&access_token=${token}`;
                                                        break;
                                                }
                                                $.ajax({
                                                    type: "GET",
                                                    url: url,
                                                    async: false,
                                                    dataType: "json",
                                                    success: function (response) {
                                                        totalImpressionMensuel += response.data[0].values[0].value
                                                        totalReachMensuel += response.data[1].values[0].value
                                                        totalInteractionMensuel += response.data[2].values[0].value
                                                        media.impression = response.data[0].values[0].value
                                                        media.reach = response.data[1].values[0].value
                                                        media.interaction = response.data[2].values[0].value
                                                        media.TauxInteraction = ((media.interaction / media.reach)*100).toFixed(2);
                                                        if (response.data.length == 4) {
                                                            media.nbVueVideo = response.data[3].values[0].value
                                                        }
                                                        totalReachTrimestre += media.reach
                                                        totalImpressionTrimestre += media.impression
                                                    }
                                                });
                                                tabPost.push(media);
                                            }
                                        });
                                        mois.nbMediaMensuel = nbMediaMensuel;
                                        mois.totalInteractionMensuel = totalInteractionMensuel;
                                        mois.totalReachMensuel = totalReachMensuel;
                                        mois.totalImpressionMensuel = totalImpressionMensuel;
                                        if (totalReachMensuel == 0) {
                                            mois.tauxInteraction = 0
                                        } else {
                                            mois.tauxInteraction = ((totalInteractionMensuel / totalReachMensuel)*100).toFixed(2);
                                        } 
                                    }
                                });
                                /* Récuperation du nombre de follower gagné */
                                $.ajax({
                                    type: "GET",
                                    async: false,
                                    url: `https://graph.facebook.com/v4.0/${idPageInsta}/insights?metric=follower_count,reach,impressions&period=day&since=${mois.dateSince}&until=${mois.dateUntil}&access_token=${token}`,
                                    dataType: "json",
                                    success: function (response) {
                                        var followergagneMensuel = 0;
                                        response.data[0].values.forEach(element => {
                                            followergagneMensuel += element.value
                                            totalFollowerGagneTrimestre += element.value
                                        });
                                        mois.totalFollowerGagneMensuel = followergagneMensuel; 
                                    }
                                });
                                /* Récuperation des stories */
                                $.ajax({
                                    type: "GET",
                                    url: `https://localhost/Projet_reporting_v2/index.php?a=API&idPageInsta=${idPageInsta}&dateDebut=${mois.dateSince}&dateFin=${mois.dateUntil}`,
                                    async: false,
                                    dataType: "json",
                                    success: function (response) {
                                        var nbStorieMois = 0;
                                        var totalreachMois = 0;
                                        response.resultat.stories.forEach(storie => {
                                            nbStoriesTrimestre++;
                                            nbStorieMois++;
                                            totalreachMois += parseInt(storie.reach);
                                            totalReachStorieTrimestre += parseInt(storie.reach);
                                            storie.date = new Date(storie.date);
                                            storie.date = formatterDate(storie.date);
                                            tabStorie.push(storie);
                                        });
                                        mois.nbStorie = nbStorieMois;
                                        if (nbStorieMois == 0) {
                                            mois.personneAtteintesMoyen = 0

                                        } else {
                                            mois.personneAtteintesMoyen = Math.round(totalreachMois/nbStorieMois);
                                        }
                                        mois.totalreach = totalreachMois
                                    }
                                });
                            });
                            if (nbMediaTrimestre == 0) {
                                $('#erreur').html(msgErreur('Aucun post trouvé dans cet periode'));
                            } else {
                                if (nbStoriesTrimestre == 0) {
                                    trimestre.ReachStoriesMoyen = 0
                                } else {
                                    trimestre.ReachStoriesMoyen = (totalReachStorieTrimestre/nbStoriesTrimestre).toFixed(0);
                                }
                                trimestre.nbMedia = nbMediaTrimestre;
                                trimestre.totalInteraction = totalInteractionTrimestre;
                                trimestre.totalReach = totalReachTrimestre;
                                trimestre.totalImpression = totalImpressionTrimestre;
                                trimestre.totalFollowerGagne = totalFollowerGagneTrimestre;
                                trimestre.nbStories = nbStoriesTrimestre;
                                trimestre.totalReachStories = totalReachStorieTrimestre;

                                $("#progress_bar").val("10");

                                /* Tri du tableau des post pour avoir le top 3 des post en taux d'interaction */
                                var tabInteraction = [...tabPost];
                                tabInteraction.sort((a,b) => (b.interaction/b.reach) - (a.interaction/a.reach));
                                tabInteraction = tabInteraction.slice(0,3);

                                /* Tri du tableau des post pour avoir le top 3 des post en reach */
                                var tabReach = [...tabPost];
                                tabReach.sort((a,b) => b.reach - a.reach);
                                tabReach = tabReach.slice(0,3);

                                /* Tri du tableau des post pour avoir le meilleur post de la periode */
                                var topMedia = [...tabPost];
                                topMedia.sort((a,b) => b.reach - a.reach || b.interaction - a.interaction);
                                topMedia = topMedia[0];

                                /* Tri du tableau des post pour avoir le top 3 des pire post en reach */
                                var flopReach = [...tabPost];
                                flopReach.sort((a,b) => a.reach - b.reach);
                                flopReach = flopReach.slice(0,3);

                                /* Tri du tableau des stories pour avoir le top 3 des stories en reach */
                                var top3ReachStories = [...tabStorie];
                                top3ReachStories.sort((a,b) => b.reach - a.reach);
                                top3ReachStories = top3ReachStories.slice(0,3);

                                /* Préparation du graphique */
                                var max = Math.ceil(Math.max(...TabLikeMedia)) + 5;

                                /* Récuperation de l'element qui contiendra le graphique */
                                var ctx = document.getElementById("myAreaChart2");

                                /* Création du graphique */
                                var myLineChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: tabDateMedia,
                                        datasets: [{
                                            label: "like",
                                            lineTension: 0.3,
                                            backgroundColor: "rgba(2,117,216,0.2)",
                                            borderColor: "rgba(2,117,216,1)",
                                            pointRadius: 5,
                                            pointBackgroundColor: "rgba(2,117,216,1)",
                                            pointBorderColor: "rgba(255,255,255,0.8)",
                                            pointHoverRadius: 5,
                                            pointHoverBackgroundColor: "rgba(2,117,216,1)",
                                            pointHitRadius: 50,
                                            pointBorderWidth: 2,
                                            data: TabLikeMedia,
                                        }],
                                    },
                                    options: {
                                        scales: {
                                            xAxes: [{
                                                time: {
                                                    unit: 'date'
                                                },
                                                gridLines: {
                                                    display: false
                                                },
                                                ticks: {
                                                    maxTicksLimit: 7
                                                }
                                            }],
                                            yAxes: [{
                                                ticks: {
                                                    min: 0,
                                                    max: max,
                                                    maxTicksLimit: 5
                                                },
                                            gridLines: {
                                                    color: "rgba(0, 0, 0, .125)",
                                                }
                                            }],
                                        },
                                        legend: {
                                            display: false
                                        }
                                    }
                                });

                                $("#progress_bar").val("20");

                                /* Récuperation de l'audience de la page Instagram */
                                $.get(`https://graph.facebook.com/v4.0/${idPageInsta}/insights/audience_gender_age/lifetime?&access_token=${token}`,function (data) {
                                    
                                    function verifValeur(params) {
                                        if (typeof params === 'number') {
                                            return params
                                        } else {
                                            return 0
                                        }
                                    }
                                    /* Récuperation de toute les données nécessaire à la création du graphique */

                                    totalFans = 0;
                                    totalFans += verifValeur(data.data[0].values[0].value["F.13-17"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["F.18-24"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["F.25-34"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["F.35-44"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["F.45-54"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["F.55-64"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["F.65+"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["M.13-17"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["M.18-24"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["M.25-34"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["M.35-44"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["M.45-54"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["M.55-64"]);
                                    totalFans += verifValeur(data.data[0].values[0].value["M.65+"]);
                                    var totalFemme = 0;
                                    var totalHomme = 0;
                                    totalFemme += femme13 = verifValeur(data.data[0].values[0].value["F.13-17"]);
                                    totalFemme += femme18 = verifValeur(data.data[0].values[0].value["F.18-24"]);
                                    totalFemme += femme25 = verifValeur(data.data[0].values[0].value["F.25-34"]);
                                    totalFemme += femme35 = verifValeur(data.data[0].values[0].value["F.35-44"]);
                                    totalFemme += femme45 = verifValeur(data.data[0].values[0].value["F.45-54"]);
                                    totalFemme += femme55 = verifValeur(data.data[0].values[0].value["F.55-64"]);
                                    totalFemme += femme65 = verifValeur(data.data[0].values[0].value["F.65+"]);
                                    totalHomme += homme13 = verifValeur(data.data[0].values[0].value["M.13-17"]);
                                    totalHomme += homme18 = verifValeur(data.data[0].values[0].value["M.18-24"]);
                                    totalHomme += homme25 = verifValeur(data.data[0].values[0].value["M.25-34"]);
                                    totalHomme += homme35 = verifValeur(data.data[0].values[0].value["M.35-44"]);
                                    totalHomme += homme45 = verifValeur(data.data[0].values[0].value["M.45-54"]);
                                    totalHomme += homme55 = verifValeur(data.data[0].values[0].value["M.55-64"]);
                                    totalHomme += homme65 = verifValeur(data.data[0].values[0].value["M.65+"]);

                                    totalFemme = (totalFemme/totalFans*100).toFixed(2)
                                    totalHomme = (totalHomme/totalFans*100).toFixed(2)
                                    console.log("Femme " + totalFemme);
                                    console.log("Homme " + totalHomme);

                                    /* Definition et affichage du graphique */
                                    am4core.ready(function() {

                                        // Themes begin
                                        am4core.useTheme(am4themes_animated);
                                        // Themes end

                                        // Create chart instance
                                        var chart = am4core.create("chartdiv", am4charts.XYChart);

                                        // Add data
                                        chart.data = [{
                                            "age": "65+",
                                            "female": -Math.abs((femme65/totalFans*100).toFixed(2)),
                                            "male": (homme65/totalFans*100).toFixed(2)
                                        }, {
                                            "age": "55-64",
                                            "female": -Math.abs((femme55/totalFans*100).toFixed(2)),
                                            "male": (homme55/totalFans*100).toFixed(2)
                                        }, {
                                            "age": "45-54",
                                            "female": -Math.abs((femme45/totalFans*100).toFixed(2)),
                                            "male": (homme45/totalFans*100).toFixed(2)
                                        }, {
                                            "age": "35-44",
                                            "female": -Math.abs((femme35/totalFans*100).toFixed(2)),
                                            "male": (homme35/totalFans*100).toFixed(2)
                                        }, {
                                            "age": "25-34",
                                            "female": -Math.abs((femme25/totalFans*100).toFixed(2)),
                                            "male": (homme25/totalFans*100).toFixed(2)
                                        }, {
                                            "age": "18-24",
                                            "female": -Math.abs((femme18/totalFans*100).toFixed(2)),
                                            "male": (homme18/totalFans*100).toFixed(2)
                                        }, {
                                            "age": "13-17",
                                            "female": -Math.abs((femme13/totalFans*100).toFixed(2)),
                                            "male": (homme13/totalFans*100).toFixed(2)
                                        }];

                                        // Use only absolute numbers
                                        chart.numberFormatter.numberFormat = "#.#s";

                                        // Create axes
                                        var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
                                        categoryAxis.dataFields.category = "age";
                                        categoryAxis.renderer.grid.template.location = 0;
                                        categoryAxis.renderer.inversed = true;

                                        var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
                                        valueAxis.extraMin = 0.1;
                                        valueAxis.extraMax = 0.1;
                                        valueAxis.renderer.minGridDistance = 40;
                                        valueAxis.renderer.ticks.template.length = 5;
                                        valueAxis.renderer.ticks.template.disabled = false;
                                        valueAxis.renderer.ticks.template.strokeOpacity = 0.4;
                                        valueAxis.renderer.labels.template.adapter.add("text", function(text) {
                                            return text == "Male" || text == "Female" ? text : text + "%";
                                        })

                                        // Create series
                                        var male = chart.series.push(new am4charts.ColumnSeries());
                                        male.dataFields.valueX = "female";
                                        male.dataFields.categoryY = "age";
                                        male.clustered = false;

                                        var maleLabel = male.bullets.push(new am4charts.LabelBullet());
                                        maleLabel.label.text = "{valueX}%";
                                        maleLabel.label.hideOversized = false;
                                        maleLabel.label.truncate = false;
                                        maleLabel.label.horizontalCenter = "right";
                                        maleLabel.label.dx = -10;

                                        var female = chart.series.push(new am4charts.ColumnSeries());
                                        female.dataFields.valueX = "male";
                                        female.dataFields.categoryY = "age";
                                        female.clustered = false;

                                        var femaleLabel = female.bullets.push(new am4charts.LabelBullet());
                                        femaleLabel.label.text = "{valueX}%";
                                        femaleLabel.label.hideOversized = false;
                                        femaleLabel.label.truncate = false;
                                        femaleLabel.label.horizontalCenter = "left";
                                        femaleLabel.label.dx = 10;

                                        var maleRange = valueAxis.axisRanges.create();
                                        maleRange.value = -10;
                                        maleRange.endValue = 0;
                                        maleRange.label.text = "Femme "+ totalFemme;
                                        maleRange.label.fill = chart.colors.list[0];
                                        maleRange.label.dy = 20;
                                        maleRange.label.fontWeight = '600';
                                        maleRange.grid.strokeOpacity = 1;
                                        maleRange.grid.stroke = male.stroke;

                                        var femaleRange = valueAxis.axisRanges.create();
                                        femaleRange.value = 0;
                                        femaleRange.endValue = 10;
                                        femaleRange.label.text = "Homme " + totalHomme;
                                        femaleRange.label.fill = chart.colors.list[1];
                                        femaleRange.label.dy = 20;
                                        femaleRange.label.fontWeight = '600';
                                        femaleRange.grid.strokeOpacity = 1;
                                        femaleRange.grid.stroke = female.stroke;

                                    }); 
                                    $("#progress_bar").val("40");
                                });

                                $("#progress_bar").val("50");

                                /* Définition d'un objet qui regroupe toute les données utilisé dans la géneration du PPTX */
                                var donneesPowerPoint = [];
                                donneesPowerPoint.trimestre = trimestre;
                                donneesPowerPoint.mois = tabMois
                                donneesPowerPoint.topPostMois = topMedia;
                                donneesPowerPoint.top3Reach = tabReach;
                                donneesPowerPoint.top3Interaction = tabInteraction;
                                donneesPowerPoint.top3FlopReach = flopReach;
                                donneesPowerPoint.storieInsta = tabStorie;
                                donneesPowerPoint.top3ReachStories = top3ReachStories;
                                $("#progress_bar").val("70");

                                /* Création du powerpoint */
                                var pptx = new PptxGenJS();
                                /* Ajout d'une diapositive */
                                var slide = pptx.addSlide();

                                html2canvas(document.body).then(function(canvas) {
                                    //on récupère les graphiques de la page et les images dans notre dossier
                                    var node = document.getElementById('myAreaChart2');
                                    var node2 = document.getElementById('chartdiv');
                                    //domtoimage va servir a faire une capture de nos canvas (graphiques) et des les convertir en image base64
                                    domtoimage.toPng(node).then(function (dataUrl) {
                                        domtoimage.toPng(node2).then(function (dataUrl2) {
                                            var img = new Image();
                                            img.src = dataUrl;
                                            var img5 = new Image();
                                            img5.src = dataUrl2;
                                            var pageGuarde = "images/capture3.png";
                                            var header = "images/bandeau1.png";
                                            var fin = "images/fin.png";
                                            var screenPage = "images/screen-page.png";
                                            var screenPost = "images/screen-post1.png";

                                            //page de garde
                                            slide.addImage({ path:pageGuarde, x:0, y:0, w:10, h:5.6 });

                                            //page de garde
                                            slide = pptx.addSlide();
                                            slide.bkgd = 'f1bf00';
                                            slide.addText( nomPageInsta ,  { x:'30%', y:'25%', w:4, color:'FFFFFF', fontFace:'Avenir 85 Heavy', align: 'center', fontSize:45 });
                                            slide.addText('PAGE Instagram',  { x:'35%', y:'40%', w:3, color:'FFFFFF', fontFace:'Avenir 85 Heavy', align: 'center', fontSize:22 });
                                            slide.addText('BILAN trimestriel',  { x:'30%', y:'57%', w:4, color:'FFFFFF', align: 'center', fontFace:'Avenir 85 Heavy', fontSize:30 });
                                            slide.addText(donneesPowerPoint.mois[0].mois + ' / ' + donneesPowerPoint.mois[1].mois + ' / ' + donneesPowerPoint.mois[2].mois + ' 2020',  { x:'25%', y:'72%', w:5, color:'FFFFFF', fontFace:'Avenir 85 Heavy', align: 'center', fontSize:22 });

                                            $("#progress_bar").val("85");
                                            
                                            // INFO TRIMESTRIEL
                                            slide = pptx.addSlide();
                                            slide.addText([
                                                { text:  ((donneesPowerPoint.trimestre.totalInteraction/donneesPowerPoint.trimestre.totalReach)*100).toFixed(2) + ' %', options: {bold:true, fontSize:12}},
                                                { text: ' Taux d\'interaction moyen', options: {}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'11%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                            slide.addText([
                                                { text: donneesPowerPoint.trimestre.totalFollowerGagne.toLocaleString(), options: {bold:true, fontSize:12}},
                                                { text: ' abonnés gagné', options: {}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'22%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                            slide.addText([
                                                { text:  donneesPowerPoint.trimestre.nbMedia.toLocaleString(), options: {bold:true, fontSize:12}},
                                                { text: ' posts', options: {}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'33%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                            slide.addText([
                                                { text: donneesPowerPoint.trimestre.totalInteraction.toLocaleString(), options: {bold:true, fontSize:12}},
                                                { text: ' interactions soit une moyenne de ', options: {}},
                                                { text: (donneesPowerPoint.trimestre.totalInteraction/donneesPowerPoint.trimestre.nbMedia).toFixed(0) + '/post', options: {bold:true}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'44%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                            slide.addText([
                                                { text: donneesPowerPoint.trimestre.totalReach.toLocaleString(), options: {bold:true, fontSize:12}},
                                                { text: ' reach total posts', options: {}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'55%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                            
                                            slide.addText([
                                                { text: donneesPowerPoint.trimestre.nbStories.toLocaleString(), options: {bold:true, fontSize:12}},
                                                { text: ' stories', options: {}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'66%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                            
                                            slide.addText([
                                                { text: donneesPowerPoint.trimestre.totalReachStories.toLocaleString(), options: {bold:true, fontSize:12}},
                                                { text: ' reach total stories soit une moyenne de ', options: {}},
                                                { text: donneesPowerPoint.trimestre.ReachStoriesMoyen + '/story', options: {bold:true}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'77%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                            
                                            slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                            slide.addText('LE COMPTE INSTAGRAM',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                            slide.addImage({ path:screenPage, x:"18%", y:"18%", w:"64%", h:"55%" });
                                            
                                            // Chiffre clés MOIS PAR MOIS
                                            slide = pptx.addSlide();
                                            slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                            slide.addText('CHIFFRES CLES',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                            var posXShape = 11
                                            var posYShape = 25
                                            var posXTitre = 6;
                                            var posYTitre = 20;
                                            donneesPowerPoint.mois.forEach(mois => {
                                                slide.addText(mois.mois,  { x:posXTitre + '%', y:posYTitre + '%', w:'100%', color:'000000', fontSize:18 });
                                                posYTitre+= 27;
                                                slide.addText([
                                                    { text: mois.tauxInteraction.toLocaleString() + ' %', options: {bold:true, fontSize:12}},
                                                    { text: ' Taux d\'interaction', options: {}}
                                                ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape + '%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                posXShape += 11
                                                slide.addText([
                                                    { text: mois.totalFollowerGagneMensuel.toLocaleString(), options: {bold:true, fontSize:12}},
                                                    { text: ' abonnés gagné', options: {}}
                                                ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape + '%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                posXShape += 11
                                                slide.addText([
                                                    { text: mois.nbMediaMensuel.toLocaleString(), options: {bold:true, fontSize:12}},
                                                    { text: ' posts', options: {}}
                                                ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape +'%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                posXShape += 11
                                                if (mois.nbMediaMensuel == 0) {
                                                    slide.addText([
                                                        { text: mois.totalInteractionMensuel.toLocaleString(), options: {bold:true, fontSize:12}},
                                                        { text: ' interactions', options: {}},
                                                    ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape + '%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                } else {
                                                    slide.addText([
                                                        { text: mois.totalInteractionMensuel.toLocaleString(), options: {bold:true, fontSize:12}},
                                                        { text: ' interactions soit une moyenne de ', options: {}},
                                                        { text: Math.round(mois.totalInteractionMensuel / mois.nbMediaMensuel).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '/post', options: {bold:true}}
                                                    ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape + '%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                }
                                                posXShape += 11
                                                slide.addText([
                                                    { text: mois.totalReachMensuel.toString(), options: {bold:true, fontSize:12}},
                                                    { text: ' reach total posts', options: {}}
                                                ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape + '%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                posXShape += 11
                                                slide.addText([
                                                    { text: mois.nbStorie.toString(), options: {bold:true, fontSize:12}},
                                                    { text: ' stories', options: {}}
                                                ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape + '%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                
                                                posXShape += 11
                                                slide.addText([
                                                    { text: mois.totalreach.toString(), options: {bold:true, fontSize:12}},
                                                    { text: ' reach total stories soit une moyenne de ', options: {}},
                                                    { text: mois.personneAtteintesMoyen + '/story', options: {bold:true}}
                                                ], { shape:pptx.shapes.RECTANGLE, align:'center', x:posXShape + '%', y:posYShape + '%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                posXShape = 11;
                                                posYShape += 27;
                                            });
                                            
                                            // FOCUS FOLLOWER
                                            slide = pptx.addSlide();
                                            slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                            slide.addText('FOCUS FOLLOWER',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                            slide.addImage({ data:img5.src, x:1, y:1, w:8, h:4 });

                                            // FOCUS LIKE
                                            slide = pptx.addSlide();
                                            slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                            slide.addText('FOCUS LIKE',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                            slide.addImage({ data:img.src, x:0.2, y:1, w:6.1, h:2.5 });
                                            slide.addImage({ path:screenPost, x:"67%", y:"18%", w:"28%", h:"42%" });
                                            
                                            // TOP POST
                                            slide = pptx.addSlide();
                                            slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                            slide.addText('TOP POST',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                            slide.addText([
                                                { text: 'TOP REACH', options: {}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'55%', y:'17%', w:2.2, h:0.4, fill:'0088CC', line:'006699', lineSize:2 , fontSize:13, color:'FFFFFF'});
                                            slide.addText([
                                                { text: 'TOP INTERACTION*', options: {}}
                                            ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'55%', y:'25%', w:2.2, h:0.4, fill:'0088CC', line:'006699', lineSize:2 , fontSize:13, color:'FFFFFF'});
                                            slide.addImage({ path:donneesPowerPoint.topPostMois.media_url, x:"10%", y:"18%", w:"40%", h:"65%" });
                                            slide.addText([
                                                { text: 'Date du post : ', options: {bold:true}},
                                                { text: donneesPowerPoint.topPostMois.date, options: {}}
                                            ],  { x:'55%', y:'35%', w:'100%', color:'000000', fontSize:15 });
                                            slide.addText('Thème : ',  { x:'55%', y:'40%', w:'100%', color:'000000', fontSize:15 });
                                            slide.addText('Format : ',  { x:'55%', y:'45%', w:'100%', color:'000000', fontSize:15 });
                                            slide.addText([
                                                { text: (donneesPowerPoint.topPostMois.comments_count == "" ? '0' : donneesPowerPoint.topPostMois.comments_count), options: {}},
                                                { text: ' Commentaires', options: {bold:true}}
                                            ],  { x:'55%', y:'50%', w:'100%', color:'000000', fontSize:15 });
                                            slide.addText([
                                                { text: (donneesPowerPoint.topPostMois.like_count == "" ? '0' : donneesPowerPoint.topPostMois.like_count), options : {}},
                                                { text: ' Likes', options: {bold:true}}
                                            ],  { x:'55%', y:'55%', w:'100%', color:'000000', fontSize:15 });
                                            slide.addText([
                                                { text: (donneesPowerPoint.topPostMois.reach == "" ? '0' : donneesPowerPoint.topPostMois.reach), options: {}},
                                                { text: ' Personnes atteintes', options: {bold:true}}
                                            ],  { x:'55%', y:'60%', w:'100%', color:'0088CC', fontSize:15 });
                                            slide.addText([
                                                { text: donneesPowerPoint.topPostMois.TauxInteraction, options: {}},
                                                { text: '% Taux d\'interaction', options: {bold:true}}
                                            ],  { x:'55%', y:'65%', w:'100%', color:'000000', fontSize:15 });
                                            if (donneesPowerPoint.topPostMois.hasOwnProperty('nbVueVideo')) {
                                                slide.addText([
                                                    { text: donneesPowerPoint.topPostMois.nbVueVideo.toLocaleString(), options: {}},
                                                    { text: ' vues ', options: {bold:true}}
                                                ],  { x:'55%', y:'70%', w:'100%', color:'000000', fontSize:15 });

                                            }
                                            function ajoutTop3(tableau,nomTop) {
                                                slide = pptx.addSlide();
                                                slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                                slide.addText('TOP 3',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                slide.addText([
                                                    { text: nomTop, options: {}}
                                                ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'25%', y:'4%', w:2.5, h:0.3, fill:'0088CC', line:'006699', lineSize:2 , fontSize:15, color:'FFFFFF'});
                                                var posXImage = 5;
                                                var posYImage = 18;
                                                var posXText = 5;
                                                var posYText = 60;
                                                tableau.forEach(post => {
                                                    slide.addImage({ path:post.media_url, x:posXImage + "%", y: posYImage + "%", w:"22%", h:"39%" });
                                                    slide.addText([
                                                        { text: 'Date du post : ', options: {bold:true}},
                                                        { text: post.date, options: {}}
                                                    ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                    posYText+=4;
                                                    slide.addText('Thème : ',  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                    posYText+=4;
                                                    slide.addText('Format : ',  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                    posYText+=4;
                                                    slide.addText([
                                                        { text: post.comments_count.toLocaleString(), options: {}},
                                                        { text: ' Commentaires', options: {bold:true}}
                                                    ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                    posYText+=4;
                                                    slide.addText([
                                                        { text: post.like_count.toLocaleString(), options: {}},
                                                        { text: ' Likes', options: {bold:true}}
                                                    ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                    posYText+=4;
                                                    slide.addText([
                                                        { text: post.reach.toLocaleString(), options: {}},
                                                        { text: ' Personnes atteintes', options: {bold:true}}
                                                    ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'0088CC', fontSize:10 });
                                                    posYText+=4;
                                                    slide.addText([
                                                        { text: post.TauxInteraction.toLocaleString(), options: {}},
                                                        { text: '% Taux d\'interaction', options: {bold:true}}
                                                    ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                    if (post.hasOwnProperty('nbVueVideo')) {
                                                        posYText+=4;
                                                        slide.addText([
                                                            { text: post.nbVueVideo.toLocaleString(), options: {}},
                                                            { text: ' vues ', options: {bold:true}}
                                                        ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });

                                                    }
                                                    posYText = 60
                                                    posXText += 35
                                                    posXImage += 35
                                                    numMedia++;
                                                });
                                            }

                                            // TOP 3 TAUX D'INTERACTION
                                            ajoutTop3(donneesPowerPoint.top3Interaction,'Top interaction')
                                            
                                            // TOP 3 REACH
                                            ajoutTop3(donneesPowerPoint.top3Reach,'Top reach')
                                            
                                            // FLOP 3 REACH
                                            ajoutTop3(donneesPowerPoint.top3FlopReach,'Flop reach')

                                            // TOP 3 REACH STORIES
                                            if (donneesPowerPoint.top3ReachStories.length > 0) {
                                                slide = pptx.addSlide();
                                                slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                                slide.addText('TOP 3 Stories',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                var numMedia = 1;
                                                var posXImage = 5
                                                var posYImage = 18
                                                var posXText = 5
                                                var posYText = 60;

                                                for (let index = 0; index < 3; index++) {
                                                    if (donneesPowerPoint.top3ReachStories.length >= numMedia) {
                                                        slide.addImage({ path:screenPost, x:posXImage + "%", y: posYImage + "%", w:"22%", h:"39%" });
                                                        
                                                        slide.addText([
                                                            { text: 'Date story : ', options: {bold:true}},
                                                            { text: donneesPowerPoint.top3ReachStories[numMedia-1].date, options: {}}
                                                        ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                        posYText+=4;
                                                        slide.addText('Thème : ',  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                        posYText+=4;
                                                        slide.addText('Format : ',  { x:posXText + '%', y:posYText + '%', w:'100%', color:'000000', fontSize:10 });
                                                        posYText+=4;
                                                        slide.addText([
                                                            { text: donneesPowerPoint.top3ReachStories[numMedia-1].reach.toString(), options: {}},
                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                        ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'0088CC', fontSize:10 });
                                                        posYText+=4;
                                                        slide.addText([
                                                            { text: donneesPowerPoint.top3ReachStories[numMedia-1].impression.toString(), options: {}},
                                                            { text: ' vues', options: {bold:true}}
                                                        ],  { x:posXText + '%', y:posYText + '%', w:'100%', color:'0088CC', fontSize:10 });
                                                    }
                                                    posYText = 60
                                                    posXText += 35
                                                    posXImage += 35
                                                    numMedia++;
                                                }
                                            }

                                            // LISTE DES POST
                                            var numMedia = 1;
                                            for (let diapo = 0; diapo < 3; diapo++) {
                                                var posXImage = 7
                                                var posYImage = 15
                                                var posXText = 18
                                                var posYText = 22;
                                                if (tabPost.length >= numMedia) {
                                                    slide = pptx.addSlide();
                                                    slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                                    for (let index = 0; index < 3; index++) {

                                                        for (let i = 0; i < 4; i++) {
                                                            if (tabPost.length >= numMedia) {
                                                                slide.addImage({ path: tabPost[numMedia-1].media_url, x:posXImage + "%", y:posYImage + "%", w:"10%", h:"20%" });
                                                                slide.addText([
                                                                        { text:  tabPost[numMedia-1].reach, options: {}},
                                                                        { text: ' personnes atteintes', options: {bold:true}}
                                                                    ],  { x:posXText + '%', y: posYText + '%', w:'100%', color:'0088CC', fontSize:10 });
                                                                slide.addText([
                                                                    { text: tabPost[numMedia-1].TauxInteraction, options: {}},
                                                                    { text: ' % Taux d\'interaction', options: {bold:true}}
                                                                ],  { x:posXText + '%', y:(posYText + 5) +'%', w:'100%', color:'000000', fontSize:10 }); 
                                                            }
                                                            /*  */
                                                            posYImage += 21;
                                                            posYText += 21;
                                                            numMedia++;
                                                        }
                                                    /* Modification de la position de la colonne */
                                                    posXImage += 30;
                                                    posXText += 30; 
                                                    /* Reset pour remonter en haut de la colonne */
                                                    posYText = 22; 
                                                    posYImage = 15;
                                                    }
                                                }
                                            }

                                            // CONCLUSION
                                            slide = pptx.addSlide();
                                            slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                            slide.addText('CONCLUSION',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });

                                            // CONCLUSION ET RECOMMANDATIONS
                                            slide = pptx.addSlide();
                                            slide.addImage({ path:header, x:0, y:0, w:10, h:0.8 });
                                            slide.addText('CONCLUSION ET RECOMMANDATIONS',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });

                                            // FIN
                                            slide = pptx.addSlide();
                                            slide.addImage({ path:fin, x:0, y:0, w:'100%', h:'100%' });
                                            slide.addText('Merci',  { x:'35%', y:'40%', w:3, color:'FFFFFF', align: 'center', fontFace:'Avenir 85 Heavy', fontSize:35 });
                                            
                                            $("#progress_bar").val("100");

                                            //on enregistre le powerpoint
                                            pptx.writeFile('bilan-reporting-Instagram ' + donneesPowerPoint.mois[0].mois + ' - ' + donneesPowerPoint.mois[1].mois  + ' - ' + donneesPowerPoint.mois[2].mois + ' ' +  nomPageInsta);
                                        }).catch(function (error) {
                                            console.error('oops, something went wrong!', error);
                                        });
                                        //fin du 2eme domtoimage
                                        
                                    }).catch(function (error) {
                                        console.error('oops, something went wrong!', error);
                                    });
                                    //fin du 1er domtoimage
                                });
                                /* Fin Export PPTX */
                                }
                            /* Récuperation de tout les post de la page Instagram choisi */
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