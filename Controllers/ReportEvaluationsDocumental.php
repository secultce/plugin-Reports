<?php
namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';
use MapasCulturais\App,
    MapasCulturais\i,
    MapasCulturais\Controller;
use ReportLib;

class ReportEvaluationsDocumental extends Controller
{
    public function reportGeneration()
    {
        $report = new ReportLib();
        $file = __DIR__ . '/../temp-files/resultado-preliminar.pdf';
        $inputJRXML = __DIR__ . '/../jasper/jrxml/resultado-preliminar.jrxml';
        $outputReportFile = __DIR__ . '/../temp-files';
        $inputJasper = __DIR__ . '/../jasper/build/resultado-preliminar.jasper';
        $dataFile = __DIR__ . '/../jasper/data-adapter-json/data.json';
        $format = 'pdf';
        $driver = 'json';
        $data_divulgacao = '20-01-19';
        $query = null;
        if (file_exists($inputJasper)) {
            //echo 'aqui 1 '; die();
            $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao);
            $report->downloadFiles($file);
        } else {
            //echo 'aqui'; die();
            $report->buildReport($inputJRXML);
            $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao);
            $report->downloadFiles($file);
        }
    }
    public function documentqualificationsummary(){
        echo 'deu bom';
        // $app = App::i();
        // $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
        // $date = isset($this->data['publishDate']) ? $this->data['publishDate'] : date("d/m/Y");
        // $datePubish = date("d/m/Y", strtotime($date));

        // $opportunityId = (int) $this->data['id'];
        // $opportunity = $app->repo("Opportunity")->find($opportunityId);

        // $dql = "SELECT e,r,a
        //         FROM
        //             MapasCulturais\Entities\RegistrationEvaluation e
        //             JOIN e.registration r
        //             JOIN r.owner a
        //         WHERE r.opportunity = :opportunity ORDER BY r.consolidatedResult ASC";

        // $q = $app->em->createQuery($dql);
        // $q->setParameters(['opportunity' => $opportunity]);
        // $evaluations = $q->getResult();

        // $json_array = [];
        // foreach ($evaluations as $e) {
        //     $registration = $e->registration;
        //     $evaluationData = (array) $e->evaluationData;
        //     $result = $e->getResultString();
        //     $metadata = (array) $registration->getMetadata();
        //     $projectName = (isset($metadata['projectName'])) ? $metadata['projectName'] : '';
        //     $descumprimentoDosItens = (string) array_reduce($evaluationData, function ($motivos, $item) {
        //         if ($item['evaluation'] == 'invalid') {
        //             $motivos .= trim($item['obs_items']);
        //         }
        //         return $motivos;
        //     });
        //     $categoria = $registration->category;
        //     $agentRelations = $app->repo('RegistrationAgentRelation')->findBy(['owner'=>$registration]);
            
        //     $coletivo = null;
            
        //     if($agentRelations) {
        //         $coletivo = $agentRelations[0]->agent->nomeCompleto;
        //     }

        //     $proponente = $registration->owner->nomeCompleto;
        //     if (strpos($categoria,'JURÍDICA') && $coletivo !== null) {
        //         $proponente = $coletivo;
        //     } 
            
        //     $json_array[] = [
        //         'n_inscricao' => $registration->number,
        //         'projeto' => $projectName,
        //         'proponente' => trim($proponente),
        //         'categoria' => $categoria,
        //         'municipio' => trim($registration->owner->En_Municipio),
        //         'resultado' => ($result == 'Válida') ? 'HABILITADO' : 'INABILITADO',
        //         'motivo_inabilitacao' => $descumprimentoDosItens,
        //     ];
        // }
        // $filename = __DIR__ . "/report/" . time() . "habilitacao-preliminar.csv";
        // $output = fopen($filename, 'w') or die("error");
        // fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        // fputcsv($output, ["Inscrição", "Projeto", "Proponente", "Categoria", "Município", "Resultado", "Motivo_Inabilitação"], ";");
        // foreach ($json_array as $relatorio) {
        //     fputcsv($output, $relatorio, ";");
        // }
        // fclose($output) or die("Can't close php://output");
        // header('Content-Encoding: UTF-8');
        // header("Content-type: text/csv; charset=UTF-8");
        // header("Content-Disposition: attachment; filename=habilitacao-documental.csv");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // readfile($filename);
        // unlink($filename);
    }
}
