<?php

include __DIR__ . '/vendor/autoload.php';

use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Datasets\Unlabeled;
use Smalot\PdfParser\Parser;

try {

    // Suspende a limita��o de MEMORY_LIMIT (php.ini)

    ini_set('memory_limit', '-1');

    // Usa PersistentModel para carregar a rede que foi treinada anteriormente

    $estimator = PersistentModel::load(new Filesystem('tipo_documento.rbx'));

    //while (empty($text)) $text = readline("Digite um texto para analisar:\n");

    if (isset($_FILES['files'])) {

        $data = $_FILES['files'];

        foreach ($data['tmp_name'] as $key => $file) {
            // Obt�m o conte�do do arquivo
            $parser = new Parser();
            $pdf = $parser->parseFile($file);
            $text = $pdf->getText();
        }

    }

    // Unlabeled usa um conjunto de dados n�o rotulados para treinar o modelo,
    // alimentar amostras desconhecidas fazer previs�es

    $dataset = new Unlabeled([
        [$text],
    ]);

    // Retorna a matriz de previs�es

    $prediction = strtoupper(current($estimator->predict($dataset)));

    // Mostra o resultado da previs�o

    echo "O documento \u{00E9} um(a): $prediction";

} catch (Throwable $e) {
    var_dump($e->getMessage());
}