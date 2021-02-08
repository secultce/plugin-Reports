<?php
namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';
use MapasCulturais\App,
    MapasCulturais\i,
    MapasCulturais\Controller;
use ReportLib;

class ReportEvaluationsDocumental extends Controller
{
   
    public function documentqualificationsummary($datePubish, $formatFile, $opportunityId){
        $app = App::i();
        $opportunityId = $opportunityId;
        $opportunity = $app->repo("Opportunity")->find($opportunityId);
        $report = new ReportLib();
        $filePDF = __DIR__ . '/../temp-files/resultado-preliminar.pdf';
        $fileXLS = __DIR__ . '/../temp-files/resultado-preliminar.xls';
        $inputJRXML = __DIR__ . '/../jasper/jrxml/resultado-preliminar.jrxml';
        $outputReportFile = __DIR__ . '/../temp-files';
        $inputJasper = __DIR__ . '/../jasper/build/resultado-preliminar.jasper';
        //$dataFile = __DIR__ . '/../jasper/data-adapter-json/data.json';
        $format = $formatFile;
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
            $agentRelations = $app->repo('RegistrationAgentRelation')->findBy(['owner'=>$registration]);
            
            $coletivo = null;
            
            if($agentRelations) {
                $coletivo = $agentRelations[0]->agent->nomeCompleto;
            }

            $proponente = $registration->owner->nomeCompleto;
            if (strpos($categoria,'JURÍDICA') && $coletivo !== null) {
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
            $jsonFile = json_encode($json_array);
            $stringFile = '{"data":'.$jsonFile.'}';
            $arquivoData = 'data.json';
            $file = fopen(__DIR__ . '/../jasper/data-adapter-json/' . $arquivoData ,'w');
            fwrite($file, $stringFile);
            fclose($file);
            $dataFile = __DIR__.'/../jasper/data-adapter-json/data.json';
        }
        $driver = 'json';
        $data_divulgacao = $datePubish;
        $query = null;
        if (file_exists($inputJasper)) {
            if($formatFile == 'pdf'){
                $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao);
                $report->downloadFiles($filePDF, $dataFile);
            }else{
                $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao);
                $report->downloadFiles($fileXLS, $dataFile);
            }
            
        } else {
            if($formatFile == 'pdf'){
                $report->buildReport($inputJRXML);
                $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao);
                $report->downloadFiles($filePDF, $dataFile);
            }else{
                $report->buildReport($inputJRXML);
                $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao);
                $report->downloadFiles($fileXLS, $dataFile);
            }
        }
    }
}
