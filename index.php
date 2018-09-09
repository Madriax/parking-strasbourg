<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Strasbourg</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script async src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row text-white" style="margin: 30px;">
            <div class="col-md-12" style="text-align: center;">
                <h1>Etat en direct des parkings de Strasbourg</h1>
                <div class="form-group" style="margin-top: 50px;">
                    <label for="recherche" style="float: left;">Recherche: </label>
                    <input type="text" class="form-control" id="recherche" placeholder="Nom du parking">
                </div>
            </div>
        </div>
        <div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4 class="alert-heading">Information trafic</h4>
            <ul class="mb-0" id="info-trafic"></ul>
        </div>
        <div class="row">
        <?php
            $status = ['ouvert', 'complet', 'indisponible', 'fermé'];
            $css = ['success', 'warning', 'info', 'danger'];
            $parkings = json_decode(file_get_contents("http://carto.strasmap.eu/remote.amf.json/Parking.geometry"));
            $etas = json_decode(file_get_contents("http://carto.strasmap.eu/remote.amf.json/Parking.status"));
            foreach($parkings->s as $parking) {
                foreach($etas->s as $eta ) {
                    if ($eta->id === $parking->id){
        ?>
            <div class="col-md-4 parking">
                <div class="card <?= "border-" . $css[intval(substr($eta->ds, -1)-1)] ?> mb-3">
                    <h3 class="card-header text-white <?= "bg-" . $css[intval(substr($eta->ds, -1)-1)] ?>"><?= strtoupper($status[intval(substr($eta->ds, -1)-1)]) ?></h3>
                    <div class="card-body">
                        <h4 class="card-title"><?= $parking->ln; ?></h4>
                    </div>
                    <div id="map" class="col-md-12"></div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            Places disponnibles:
                            <div class="progress bg-danger" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($eta->df/$eta->dt)*100; ?>%"><?= $eta->df . ' / ' . $eta->dt ?></div>
                            </div>
                        </li>
                        <li class="list-group-item"><?php if ($parking->price_fr) { echo $parking->price_fr; } else { echo "Aucune donnée de paiement disponnible"; } ?></li>
                    </ul>
                    <div class="card-body">
                        <a href="https://www.google.fr/maps/place/<?= $parking->go->y ?>,<?= $parking->go->x ?>" target="_BLANK" class="card-link">J'y vais !</a>
                    </div>
                </div>
            </div>
                    
                    <?php } } } ?>

        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <span class="text-muted">Par <a target="_BLANK" href="http://madriax.fr">Alexandre Duvois</a> | Donnée issue de <a href="https://www.strasbourg.eu/open-data-donnees" target="_BLANK" title="OpenData ville de Strasbourg">Strasbourg OpenData</a></span>
        </div>
    </footer>

    <script type="text/javascript">

        function getTraficInfo() {
            const ajaxRequest = new XMLHttpRequest();
            ajaxRequest.open("GET", "handler.php");

            ajaxRequest.onload = () => {
                result = JSON.parse(ajaxRequest.responseText);
                for (a in result.s ) {
                    if (result.s[a].ds != "type_manifestation" && result.s[a].ds != "type_travaux") {
                        document.getElementById('info-trafic').innerHTML = '<li>Attention ' + result.s[a].dt + ' : ' + result.s[a].dp + '</li>';
                    }
                }
            }
            ajaxRequest.send();
        }

        getTraficInfo();
        var timer_thread = setInterval(() => {
            getTraficInfo()
        }, 90000);


        document.getElementById('recherche').addEventListener('keyup', function(e) {
            var recherche = this.value.toLowerCase();
            var documents = document.querySelectorAll('.parking');
            
            Array.prototype.forEach.call(documents, function(document) {
                // On a bien trouvé les termes de recherche.
                if (document.innerHTML.toLowerCase().indexOf(recherche) > -1) {
                    document.style.display = 'block';
                } else {
                    document.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>