<?php

namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';
require_once PLUGINS_PATH . '/Report/Controllers/Report.php';

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use ReportLib;
use Report\Controllers\Report;
use OpportunityPhases\Module;

class Tecnico extends Report
{
    public function ALL_report()
    {
        $app = App::i();
        $opportunityId = (int) $this->data['id'];
        $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
        $date = isset($this->data['publishDate']) ? $this->data['publishDate'] : date("d/m/Y");
        $datePublish = date("d/m/Y", strtotime($date));

        $sqlQuantityEvaluators = "
                SELECT 
                    a.user_id, a.name
                FROM 
                    evaluation_method_configuration emc
                    JOIN agent_relation ar ON ar.object_id = emc.id
                    JOIN agent a ON ar.agent_id = a.id
                WHERE
                    ar.object_type = 'MapasCulturais\Entities\EvaluationMethodConfiguration'
                    AND emc.opportunity_id = $opportunityId;
            ";

        $stmtQuantityEvaluators = $app->em->getConnection()->prepare($sqlQuantityEvaluators);
        $stmtQuantityEvaluators->execute();
        $quantityEvaluators = $stmtQuantityEvaluators->fetchAll();

        $report = new ReportLib();
        $filePDF = __DIR__ . "/../temp-files/relatorio-tecnico.pdf";
        $fileXLS = __DIR__ . "/../temp-files/relatorio-tecnico.xls";
        $inputJRXML = __DIR__ . "/../jasper/jrxml/Relatorio_Tecnico_" . count($quantityEvaluators) . ".jrxml";
        $inputJASPER = __DIR__ . "/../jasper/jrxml/Relatorio_Tecnico_" . count($quantityEvaluators) . ".jasper";
        $outputReportFile = __DIR__ . "/../temp-files";
        $inputReportFile = __DIR__ . "/../jasper/build/Relatorio_Tecnico" . count($quantityEvaluators) . ".jasper";

        if (count($quantityEvaluators) == 0) {
            throw new Error('Não existem avaliadores!');
        }

        $sqlData = "
                SELECT data.*, ROW_NUMBER () OVER (ORDER BY media DESC) AS RANKING FROM (
                    SELECT
                        r.number n_inscricao,
                        r.category categoria,
                        r.agents_data::json->'owner'->>'nomeCompleto' proponente,
                        rm.value projeto,
                        r.agents_data::json->'owner'->>'En_Municipio' municipio, \n
            ";

        for ($i = 1; $i <= count($quantityEvaluators); $i++) {
            $sqlData .= "   COALESCE(CAST(nota_$i.result AS FLOAT), 0) nota_$i, \n";
        }

        $sqlData .= "(";

        for ($i = 1; $i <= count($quantityEvaluators); $i++) {
            if ($i == count($quantityEvaluators)) {
                $sqlData .= "   COALESCE(CAST(nota_$i.result AS FLOAT), 0)) / " . count($quantityEvaluators) . " media \n";
            } else {
                $sqlData .= "   COALESCE(CAST(nota_$i.result AS FLOAT), 0) + \n";
            }
        }

        $sqlData .= "
            FROM
                 registration r
                 LEFT JOIN registration_meta rm ON rm.object_id = CAST(TRIM(r.number, 'on-') AS INTEGER ) AND rm.key = 'projectName' \n";

        $count = 1;
        foreach ($quantityEvaluators as $qe) {
            $sqlData .= "LEFT JOIN registration_evaluation nota_$count ON nota_$count.registration_id = r.id AND nota_$count.user_id = {$qe["user_id"]} \n";
            $count++;
        }

        $sqlData .= "
            WHERE
                r.opportunity_id = $opportunityId) data;
            ";
        $driver = 'json';
        $query = null;
        $stmt = $app->em->getConnection()->prepare($sqlData);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $jsonFile = json_encode($data);
        $dataFile = $this->generationJSONFile($jsonFile);
        //echo '{"data":' . json_encode($data) . '}';
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
