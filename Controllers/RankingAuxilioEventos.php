<?php

namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';
require_once PLUGINS_PATH . '/Report/Controllers/Report.php';

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use ReportLib;
use Report\Controllers\Report;

class RankingAuxilioEventos extends Report
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
        $filePDF = __DIR__ . '/../temp-files/relatorio-ranking-auxlio-eventos.pdf';
        $fileXLS = __DIR__ . '/../temp-files/relatorio-ranking-auxlio-eventos.xls';
        $inputJRXML = __DIR__ . '/../jasper/jrxml/relatorio-ranking-auxlio-eventos.jrxml';
        $inputJASPER = __DIR__ . '/../jasper/jrxml/relatorio-ranking-auxlio-eventos.jasper';
        $outputReportFile = __DIR__ . '/../temp-files';
        $inputReportFile = __DIR__ . '/../jasper/build/relatorio-ranking-auxlio-eventos.jasper';

        $sqlData = "
            select
                --ROW_NUMBER() OVER (partition BY rm.object_id ORDER BY rm.object_id) AS registro_table, LINHA USADA PARA CHECAR DADOS REPETIDOS
                nullif(rm.value, '')::int as pontuacao,
                nullif(rm_ranking_posicao.value, '')::int  as ranking,
                rm.object_id as n_inscricao,
                r.agents_data::json->'owner'->>'nomeCompleto' as proponente,
                rm_funcao.value as funcao,
                r.agents_data::json->'owner'->>'En_Municipio' as municipio,
                rm_resultado_final.value as situacao,
                CASE
                    WHEN rm_motivo.value = '.' THEN '' 
                    ELSE
                        rm_motivo.value
                END as motivo
            from
                public.registration as r
                    inner join public.registration_meta as rm
                        on rm.object_id = r.id
                    inner join public.registration_meta as rm_funcao
                        on rm_funcao.object_id = r.id
                            and rm_funcao.key = 'field_26552'
                    inner join public.registration_meta as rm_resultado_final
                        on rm_resultado_final.object_id = r.id
                            and rm_resultado_final.key = 'resultado_final'
                    inner join public.registration_meta as rm_motivo
                        on rm_motivo.object_id = r.id
                            and rm_motivo.key = 'resultado_final_motivo' 
                    inner join public.registration_meta as rm_ranking_posicao
                        on rm_ranking_posicao.object_id = r.id
                            and rm_ranking_posicao.key = 'ranking_posicao'
            where
                r.status = 10
                and r.opportunity_id = 2852
                and rm.key = 'ranking'
            order by
            situacao asc, ranking asc, pontuacao desc
        ";

        $stmt = $app->em->getConnection()->prepare($sqlData);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $json_array = [];
        foreach ($data as $d) {
            $funcao = json_decode($d['funcao']);
            $funcaoString = $funcao[0];
            $json_array[] = [
                "ranking" => $d['ranking'],
                "n_inscricao" => $d['n_inscricao'],
                "proponente" => $d['proponente'],
                "funcao" => $funcaoString,
                "municipio" => $d['municipio'],
                "situacao" => $d['situacao'],
                "motivo" => $d['motivo']
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
