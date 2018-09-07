<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Strasbourg</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script async src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="margin: 30px;">
            <div class="col-md-12" style="text-align: center;">
                <h1>Etat en direct des parkings de Strasbourg</h1>
            </div>
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
            <div class="col-md-4">
                <div class="card <?= "border-" . $css[intval(substr($eta->ds, -1)-1)] ?> mb-3">
                    <h3 class="card-header text-white <?= "bg-" . $css[intval(substr($eta->ds, -1)-1)] ?>"><?= strtoupper($status[intval(substr($eta->ds, -1)-1)]) ?></h3>
                    <div class="card-body">
                        <h4 class="card-title"><?= $parking->ln; ?></h4>
                        <div class="progress bg-danger" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($eta->df/$eta->dt)*100; ?>%"><?= $eta->df . ' / ' . $eta->dt ?></div>
                        </div>
                    </div>
                    <div id="map" class="col-md-12"></div>
                    <ul class="list-group list-group-flush">
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

</body>
</html>