<?php
require __DIR__ . './../vendor/autoload.php';

use PHPJasper\PHPJasper;

class ReportLib
{
    //BUILDA O ARQUIVO JRXML PARA .JASPER
    public function buildReport($inputJRXML)
    {

        //$input = __DIR__."/../jasper/resultado-preliminar.jrxml";
        //echo $input; die();
        $jasper = new PHPJasper();
        $jasper->compile($inputJRXML)->execute();
    }
    //EXECUTA O ARQUIVO .JASPER
    public function executeReport($inputReport, $outputReport, $dataFile, $format, $driver, $query, $params = null)
    {
        $options = [
            'format' => [$format],
            'params' => $params,
            'locale' => 'en',
            'db_connection' => [
                'driver' => $driver,
                'data_file' => $dataFile,
                //'json_query' => $query || null,
            ],
        ];
        $jasper = new PHPJasper();
        return $jasper->process(
            $inputReport,
            $outputReport,
            $options
        )->execute();
    }


    //GERA O DOWNLOAD DOS RELATÓRIOS
    public function downloadFiles($arquivo, $dataFile)
    {
        if (isset($arquivo) && file_exists($arquivo)) {
            // faz o teste se a variavel não esta vazia e se o arquivo realmente existe
            switch (strtolower(substr(strrchr(basename($arquivo), "."), 1))) {
                    // verifica a extensão do arquivo para pegar o tipo
                case "pdf":
                    $tipo = "application/pdf";
                    break;
                case "doc":
                    $tipo = "application/msword";
                    break;
                case "xls":
                    $tipo = "application/vnd.ms-excel";
                    break;
                case "odt":
                    $tipo = "application/vnd.oasis.opendocument.text";
                    break;
                case "php": // deixar vazio por seurança
                case "htm": // deixar vazio por seurança
                case "html": // deixar vazio por seurança
            }
            header("Content-Type: " . $tipo);
            // informa o tipo do arquivo ao navegador
            header("Content-Length: " . filesize($arquivo));
            // informa o tamanho do arquivo ao navegador
            header("Content-Disposition: attachment; filename=" . basename($arquivo));
            // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
            readfile($arquivo); // lê o arquivo
            //APAGA O RELATÓRIO TEMPORÁRIO, JSON TEMPORÁRIO,  e .jasper
            unlink($arquivo);
            unlink($dataFile);
            exit; // aborta pós-ações
        }
    }
}
