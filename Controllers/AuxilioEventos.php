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
        //$opportunityId = (int) $this->data['id'];
        //$opportunity =  $app->repo("Opportunity")->find($opportunityId);
        $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
        $dataHora = date("d/m/Y - H:i:s", time());
        $data_divulgacao = strval($dataHora);
        $filePDF = __DIR__ . '/../temp-files/auxilio-financeiro-eventos.pdf';
        $fileXLS = __DIR__ . '/../temp-files/auxilio-financeiro-eventos.xls';
        $inputJRXML = __DIR__ . '/../jasper/jrxml/auxilio-financeiro-eventos.jrxml';
        $inputJASPER = __DIR__ . '/../jasper/jrxml/auxilio-financeiro-eventos.jasper';
        $outputReportFile = __DIR__ . '/../temp-files';
        $inputReportFile = __DIR__ . '/../jasper/build/auxilio-financeiro-eventos.jasper';

        $sqlData = " 
            with edital as (
                select distinct
                    r.number as num_inscricao,
                    r.agents_data::json->'owner'->>'nomeCompleto' proponente,
                    rm.value as funcao,
                    r.agents_data::json->'owner'->>'En_Municipio' municipio,
                    CAST (re.result as int) as resultado_status,
                    re.user_id as avaliador,
                    re.evaluation_data as obs_motivo,
                    rm.key as key
                from
                    public.registration as r
                    left join public.registration_evaluation as re
                    ON re.registration_id = r.id
                    INNER join public.registration_meta as rm
                    ON rm.object_id = r.id
                where
                    opportunity_id = 2852 --'2763'
                    and r.status = 1
                    and rm.key = 'field_26552'
                    --and rm.key = 'field_26110'
            )
            select
                num_inscricao,
                proponente,
                funcao,
                municipio,
                CASE
                    WHEN (count(*) * 10 = sum(resultado_status)) = 'false' THEN 'REPROVADO'
                    WHEN (count(*) * 10 = sum(resultado_status)) = 'true'  THEN 'APROVADO'
                    WHEN (count(*) * 10 = sum(resultado_status)) IS NULL  THEN 'AGUARDANDO AVALIAÇÃO'
                    END as situacao,
                CASE
                    WHEN (string_agg(obs_motivo, ',')) IS NULL THEN ''
                    ELSE (string_agg(obs_motivo, ','))
                    END as motivo
            from
                edital
            group by
                num_inscricao, 
                proponente, 
                funcao, 
                municipio
        
        ";
        $stmt = $app->em->getConnection()->prepare($sqlData);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $json_array = [];
        foreach ($data as $d) {
            $funcao = json_decode($d['funcao']);
            $funcaoString = $funcao[0];
            $json_array[] = [
                'n_inscricao' => $d['num_inscricao'],
                'proponente' => $d['proponente'],
                'funcao' => $funcaoString,
                'municipio' => $d['municipio'],
                'situacao' => $d['situacao'],
                'motivo' => $d['motivo']
            ];
        }

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
