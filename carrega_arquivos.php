<?php

use Rubix\ML\Loggers\Screen;
use Smalot\PdfParser\Parser;

include __DIR__ . '/vendor/autoload.php';

try {

    // Suspende a limitação de MEMORY_LIMIT (php.ini)

    ini_set('memory_limit', '-1');

    $logger = new Screen();

    $text = "";

    // Exibe as informações dos arquivos que foram importados
    //var_dump($_FILES);

    if (isset($_FILES['files'])) {

        $data = $_FILES['files'];

        foreach ($data['name'] as $file) {
            // Verifica se algum arquivo não tem a extensão PDF
            if (pathinfo($file, PATHINFO_EXTENSION) != 'pdf') {
                throw new Exception('Escolha apenas arquivos com a extensão PDF.');
            }
        }
        $i = 1;
        $label = $_POST['tipoDocumento'];
        $arq = '';

        foreach ($data['tmp_name'] as $key => $file) {

            // Obtém o conteúdo dos arquivos
            $parser = new Parser();
            $pdf = $parser->parseFile($file);
            $text = $pdf->getText();

            // Exibir teor do documento (sem tratamento)
            //echo $text;

            // Exibir o teor do documento (com quebra de página e formatando parágrafos)
            //echo nl2br($text);

            // Guarda o teor do documento em um arquivo txt

            // Como estava usando o development server, fiz essa "gambiarra"
            // para inverter as barras sem gerar erro no código

            switch ($label) {
                case 'inicial':
                    $arq = "treinamento_txt\inicial\documento$i.txt";
                    break;
                case 'procuracao':
                    $arq = "treinamento_txt\procuracao\documento$i.txt";
                    break;
            }

            //echo $arq;
            $logger->info('Salvando o conteudo do arquivo ' . $i . '<br>');
            file_put_contents($arq, $text);
            $i += 1;

        }

        /* echo('<br><hr><br>');
        echo('<legend>Treinamento (NOVO) </legend><br>');
        echo('<form action="treina_classificador.php" METHOD="post">');
        echo('<button type="submit">Treinar</button>');
        echo('</form>');
        echo('<br><hr>'); */

        //include 'pre_processamento.php';
    }

} catch (Throwable $e) {
    var_dump($e->getMessage());
}