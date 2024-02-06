<?php

declare(strict_types=1);

include __DIR__ . '/vendor/autoload.php';

use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;
use Rubix\ML\Loggers\Screen;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Classifiers\RandomForest;

try {

    // Suspende a limitação de MEMORY_LIMIT (php.ini)

    ini_set('memory_limit', '-1');

    $logger = new Screen();

    $logger->info('Carregando o conjunto de dados para a memoria...' . '<br>');

    $samples = $labels = [];

    // Carregando o conjunto de dados

    // Obs: poderá ser necessário inverter a posição das barras do caminho do diretório
    // ou mudar para o loop comentado abaixo

    /*foreach (['inicial', 'procuracao'] as $label) {
        foreach (glob('treinamento_txt\{$label}\*.txt') as $file) {
            $samples[] = [file_get_contents($file)];
            $labels[] = $label;
        }
    }*/

    // Como estava usando o development server, fiz essa "gambiarra"
    // para inverter as barras sem gerar erro no código

    foreach (glob("treinamento_txt\inicial\*.txt") as $file) {
        $samples[] = [file_get_contents($file)];
        $labels[] = 'inicial';
    }

    foreach (glob("treinamento_txt\procuracao\*.txt") as $file) {
        $samples[] = [file_get_contents($file)];
        $labels[] = 'procuracao';
    }

    $dataset = new Labeled($samples, $labels);
    //var_dump($dataset);

    // Separa uma parte dos dados para treinamento e outra para teste
    // 80% para treinamento, 20% para teste (de forma proporcional)

    [$training, $testing] = $dataset->stratifiedSplit(0.8);

    // Método de aprendizado escolhido: Random Forest

    //$method = new \Rubix\ML\Classifiers\ClassificationTree();
    $method = new RandomForest();

    // Treinamento

    $method->train($training);

    // Fazendo as predições com os dados de teste

    $predictions = $method->predict($testing);

    // Medindo os resultados gerando a matriz de confusão,
    // com falsos positivos e negativos

    $confusionMatrix = new ConfusionMatrix();

    // Gerando as predições comparando as predições comparando com o conjunto de testes
    // $predictions = [s,n,s,n], $testing->labels() = [s,s,n,n]

    $confusionMatrix->generate($predictions, $testing->labels());

    echo('<br>');
    echo($confusionMatrix->generate($predictions, $testing->labels()));

    // Medindo o resultado (acurácia)

    $accuracy = new Accuracy();
    $accuracy->score($predictions, $testing->labels());

    echo('<br><br>');
    echo('Acuracia:' . $accuracy->score($predictions, $testing->labels()));

} catch (Throwable $e) {
    var_dump($e->getMessage());
}