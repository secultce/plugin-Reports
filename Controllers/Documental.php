<?php

namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';
require_once PLUGINS_PATH . '/Report/Controllers/Report.php';

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use ReportLib;
use Report\Controllers\Report;

class Documental extends Report
{
    public function ALL_report()
    {
        $app = App::i();
        $opportunityId = (int) $this->data['id'];
        $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
        $date = isset($this->data['publishDate']) ? $this->data['publishDate'] : date("d/m/Y");
        $datePublish = date("d/m/Y", strtotime($date));
        $opportunity =  $app->repo("Opportunity")->find($opportunityId);
        $report = new ReportLib();
        $filePDF = __DIR__ . '/../temp-files/resultado-preliminar.pdf';
        $fileXLS = __DIR__ . '/../temp-files/resultado-preliminar.xls';
        $inputJRXML = __DIR__ . '/../jasper/jrxml/resultado-preliminar.jrxml';
        $inputJASPER = __DIR__ . '/../jasper/jrxml/resultado-preliminar.jasper';
        $outputReportFile = __DIR__ . '/../temp-files';
        $inputReportFile = __DIR__ . '/../jasper/build/resultado-preliminar.jasper';
        //$dataFile = __DIR__ . '/../jasper/data-adapter-json/data.json';
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
            $projectName = (isset($metadata['projectName'])) ? $metadata['projectName'] : '';
            $descumprimentoDosItens = (string) array_reduce($evaluationData, function ($motivos, $item) {
                if ($item['evaluation'] == 'invalid') {
                    $motivos .= trim($item['obs_items']);
                }
                return $motivos;
            });
            $categoria = $registration->category;
            $agentRelations = $app->repo('RegistrationAgentRelation')->findBy(['owner' => $registration]);
            $coletivo = null;
            if ($agentRelations) {
                $coletivo = $agentRelations[0]->agent->nomeCompleto;
            }
            $proponente = $registration->owner->nomeCompleto;
            if (strpos($categoria, 'JURÍDICA') && $coletivo !== null) {
                $proponente = $coletivo;
            }
            $json_array[] = [
                'n_inscricao' => $registration->number,
                'projeto' => $projectName,
                'proponente' => trim($proponente),
                'categoria' => $categoria,
                'municipio' => trim($registration->owner->En_Municipio),
                'resultado' => ($result == 'Válida') ? 'HABILITADO' : 'INABILITADO',
                'motivo_inabilitacao' => $descumprimentoDosItens,
            ];
        }

        $publish = (array)$app->repo("Opportunity")->findOpportunitiesWithDateByIds($opportunityId);
        $driver = 'json';
        $data_divulgacao = $datePublish;
        $nome_edital = $publish[0]['name'];
        $query = null;
        $params = [
            "data_divulgacao" => $data_divulgacao,
            "nome_edital" => $nome_edital,
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
