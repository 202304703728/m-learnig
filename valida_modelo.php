<?php

include __DIR__ . '/vendor/autoload.php';

use Rubix\ML\Loggers\Screen;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\CrossValidation\Reports\AggregateReport;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;
use Rubix\ML\CrossValidation\Reports\MulticlassBreakdown;

try {

    // Suspende a limitação de MEMORY_LIMIT (php.ini)

    ini_set('memory_limit', '-1');

    // Aumentando o limite de tempo para 5 minutos

    ini_set('max_execution_time', '300');

    $logger = new Screen();

    $logger->info('Carregando o conjunto de dados para a memoria...' . '<br>');

    $samples = $labels = [];

    // Carregando conjunto de dados

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

    // Carrega as amostras e rótulos em um conjunto de dados ($dataset)

    $dataset = Labeled::build($samples, $labels)
        ->randomize()
        ->take(10000);

    // Carrega o histórico de salvamentos

    $estimator = PersistentModel::load(new Filesystem('tipo_documento.rbx'));

    $logger->info('Fazendo as predicoes...' . '<br>');

    // Predict() pega um conjunto de dados de entrada e retorna uma matriz de previsões

    $predictions = $estimator->predict($dataset);

    // Gera um relatório com o resultado do treinamento

    $report = new AggregateReport([
        new MulticlassBreakdown(),
        new ConfusionMatrix(),
    ]);

    $results = $report->generate($predictions, $dataset->labels());

    echo $results;

    $results->toJSON()->saveTo(new Filesystem('relatorio.json'));

    $logger->info('<br>' . 'Relatorio salvo em relatorio.json');

} catch (Throwable $e) {
    var_dump($e->getMessage());
}