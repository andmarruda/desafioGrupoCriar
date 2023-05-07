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
                <h3>Envie o log da corrida de kart para o levantamento dos dados.</h3>
                <form method="post" enctype="multipart/form-data" action="resultado.php">
                    <div class="mb-3">
                        <label for="arquivolog" class="form-label">Arquivo do log<br><small>Extensão .log, .txt</small></label>
                        <input type="file" class="form-control" id="arquivolog" name="arquivolog" accept=".txt,.log">
                    </div>
                    <button type="submit" class="btn btn-primary">Processar dados</button>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    </body>
</html>