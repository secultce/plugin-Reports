<?php

namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';
require_once PLUGINS_PATH . '/Report/Controllers/Report.php';

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use ReportLib;
use Report\Controllers\Report;

class AuxilioEventos extends Report
{
    public function ALL_report()
    {
        $app = App::i();
        $report = new ReportLib();
        $opportunityId = (int) $this->data['id'];
        $opportunity =  $app->repo("Opportunity")->find($opportunityId);
        $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
        $data_divulgacao = date("d/m/Y - H:i:s", time());
        $filePDF = __DIR__ . '/../temp-files/EDITAL-AUXILIO-FINANCEIRO-AO-SETOR-DE-EVENTOS.pdf';
        $fileXLS = __DIR__ . '/../temp-files/EDITAL-AUXILIO-FINANCEIRO-AO-SETOR-DE-EVENTOS.xls';
        $inputJRXML = __DIR__ . '/../jasper/jrxml/EDITAL-AUXILIO-FINANCEIRO-AO-SETOR-DE-EVENTOS.jrxml';
        $inputJASPER = __DIR__ . '/../jasper/jrxml/EDITAL-AUXILIO-FINANCEIRO-AO-SETOR-DE-EVENTOS.jasper';
        $outputReportFile = __DIR__ . '/../temp-files';
        $inputReportFile = __DIR__ . '/../jasper/build/EDITAL-AUXILIO-FINANCEIRO-AO-SETOR-DE-EVENTOS.jasper';
        $driver = 'json';
        $query = null;
        $params = [
            "data_divulgacao" => $data_divulgacao
        ];

        $dql = "SELECT e,r,a
                    FROM
                        MapasCulturais\Entities\RegistrationEvaluation e
                        JOIN e.registration r
                        JOIN r.owner a
                    WHERE r.opportunity = :opportunity ORDER BY r.consolidatedResult ASC";

        $q = $app->em->createQuery($dql);
        $q->setParameters(['opportunity' => $opportunity]);
        $evaluations = $q->getResult();
        $json_array = [];
        foreach ($evaluations as $e) {
            $registration = $e->registration;
            $evaluationData = (array) $e->evaluationData;
            $result = $e->getResultString();
            $metadata = (array) $registration->getMetadata();
        }
        /*
           Num inscricao = 
           
           Nome = 
           
           Função = 

           Municipio = 

           Situação = 

           Motivo = 
        
        */


        var_dump($json_array);
        die();

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
