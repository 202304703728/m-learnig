<!DOCTYPE html>
<html lang="en">
<head><title>Machine Learning</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, inicial-scale=1.0">
</head>
<body>
<form action="carrega_arquivos.php" enctype="multipart/form-data" METHOD="post">
    <fieldset>
        <h2>Selecao do tipo de documento</h2>
        <div>
            <input name="tipoDocumento" type="radio" id="rdlInicial" name="rdlInicial" value="inicial"/>
            <label for="rdlInicial">Peticao Inicial</label>
        </div>
        <div>
            <input name="tipoDocumento" type="radio" id="rdlProcuracao" name="rdlProcuracao" value="procuracao"/>
            <label for="rdlProcuracao">Procuracao</label>
        </div>
    </fieldset>
    <br>
    <input type="file" name="files[]" multiple>
    <button type="submit">Carregar</button>
</form>
<br><hr>
<form action="treina_classificador.php" enctype="multipart/form-data" METHOD="post">
    <h2>Treinamento (NOVO - Multilayer Perceptron)</h2>
    <br>
    <button type="submit">Treinar</button>
</form>
<br><hr>
<form action="valida_modelo.php" METHOD="post">
    <h2>Validacao do modelo</h2>
    <br>
    <button type="submit">Validar</button>
</form>
<br><hr>
<form action="prever_documento.php" enctype="multipart/form-data" METHOD="post">
    <h2>Predicao do tipo de documento</h2>
    <br>
    <input type="file" name="files[]">
    <button type="submit">Prever</button>
</form>
<!--br><hr>
<form action="treina_classificador_random.php" enctype="multipart/form-data" METHOD="post">
    <h2>Validacao e Treinamento (ANTIGO - Random Forest)</h2>
    <br>
    <button type="submit">Validar e Treinar</button>
</form-->
</body>
</html>