<?php

namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';
require_once PLUGINS_PATH . '/Report/Controllers/Report.php';

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use ReportLib;
use Report\Controllers\Report;

class PagamentoAuxilioEventos extends Report
{
    public function ALL_report()
    {
        $app = App::i();
        $report = new ReportLib();
        //$opportunityId = (int) $this->data['id'];
        //$opportunity =  $app->repo("Opportunity")->find($opportunityId);
        $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
        $dataHora = date("d/m/Y - H:i:s", time());
        $data_divulgacao = strval($dataHora);
        $filePDF = __DIR__ . '/../temp-files/relatorio-pagamentos-auxilio-eventos.pdf';
        $fileXLS = __DIR__ . '/../temp-files/relatorio-pagamentos-auxilio-eventos.xls';
        $inputJRXML = __DIR__ . '/../jasper/jrxml/relatorio-pagamentos-auxilio-eventos.jrxml';
        $inputJASPER = __DIR__ . '/../jasper/jrxml/relatorio-pagamentos-auxilio-eventos.jasper';
        $outputReportFile = __DIR__ . '/../temp-files';
        $inputReportFile = __DIR__ . '/../jasper/build/relatorio-pagamentos-auxilio-eventos.jasper';

        // $sqlData = "";
        // $stmt = $app->em->getConnection()->prepare($sqlData);
        // $stmt->execute();
        // $data = $stmt->fetchAll();
        // $json_array = [];
        // foreach ($data as $d) {
        //     $funcao = json_decode($d['funcao']);
        //     $funcaoString = $funcao[0];
        //     $json_array[] = [
        //         'n_inscricao' => $d['num_inscricao'],
        //         'proponente' => $d['proponente'],
        //         'funcao' => $funcaoString,
        //         'municipio' => $d['municipio'],
        //         'situacao' => $d['situacao'],
        //         'motivo' => $d['motivo']
        //     ];
        // }
        $json_array[] = [
            "n_inscricao" => "ON-341311137",
            "nome" => "ALDJANE LIMA DE OLIVEIRA",
            "funcao" => "PRODUTOR",
            "municipio" => "FORTALEZA",
            "parcela_1" => "AGUARDANDO RETORNO",
            "motivo_1" => "CONTA CORRENTE INVALIDA",
            "parcela_2" => "PENDENDTE",
            "motivo_2" => "PENDENDTE"
        ];

        $driver = 'json';
        $query = null;
        $params = [
            "data_divulgacao" => $data_divulgacao
        ];
        $jsonFile = json_encode($json_array);
        $dataFile = $this->generationJSONFile($jsonFile);

        if (file_exists($inputReportFile)) {
            if ($format == 'pdf') {
                $report->executeReport($inputReportFile, $outputReportFile, $dataFile, $format, $driver, $query, $params);
                $report->downloadFiles($filePDF, $dataFile);
            } else {
                $report->executeReport($inputReportFile, $outputReportFile, $dataFile, $format, $driver, $query, $params);
                $report->downloadFiles($fileXLS, $dataFile);
            }
        } else {
            if ($format == 'pdf') {
                $report->buildReport($inputJRXML);
                $report->executeReport($inputJASPER, $outputReportFile, $dataFile, $format, $driver, $query, $params);
                $report->downloadFiles($filePDF, $dataFile);
            } else {
                $report->buildReport($inputJRXML);
                $report->executeReport($inputJASPER, $outputReportFile, $dataFile, $format, $driver, $query, $params);
                $report->downloadFiles($fileXLS, $dataFile);
            }
        }
    }
}
