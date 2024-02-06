<?php


include __DIR__ . '/vendor/autoload.php';

use Rubix\ML\Loggers\Screen;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Pipeline;
use Rubix\ML\Transformers\TextNormalizer;
use Rubix\ML\Transformers\WordCountVectorizer;
use Rubix\ML\Tokenizers\NGram;
use Rubix\ML\Transformers\TfIdfTransformer;
use Rubix\ML\Transformers\ZScaleStandardizer;
use Rubix\ML\Classifiers\MultilayerPerceptron;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\NeuralNet\Layers\Activation;
use Rubix\ML\NeuralNet\Layers\PReLU;
use Rubix\ML\NeuralNet\Layers\BatchNorm;
use Rubix\ML\NeuralNet\ActivationFunctions\LeakyReLU;
use Rubix\ML\NeuralNet\Optimizers\AdaMax;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Extractors\CSV;

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

    // Definindo a arquitetura da rede neural e instanciando Multilayer Perceptron

    $estimator = new PersistentModel(
        new Pipeline([
            new TextNormalizer(),
            new WordCountVectorizer(PHP_INT_MAX, 1, 0.8, new NGram(1, 2)),
            new TfIdfTransformer(),
            new ZScaleStandardizer(),
        ], new MultilayerPerceptron([
            new Dense(100),
            new Activation(new LeakyReLU()),
            new Dense(100),
            new Activation(new LeakyReLU()),
            new Dense(100, 0.0, false),
            new BatchNorm(),
            new Activation(new LeakyReLU()),
            new Dense(50),
            new PReLU(),
            new Dense(50),
            new PReLU(),
        ], 256, new AdaMax(0.0001))),
        new Filesystem('tipo_documento.rbx', true)
    );

    $estimator->setLogger($logger);

    // --- Treinando

    $estimator->train($dataset);

    // Registra a pontuação de validação e perda de cada iteração e salva em arquivo

    $extractor = new CSV('progresso.csv', true);

    $extractor->export($estimator->steps());

    $logger->info('<br>' . 'Progresso salvo em progresso.csv' . '<br>');

    // --- Salvando o modelo treinado

    // O modelo é salvo para que possa ser carregado posteriormente
    // nas etapas de validação e previsão

    if (strtolower(trim(readline('Salvar este modelo? (s|[n]): '))) === 's') {
        $estimator->save();
    }

} catch (Throwable $e) {
    var_dump($e->getMessage());
}