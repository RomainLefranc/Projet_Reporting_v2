<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Nautilus Social Manager - Export PPTX Instagral</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <script src="vendor/chart.js/Chart.min.js"></script>


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php
            include 'view/admin/sidebar.php'
        ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php
                    include 'view/admin/navbar.php'
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
                            <label for="example-page-input" class="col-lg-2 col-form-label">Pages disponible</label>
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
                            <label for="example-date-input" class="col-lg-2 col-form-label">Mois</label>
                            <div class="col-lg-10">
                            <input class="form-control" type="month" id="dateDebut" style="max-width: 300px;" required>
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
                        <div class="card-body">
                            <canvas id="myAreaChart2" width="100%" height="30"></canvas>
                        </div>
                    </div>

                    <div id="chartdiv" style='width: 100%;height: 500px;'></div>
                    <div id="editor"></div><br/>
                    <div class="top">
                        <h3>Top post du mois</h3>
                        <div class="row" id="topMedia" ></div>
                    </div>
                    <div class="top">
                        <h3>Top 3 reach du mois</h3>
                        <div class="row" id="top3Reach" ></div>
                    </div>
                    <div class="top">
                        <h3>Top 3 interaction du mois</h3>
                        <div class="row" id="top3Interaction"></div>
                    </div>

                                    <!-- Resources -->
                    <script src="https://www.amcharts.com/lib/4/core.js"></script>
                    <script src="https://www.amcharts.com/lib/4/charts.js"></script>
                    <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                    <script>
                        $('.top').hide();
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
                        $('#bilan').submit(function (e) { 
                            $('#top3Interaction').html();
                            $('#top3Reach').html();
                            $('#topMedia').html();
                            e.preventDefault();
                            $('#erreur').html('');
                            $("#progress_bar").val("0");
                            //on réinitialise le contenu de le l'HTML contenant le canvas, évitant un bug d'affichage
                            $(".card-body").html("");
                            $(".card-body").html('<canvas id="myAreaChart2" width="100%" height="30"></canvas>');

                            var idPageInsta = $('#choixPageInsta').val();

                            var token = $('#choixPageInsta').find('option:selected').data('value');
                            var mois = $('#dateDebut').val();

                            mois = new Date(mois);

                            var dateDebut = new Date(mois.getFullYear(), mois.getMonth(), 1);
                            var dateSince = dateDebut.getFullYear() + '-' + ((dateDebut.getMonth() > 8) ? (dateDebut.getMonth() + 1) : ('0' + (dateDebut.getMonth() + 1))) + '-' + ((dateDebut.getDate() > 9) ? dateDebut.getDate() : ('0' + dateDebut.getDate()));

                            var dateFin = new Date(mois.getFullYear(), mois.getMonth() + 1, 0);
                            var dateUntil = dateFin.getFullYear() + '-' + ((dateFin.getMonth() > 8) ? (dateFin.getMonth() + 1) : ('0' + (dateFin.getMonth() + 1))) + '-' + ((dateFin.getDate() > 9) ? dateFin.getDate() : ('0' + dateFin.getDate()));
                            
                            var url = `https://graph.facebook.com/v8.0/${idPageInsta}?fields=id,media{id,caption,like_count,media_type,comments_count,thumbnail_url,media_url,timestamp}&access_token=${token}`;
                            $.get(url, function (data) {

                               /*  if (data.data.length > 0) {
                                    
                                } else {
                                    
                                } */
                                var tabDateMedia = [];
                                var tabEngagementMedia = [];
                                var nbMediaMensuel = 0;
                                var totalInteractionMensuel = 0;
                                var totalReachMensuel = 0
                                var totalImpressionMensuel = 0;
                                var tabPost = [];
                                data.media.data.forEach(media => {
                                    var dateMedia = new Date(media.timestamp);
                                    if (dateMedia.getMonth() + 1 == mois.getMonth() + 1) {
                                        nbMediaMensuel++;
                                        media.date = ((dateMedia.getDate() > 9) ? dateMedia.getDate() : ('0' + dateMedia.getDate())) + '/' + ((dateMedia.getMonth() > 8) ? (dateMedia.getMonth() + 1) : ('0' + (dateMedia.getMonth() + 1))) + '/' + dateMedia.getFullYear() + ' ' + dateMedia.getHours() + 'h' +  ((dateMedia.getMinutes() > 9) ? dateMedia.getMinutes() : ('0' + dateMedia.getMinutes()));
                                        var date = ((dateMedia.getDate() > 9) ? dateMedia.getDate() : ('0' + dateMedia.getDate())) + '/' + ((dateMedia.getMonth() > 8) ? (dateMedia.getMonth() + 1) : ('0' + (dateMedia.getMonth() + 1))) + '/' + dateMedia.getFullYear() + ' ' + dateMedia.getHours() + 'h' +  ((dateMedia.getMinutes() > 9) ? dateMedia.getMinutes() : ('0' + dateMedia.getMinutes()));
                                        tabDateMedia.push(date);
                                        tabEngagementMedia.push(media.like_count + media.comments_count);
                                        totalInteractionMensuel += media.like_count + media.comments_count;
                                        var idMedia = media.id;
                                        var url = '';
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
                                                media.impression = response.data[0].values[0].value
                                                totalImpressionMensuel += response.data[0].values[0].value
                                                media.reach = response.data[1].values[0].value
                                                totalReachMensuel += response.data[1].values[0].value
                                                media.interaction = response.data[2].values[0].value
                                                if (response.data.length == 4) {
                                                    media.nbVueVideo = response.data[3].values[0].value
                                                } else {
                                                    media.nbVueVideo = 0;
                                                }
                                            }
                                        });
                                        tabPost.push(media);
                                    }
                                });
                                console.log(totalInteractionMensuel + ' Interaction dans le mois, soit une moyenne de ' + (totalInteractionMensuel/nbMediaMensuel).toFixed(0) + ' par post');
                                console.log(totalImpressionMensuel + ' visites sur les post dont ' + totalReachMensuel + ' visiteurs unique');
                                console.log("Taux d'interaction moyenne de :" + ((totalInteractionMensuel/totalReachMensuel)*100).toFixed(2)) + ' %';
                                console.log(nbMediaMensuel + " post");

                                /* Tableau top 3 interaction */
                                var tabInteraction = [...tabPost];
                                tabInteraction.sort(function (a,b) { return b.interaction - a.interaction });

                                /* Tableau top 3 reach */
                                var tabReach = [...tabPost];
                                tabReach.sort(function (a,b) { return b.reach - a.reach });

                                /* top 1 post */
                                var topMedia = [...tabPost];
                                topMedia.sort(function(a,b) {return b.reach - a.reach || b.interaction - a.interaction})
                                topMedia = topMedia[0];

                                /* Tableau top 3 flop reach */
                                var flopReach = [...tabPost];
                                flopReach.sort(function (a,b) {return a.reach - b.reach});

                                /* var carte = `
                                    <div class="card m-2" style="width: 18rem;">
                                        <img class="card-img-top" src="${topMedia.media_url}">
                                        <div class="card-body">
                                            <p class="card-text">
                                            <strong>Date : </strong>${topMedia.date}<br>
                                            <strong>like : </strong>${topMedia.like_count}<br>
                                            <strong>Commentaire : </strong>${topMedia.comments_count}<br>
                                            <strong>Impression : </strong>${topMedia.impression}<br>
                                            <strong>Vue videos : </strong>${topMedia.nbVueVideo}<br>
                                            <strong>Reach : </strong>${topMedia.reach}<br>
                                            <strong>Taux d'engagement </strong>${(((topMedia.interaction)/topMedia.reach)*100).toFixed(2)+' %'}
                                            </p>
                                        </div>
                                    </div>`;
                                $('#topMedia').append(carte);

                                var top3Reach = '';
                                for (let index = 0; index < 3; index++) {
                                    top3Reach += `
                                    <div class="card m-2" style="width: 18rem;" id="${index}">
                                        <img class="card-img-top" src="${tabReach[index].media_url}">
                                        <div class="card-body">
                                            <p class="card-text">
                                            <strong>Date : </strong>${tabReach[index].date}<br>
                                            <strong>like : </strong>${tabReach[index].like_count}<br>
                                            <strong>Commentaire : </strong>${tabReach[index].comments_count}<br>
                                            <strong>Impression : </strong>${tabReach[index].impression}<br>
                                            <strong>Vue videos : </strong>${tabReach[index].nbVueVideo}<br>
                                            <strong>Reach : </strong>${tabReach[index].reach}<br>
                                            <strong>Taux d'engagement </strong>${(((tabReach[index].interaction)/tabReach[index].reach)*100).toFixed(2)+' %'}
                                            </p>
                                        </div>
                                    </div>`;
                                }
                                $('#top3Reach').append(top3Reach);

                                var top3Interaction = '';
                                for (let index = 0; index < 3; index++) {
                                    top3Interaction += `
                                    <div class="card m-2" style="width: 18rem;" id="${index}">
                                        <img class="card-img-top" src="${tabInteraction[index].media_url}">
                                        <div class="card-body">
                                            <p class="card-text">
                                            <strong>Date : </strong>${tabInteraction[index].date}<br>
                                            <strong>like : </strong>${tabInteraction[index].like_count}<br>
                                            <strong>Commentaire : </strong>${tabInteraction[index].comments_count}<br>
                                            <strong>Impression : </strong>${tabInteraction[index].impression}<br>
                                            <strong>Vue videos : </strong>${tabInteraction[index].nbVueVideo}<br>
                                            <strong>Reach : </strong>${tabInteraction[index].reach}<br>
                                            <strong>Taux d'engagement </strong>${(((tabInteraction[index].interaction)/tabInteraction[index].reach)*100).toFixed(2)+' %'}
                                            </p>
                                        </div>
                                    </div>`;
                                }
                                $('#top3Interaction').append(top3Interaction);
                                $('.top').show();
                                 */

                                //on défini l'échelle maximale de notre graphique
                                var max = Math.ceil(Math.max(...tabEngagementMedia)) + 5;
                                var ctx = document.getElementById("myAreaChart2");

                                var myLineChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: tabDateMedia,
                                        datasets: [{
                                            label: "Engagement",
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
                                            data: tabEngagementMedia,
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

                                var url = `https://graph.facebook.com/v4.0/${idPageInsta}/insights/audience_gender_age/lifetime?&access_token=${token}`;               
                                $.get(url,function (data) {
                                    
                                    function verifValeur(params) {
                                        if (typeof params === 'number') {
                                            return params
                                        } else {
                                            return 0
                                        }
                                    }

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
                                    
                                    femme13 = verifValeur(data.data[0].values[0].value["F.13-17"]);
                                    femme18 = verifValeur(data.data[0].values[0].value["F.18-24"]);
                                    femme25 = verifValeur(data.data[0].values[0].value["F.25-34"]);
                                    femme35 = verifValeur(data.data[0].values[0].value["F.35-44"]);
                                    femme45 = verifValeur(data.data[0].values[0].value["F.45-54"]);
                                    femme55 = verifValeur(data.data[0].values[0].value["F.55-64"]);
                                    femme65 = verifValeur(data.data[0].values[0].value["F.65+"]);
                                    homme13 = verifValeur(data.data[0].values[0].value["M.13-17"]);
                                    homme18 = verifValeur(data.data[0].values[0].value["M.18-24"]);
                                    homme25 = verifValeur(data.data[0].values[0].value["M.25-34"]);
                                    homme35 = verifValeur(data.data[0].values[0].value["M.35-44"]);
                                    homme45 = verifValeur(data.data[0].values[0].value["M.45-54"]);
                                    homme55 = verifValeur(data.data[0].values[0].value["M.55-64"]);
                                    homme65 = verifValeur(data.data[0].values[0].value["M.65+"]);

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
                                        maleRange.label.text = "Femme";
                                        maleRange.label.fill = chart.colors.list[0];
                                        maleRange.label.dy = 20;
                                        maleRange.label.fontWeight = '600';
                                        maleRange.grid.strokeOpacity = 1;
                                        maleRange.grid.stroke = male.stroke;

                                        var femaleRange = valueAxis.axisRanges.create();
                                        femaleRange.value = 0;
                                        femaleRange.endValue = 10;
                                        femaleRange.label.text = "Homme";
                                        femaleRange.label.fill = chart.colors.list[1];
                                        femaleRange.label.dy = 20;
                                        femaleRange.label.fontWeight = '600';
                                        femaleRange.grid.strokeOpacity = 1;
                                        femaleRange.grid.stroke = female.stroke;

                                    }); 
                                
                                });

                                var url = `https://graph.facebook.com/v4.0/${idPageInsta}/insights?metric=follower_count,reach,impressions&period=day&since=${dateSince}&until=${dateUntil}&access_token=${token}`;               
                                $.get(url, function (data) {
                                    var followergagneMensuel = 0;
                                    var reachMensuelPageInsta = 0;
                                    var impressionMensuelPageInsta = 0;

                                    data.data[0].values.forEach(element => {
                                        followergagneMensuel += element.value
                                    });
                                    data.data[1].values.forEach(element => {
                                        reachMensuelPageInsta += element.value
                                    });
                                    data.data[2].values.forEach(element => {
                                        impressionMensuelPageInsta += element.value
                                    });
                                    console.log(followergagneMensuel + ' abonnés gagné');
                                    console.log(impressionMensuelPageInsta + ' visites sur le compte Instagram dont ' + reachMensuelPageInsta + ' visiteurs unique');
                
                                });

                                function toDataURL(url, callback) {
                                    var xhr = new XMLHttpRequest();
                                    xhr.onload = function() {
                                        var reader = new FileReader();
                                        reader.onloadend = function() {
                                        callback(reader.result);
                                        }
                                        reader.readAsDataURL(xhr.response);
                                    };
                                    xhr.open('GET', url);
                                    xhr.responseType = 'blob';
                                    xhr.send();
                                }
                                /* var donneesPowerPoint = [];
                                donneesPowerPoint.topPostMois = topMedia;
                                donneesPowerPoint.top3ReachMois = tabReach;
                                donneesPowerPoint.top3Interaction = tabInteraction;
                                donneesPowerPoint.top3FlopReach = flopReach;
                                donneesPowerPoint.bilan[0] = moyenneTauxInteractionMois;
                                donneesPowerPoint.bilan[1] = nbFollowerGagne;
                                donneesPowerPoint.bilan[2] = totalInteractionMois;
                                donneesPowerPoint.bilan[3] = totalReachMois;
                                donneesPowerPoint.bilan[4] = nbMedia; */


                                /* toDataURL(imageMeilleurPost, function(dataUrl1) {
                                    toDataURL(imgTaux1, function(dataUrl12) {
                                        toDataURL(imgTaux2, function(dataUrl3) {
                                            toDataURL(imgTaux3, function(dataUrl4) {
                                                toDataURL(imgReach1, function(dataUrl5) {
                                                    toDataURL(imgReach2, function(dataUrl6) {
                                                        toDataURL(imgReach3, function(dataUrl7) {
                                                            toDataURL(imgDernierReach1, function(dataUrl8) {
                                                                toDataURL(imgDernierReach2, function(dataUrl9) {
                                                                    toDataURL(imgDernierReach3, function(dataUrl10) {
                                                                        //on initialise notre Powerpoint
                                                                        var pptx = new PptxGenJS();
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
                                                                                    var img2 = new Image();
                                                                                    img2.src = "images/capture3.png";
                                                                                    var img3 = new Image();
                                                                                    img3.src = "images/bandeau1.png";
                                                                                    var img4 = new Image();
                                                                                    img4.src = "images/fin.png";
                                                                                    var img6 = new Image();
                                                                                    img6.src = "images/screen-page.png";
                                                                                    var img7 = new Image();
                                                                                    img7.src = "images/screen-post1.png";

                                                                                    //page de garde
                                                                                    slide.addImage({ path:img2.src, x:0, y:0, w:10, h:5.6 });

                                                                                    //page de garde
                                                                                    slide = pptx.addSlide();
                                                                                    slide.bkgd = 'f1bf00';
                                                                                    slide.addText(selected.data('nom') ,  { x:'30%', y:'25%', w:4, color:'FFFFFF', fontFace:'Avenir 85 Heavy', align: 'center', fontSize:45 });
                                                                                    slide.addText('PAGE FACEBOOK',  { x:'35%', y:'40%', w:3, color:'FFFFFF', fontFace:'Avenir 85 Heavy', align: 'center', fontSize:22 });
                                                                                    slide.addText('BILAN Trimestriel',  { x:'30%', y:'57%', w:4, color:'FFFFFF', align: 'center', fontFace:'Avenir 85 Heavy', fontSize:30 });
                                                                                    slide.addText('(' + lesMois[0] + ' / ' + lesMois[1] + ' / ' + lesMois[2] + ') 2020',  { x:'25%', y:'72%', w:5, color:'FFFFFF', fontFace:'Avenir 85 Heavy', align: 'center', fontSize:22 });


                                                                                    $("#progress_selectionne").val("85");
                                                                                    
                                                                                    //seconde page "LA PAGE FACEBOOK"
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addText([
                                                                                            { text: taux.toFixed(2) + ' %', options: {bold:true, fontSize:12}},
                                                                                            { text: ' Taux d\'interaction', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'10%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: fans.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' fans', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'25%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: nbPosts, options: {bold:true, fontSize:12}},
                                                                                            { text: ' posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'40%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: mesInteractions.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' interactions soit une moyenne de ', options: {}},
                                                                                            { text: intParPost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '/post', options: {bold:true}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'55%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: mesReach.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' reach total posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'70%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: mesReachOrga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' reach organique soit ' + tauxReachOrganique.toFixed(2) + '%', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'85%', y:'80%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('LA PAGE FACEBOOK',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                                                    slide.addImage({ path:img6.src, x:"18%", y:"18%", w:"64%", h:"55%" });
                                                                                    
                                                                                    //troisième page CHIFFRES CLES
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('CHIFFRES CLES',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });

                                                                                    slide.addText(lesMois[0],  { x:'5%', y:'18%', w:'100%', color:'000000', fontSize:18 });
                                                                                    slide.addText([
                                                                                            { text: (interacParMois[0]/reachParMois[0]*100).toFixed(2) + ' %', options: {bold:true, fontSize:12}},
                                                                                            { text: ' Taux d\'interaction', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'15%', y:'25%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: fanMois[0].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' fans', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'30%', y:'25%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: postsParMois[0], options: {bold:true, fontSize:12}},
                                                                                            { text: ' posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'45%', y:'25%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: interacParMois[0].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' interactions soit une moyenne de ', options: {}},
                                                                                            { text: Math.round(interacParMois[0] / postsParMois[0]).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '/post', options: {bold:true}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'60%', y:'25%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: reachParMois[0].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' reach total posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'75%', y:'25%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    

                                                                                    slide.addText(lesMois[1],  { x:'5%', y:'45%', w:'100%', color:'000000', fontSize:18 });
                                                                                    slide.addText([
                                                                                            { text: (interacParMois[1]/reachParMois[1]*100).toFixed(2) + ' %', options: {bold:true, fontSize:12}},
                                                                                            { text: ' Taux d\'interaction', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'15%', y:'52%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: fanMois[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' fans', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'30%', y:'52%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: postsParMois[1], options: {bold:true, fontSize:12}},
                                                                                            { text: ' posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'45%', y:'52%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: interacParMois[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' interactions soit une moyenne de ', options: {}},
                                                                                            { text: Math.round(interacParMois[1] / postsParMois[1]).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '/post', options: {bold:true}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'60%', y:'52%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: reachParMois[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' reach total posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'75%', y:'52%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});

                                                                                    slide.addText(lesMois[2],  { x:'5%', y:'72%', w:'100%', color:'000000', fontSize:18 });
                                                                                    slide.addText([
                                                                                            { text: (interacParMois[2]/reachParMois[2]*100).toFixed(2) + ' %', options: {bold:true, fontSize:12}},
                                                                                            { text: ' Taux d\'interaction', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'15%', y:'79%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: fanMois[2].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' fans', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'30%', y:'79%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: postsParMois[2], options: {bold:true, fontSize:12}},
                                                                                            { text: ' posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'45%', y:'79%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: interacParMois[2].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' interactions soit une moyenne de ', options: {}},
                                                                                            { text: Math.round(interacParMois[2] / postsParMois[2]).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + '/post', options: {bold:true}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'60%', y:'79%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: reachParMois[2].toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "), options: {bold:true, fontSize:12}},
                                                                                            { text: ' reach total posts', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'75%', y:'79%', w:1, h:1, fill:'0088CC', line:'006699', lineSize:2 , fontSize:10, color:'FFFFFF'});
                                                                                    

                                                                                    //quatrième page FOCUS FANS
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('FOCUS FANS',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                                                    slide.addImage({ data:img5.src, x:1, y:1, w:8, h:3 });

                                                                                    //cinquième page FOCUS LIKE
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('FOCUS LIKE',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                                                    slide.addImage({ data:img.src, x:0.2, y:1, w:6.1, h:2.5 });
                                                                                    slide.addImage({ path:img7.src, x:"67%", y:"18%", w:"28%", h:"42%" });

                                                                                    //sixième page TOP POST
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('TOP POST',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                                                    slide.addText([
                                                                                            { text: 'TOP REACH', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'55%', y:'17%', w:2.2, h:0.4, fill:'0088CC', line:'006699', lineSize:2 , fontSize:13, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: 'TOP INTERACTION*', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'55%', y:'25%', w:2.2, h:0.4, fill:'0088CC', line:'006699', lineSize:2 , fontSize:13, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDate, options: {}}
                                                                                        ],  { x:'55%', y:'35%', w:'100%', color:'000000', fontSize:15 });
                                                                                    slide.addText('Thème : ',  { x:'55%', y:'40%', w:'100%', color:'000000', fontSize:15 });
                                                                                    slide.addText('Format : ',  { x:'55%', y:'45%', w:'100%', color:'000000', fontSize:15 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numPost].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numPost].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'55%', y:'50%', w:'100%', color:'000000', fontSize:15 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numPost].comments.summary.total_count == "" ? '0' : listpost.posts.data[numPost].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'55%', y:'55%', w:'100%', color:'000000', fontSize:15 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPost == "" ? '0' : sharesPost), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'55%', y:'60%', w:'100%', color:'000000', fontSize:15 });
                                                                                    slide.addText([
                                                                                            { text: (clicsDuPost == "" ? '0' : clicsDuPost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'55%', y:'65%', w:'100%', color:'000000', fontSize:15 });
                                                                                    slide.addText([
                                                                                            { text: (meilleurReach == "" ? '0' : meilleurReach.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'55%', y:'70%', w:'100%', color:'0088CC', fontSize:15 });
                                                                                    slide.addText([
                                                                                            { text: tauxReaction, options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'55%', y:'75%', w:'100%', color:'000000', fontSize:15 });

                                                                                    slide.addImage({ data:dataUrl1, x:"10%", y:"18%", w:"40%", h:"65%" });

                                                                                    //septième page TOP 3 (taux d'interaction)
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('TOP 3',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                                                    slide.addText([
                                                                                            { text: 'Top intéraction', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'25%', y:'4%', w:2.5, h:0.3, fill:'0088CC', line:'006699', lineSize:2 , fontSize:15, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateTaux1, options: {}}
                                                                                        ],  { x:'5%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'5%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'5%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numTaux1].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numTaux1].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numTaux1].comments.summary.total_count == "" ? '0' : listpost.posts.data[numTaux1].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostTaux1 == "" ? '0' : sharesPostTaux1), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicTaux1 == "" ? '0' : clicTaux1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (reachTaux1 == "" ? '0' : reachTaux1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'88%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: taux1.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'92%', w:'100%', color:'0088CC', fontSize:10 });

                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateTaux2, options: {}}
                                                                                        ],  { x:'40%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'40%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'40%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numTaux2].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numTaux2].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numTaux2].comments.summary.total_count == "" ? '0' : listpost.posts.data[numTaux2].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostTaux2 == "" ? '0' : sharesPostTaux2), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicTaux2 == "" ? '0' : clicTaux2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (reachTaux2 == "" ? '0' : reachTaux2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'88%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: taux2.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'92%', w:'100%', color:'0088CC', fontSize:10 });

                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateTaux3, options: {}}
                                                                                        ],  { x:'75%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'75%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'75%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numTaux3].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numTaux3].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numTaux3].comments.summary.total_count == "" ? '0' : listpost.posts.data[numTaux3].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostTaux3 == "" ? '0' : sharesPostTaux3), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicTaux3 == "" ? '0' : clicTaux3.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (reachTaux3 == "" ? '0' : reachTaux3.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'88%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: taux3.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'92%', w:'100%', color:'0088CC', fontSize:10 });

                                                                                    slide.addImage({ data:dataUrl12, x:"5%", y:"18%", w:"22%", h:"39%" });
                                                                                    slide.addImage({ data:dataUrl3, x:"40%", y:"18%", w:"22%", h:"39%" });
                                                                                    slide.addImage({ data:dataUrl4, x:"75%", y:"18%", w:"22%", h:"39%" });
                                                                                    
                                                                                    
                                                                                    //huitième page TOP 3 (personnes atteintes)
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('TOP 3',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                                                    slide.addText([
                                                                                            { text: 'Top reach', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'25%', y:'4%', w:2.5, h:0.3, fill:'0088CC', line:'006699', lineSize:2 , fontSize:15, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateReach1, options: {}}
                                                                                        ],  { x:'5%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'5%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'5%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numReach1].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numReach1].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numReach1].comments.summary.total_count == "" ? '0' : listpost.posts.data[numReach1].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostReach1 == "" ? '0' : sharesPostReach1), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicReach1 == "" ? '0' : clicReach1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (reach1 == "" ? '0' : reach1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'88%', w:'100%', color:'0088CC', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: tauxReach1.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'92%', w:'100%', color:'000000', fontSize:10 });

                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateReach2, options: {}}
                                                                                        ],  { x:'40%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'40%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'40%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numReach2].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numReach2].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numReach2].comments.summary.total_count == "" ? '0' : listpost.posts.data[numReach2].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostReach2 == "" ? '0' : sharesPostReach2), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicReach2 == "" ? '0' : clicReach2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (reach2 == "" ? '0' : reach2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'88%', w:'100%', color:'0088CC', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: tauxReach2.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'92%', w:'100%', color:'000000', fontSize:10 });

                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateReach3, options: {}}
                                                                                        ],  { x:'75%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'75%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'75%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numReach3].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numReach3].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numReach3].comments.summary.total_count == "" ? '0' : listpost.posts.data[numReach3].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostReach3 == "" ? '0' : sharesPostReach3), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicReach3 == "" ? '0' : clicReach3.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (reach3 == "" ? '0' : reach3.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'88%', w:'100%', color:'0088CC', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: tauxReach3.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'92%', w:'100%', color:'000000', fontSize:10 });

                                                                                    slide.addImage({ data:dataUrl5, x:"5%", y:"18%", w:"22%", h:"39%" });
                                                                                    slide.addImage({ data:dataUrl6, x:"40%", y:"18%", w:"22%", h:"39%" });
                                                                                    slide.addImage({ data:dataUrl7, x:"75%", y:"18%", w:"22%", h:"39%" });


                                                                                    //neuvième page FLOP POST
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('FLOP POST',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });
                                                                                    slide.addText([
                                                                                            { text: 'Flop reach', options: {}}
                                                                                        ], { shape:pptx.shapes.RECTANGLE, align:'center', x:'35%', y:'4%', w:2.5, h:0.3, fill:'0088CC', line:'006699', lineSize:2 , fontSize:15, color:'FFFFFF'});
                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateDernierReach1, options: {}}
                                                                                        ],  { x:'5%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'5%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'5%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numDernierReach1].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numDernierReach1].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numDernierReach1].comments.summary.total_count == "" ? '0' : listpost.posts.data[numDernierReach1].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostDernierReach1 == "" ? '0' : sharesPostDernierReach1), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicDernierReach1 == "" ? '0' : clicDernierReach1), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (dernierReach1 == "" ? '0' : dernierReach1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'88%', w:'100%', color:'0088CC', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: tauxDernierReach1.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'5%', y:'92%', w:'100%', color:'000000', fontSize:10 });

                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateDernierReach2, options: {}}
                                                                                        ],  { x:'40%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'40%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'40%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numDernierReach2].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numDernierReach2].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numDernierReach2].comments.summary.total_count == "" ? '0' : listpost.posts.data[numDernierReach2].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostDernierReach2 == "" ? '0' : sharesPostDernierReach2), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicDernierReach2 == "" ? '0' : clicDernierReach2), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (dernierReach2 == "" ? '0' : dernierReach2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'88%', w:'100%', color:'0088CC', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: tauxDernierReach2.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'40%', y:'92%', w:'100%', color:'000000', fontSize:10 });

                                                                                    slide.addText([
                                                                                            { text: 'Date du post : ', options: {bold:true}},
                                                                                            { text: maDateDernierReach3, options: {}}
                                                                                        ],  { x:'75%', y:'60%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Thème : ',  { x:'75%', y:'64%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText('Format : ',  { x:'75%', y:'68%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numDernierReach3].reactions.summary.total_count == "" ? '0' : listpost.posts.data[numDernierReach3].reactions.summary.total_count), options: {}},
                                                                                            { text: ' Réactions', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'72%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (listpost.posts.data[numDernierReach3].comments.summary.total_count == "" ? '0' : listpost.posts.data[numDernierReach3].comments.summary.total_count), options: {}},
                                                                                            { text: ' Commentaires', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'76%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (sharesPostDernierReach3 == "" ? '0' : sharesPostDernierReach3), options: {}},
                                                                                            { text: ' Partages', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'80%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (clicDernierReach3 == "" ? '0' : clicDernierReach3), options: {}},
                                                                                            { text: ' Clics', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'84%', w:'100%', color:'000000', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: (dernierReach3 == "" ? '0' : dernierReach3.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")), options: {}},
                                                                                            { text: ' Personnes atteintes', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'88%', w:'100%', color:'0088CC', fontSize:10 });
                                                                                    slide.addText([
                                                                                            { text: tauxDernierReach3.toFixed(2), options: {}},
                                                                                            { text: '% Taux d\'interaction', options: {bold:true}}
                                                                                        ],  { x:'75%', y:'92%', w:'100%', color:'000000', fontSize:10 });

                                                                                    slide.addImage({ data:dataUrl8, x:"5%", y:"18%", w:"22%", h:"39%" });
                                                                                    slide.addImage({ data:dataUrl9, x:"40%", y:"18%", w:"22%", h:"39%" });
                                                                                    slide.addImage({ data:dataUrl10, x:"75%", y:"18%", w:"22%", h:"39%" });

                                                                                    //dixième page CONCLUSION
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('CONCLUSION',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });

                                                                                    //onxième page CONCLUSION ET RECOMMANDATIONS
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img3.src, x:0, y:0, w:10, h:0.8 });
                                                                                    slide.addText('CONCLUSION ET RECOMMANDATIONS',  { x:'9%', y:'7%', w:'100%', color:'FFFFFF', fontFace:'Avenir 85 Heavy', fontSize:25 });

                                                                                    //douzième page FIN
                                                                                    slide = pptx.addSlide();
                                                                                    slide.addImage({ path:img4.src, x:0, y:0, w:'100%', h:'100%' });
                                                                                    slide.addText('Merci',  { x:'35%', y:'40%', w:3, color:'FFFFFF', align: 'center', fontFace:'Avenir 85 Heavy', fontSize:35 });
                                                                                    
                                                                                    $("#progress_selectionne").val("100");

                                                                                    //on enregistre le powerpoint
                                                                                    pptx.writeFile('bilan-reporting');
                                                                                }).catch(function (error) {
                                                                                    console.error('oops, something went wrong!', error);
                                                                                });
                                                                                //fin du 2eme domtoimage
                                                                                
                                                                            }).catch(function (error) {
                                                                                console.error('oops, something went wrong!', error);
                                                                            });
                                                                            //fin du 1er domtoimage
                                                                        });
                                                                        //fin de html2canvas
                                                                        
                                                                    //debut convertion image
                                                                    });
                                                                });
                                                            });
                                                        });
                                                    });
                                                });
                                            });
                                            
                                        });
                                    });
                                }); */

                            });
                            
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
        include 'view/admin/footer.php';
    ?>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pret à partir ?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Voulez vous vous deconnecter ?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <a class="btn btn-primary" href="index.php?a=d">Se deconnecter</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>