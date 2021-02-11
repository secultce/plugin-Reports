<?php
namespace Report;

require_once PLUGINS_PATH . 'Report/business/ReportLib.php';

use MapasCulturais\App;
use MapasCulturais\Entities;
use MapasCulturais\i;
use ReportLib;

class Plugin extends \MapasCulturais\Plugin
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function _init()
    {
        $app = App::i();

        //EDITAL CINEMA E VIDEO
        //HOOK ADD BOTÃO NOS EDITAIS DOCUMENTAIS
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunity = $this->controller->requestedEntity;
            $type_evaluation = $opportunity->evaluationMethodConfiguration->getDefinition()->slug;
            if ($type_evaluation == 'documentary') {
                $opportunity = $this->controller->requestedEntity;
                $this->part('reports/button-report', ['entity' => $opportunity]);
            }
        });
        //HOOK PARA GERAR O RELATÓRIO DOCUMENTAL
        $app->hook("<<GET|POST>>(opportunity.documentQualificationSummary)", function () use ($app) {
            $opportunityId = (int) $this->data['id'];
            $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
            $date = isset($this->data['publishDate']) ? $this->data['publishDate'] : date("d/m/Y");
            $datePublish = date("d/m/Y", strtotime($date));
            //$cinemaVideo = new ReportEvaluationDocumental();
            //$cinemaVideo->documentqualificationsummary($opportunityId, $format, $datePublish);
            $opportunity =  $app->repo("Opportunity")->find($opportunityId);
            $report = new ReportLib();
            $filePDF = __DIR__ . '/temp-files/resultado-preliminar.pdf';
            $fileXLS = __DIR__ . '/temp-files/resultado-preliminar.xls';
            $inputJRXML = __DIR__ . '/jasper/jrxml/resultado-preliminar.jrxml';
            $inputJASPER = __DIR__ . '/jasper/jrxml/resultado-preliminar.jasper';
            $outputReportFile = __DIR__ . '/temp-files';
            $inputJasper = __DIR__ . '/jasper/build/resultado-preliminar.jasper';
            //$dataFile = __DIR__ . '/jasper/data-adapter-json/data.json';
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
                $jsonFile = json_encode($json_array);
                $stringFile = '{"data":'.$jsonFile.'}';
                $arquivoData = 'data.json';
                $file = fopen(__DIR__ . '/jasper/data-adapter-json/' . $arquivoData, 'w');
                fwrite($file, $stringFile);
                fclose($file);
                $dataFile = __DIR__.'/jasper/data-adapter-json/data.json';
            }

            $publish = (array)$app->repo("Opportunity")->findOpportunitiesWithDateByIds($opportunityId);
            $driver = 'json';
            $data_divulgacao = $datePublish;
            $nome_edital = utf8_encode($publish[0]['name']);
            $query = null;
            if (file_exists($inputJasper)) {
                if ($format == 'pdf') {
                    $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao, $nome_edital);
                    $report->downloadFiles($filePDF, $dataFile);
                } else {
                    $report->executeReport($inputJasper, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao, $nome_edital);
                    $report->downloadFiles($fileXLS, $dataFile);
                }
            } else {
                if ($format == 'pdf') {
                    $report->buildReport($inputJRXML);
                    $report->executeReport($inputJASPER, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao, $nome_edital);
                    $report->downloadFiles($filePDF, $dataFile);
                } else {
                    $report->buildReport($inputJRXML);
                    $report->executeReport($inputJASPER, $outputReportFile, $dataFile, $format, $driver, $query, $data_divulgacao, $nome_edital);
                    $report->downloadFiles($fileXLS, $dataFile);
                }
            }
        });
    }
    public function register()
    {
        // register metadata, taxonomies
        $app = App::i();
        //$app->registerController('reportevaluationdocumental', 'Report\Controllers\ReportEvaluationDocumental');
        $app->registerController('teste', 'Report\Controllers\Teste');
    }
}
