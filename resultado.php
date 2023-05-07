<?php
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require_once __DIR__.'/src/autoload.php';
        $up = new upload();
        $dir = __DIR__.'/storage/';

        if(!$up->verify($_FILES['arquivolog']) || !$up->store($dir)){
            echo $up->getLastError();
            die;
        }

        $file = $dir.$up->getFilename();
        $race = new \challenge\race($file);
        $analytics = $race->getAnalytics();
        $bestLap = $analytics->getBestRaceLap();
        $raceEnd = $analytics->getRaceFirstEnd();
        $pilotData = $analytics->getOrderedData();
    } else{
        header("Location: desafio.php");
    }
?>
<!Doctype html>
<html>
    <head>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Anderson Arruda">
        <title>Corrida de Kart</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <h3>Dados da corrida de Kart</h3>
                <p><b>Tempo total da prova:</b> <?= $raceEnd; ?></p>
                <p>
                    <b>Melhor volta da corrida:</b> 
                    A melhor volta foi dada pelo piloto <?= $bestLap[1]. ' - '. $bestLap[2]; ?>
                    tempo total da volta: <?= $bestLap[4]->format('i:s.v'); ?>
                </p>

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Posição</th>
                            <th>Código Piloto</th>
                            <th>Nome Piloto</th>
                            <th>Qtde de voltas completadas</th>
                            <th>Tempo total de prova</th>
                            <th>Melhor volta</th>
                            <th>Velocidade média</th>
                            <th>Distância primeiro colocado em tempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $firstPlace = NULL;
                            $x=1;
                            foreach($pilotData as $pilot)
                            {
                                echo '<tr>';
                                if($x == 1){
                                    echo '<td style="background: #FFD700;">&nbsp;</td>';
                                } else if($x == 2){
                                    echo '<td style="background: #C0C0C0;">&nbsp;</td>';
                                } else if($x == 3){
                                    echo '<td style="background: #CD7F32;">&nbsp;</td>';
                                } else{
                                    echo '<td>&nbsp;</td>';
                                }

                                $bestPilotLap = $analytics->bestPilotLap($pilot['codigo']);
                                $media = $analytics->avgPilotSpeed($pilot['codigo']);

                                $distancia = '';
                                $tempoProva = $analytics->getRacePilotEnd($pilot['codigo']);
                                if(is_null($firstPlace))
                                {
                                    $firstPlace = end($pilot['hora']);
                                } else if($pilot['total_volta'] == 4)
                                {
                                    $distancia = $analytics->getDiff($firstPlace, end($pilot['hora']));
                                } else{
                                    $distancia = 'Corrida não completada!';
                                }

                                echo '
                                    <td>'. $x. '</td>
                                    <td>'. $pilot['codigo']. '</td>
                                    <td>'. $pilot['nome']. '</td>
                                    <td>'. $pilot['total_volta']. '</td>
                                    <td>'. $tempoProva. '</td>
                                    <td>Volta num: '. $bestPilotLap['lap']. ' tempo: '. $bestPilotLap['time']. '</td>
                                    <td>'. $media. '</td>
                                    <td>'. $distancia. '</td>
                                ';
                                echo '</tr>';

                                $x++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>