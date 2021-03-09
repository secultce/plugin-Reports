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

        $sqlData = "
            select distinct 
                r.number as n_inscricao,
                r.agents_data::json->'owner'->>'nomeCompleto' nome,
                rm.value as funcao,
                r.agents_data::json->'owner'->>'En_Municipio' municipio,
                sp.valor as valor_parcela,
                CASE
                WHEN sp.status = 0 then 'PEDENTE'
                WHEN sp.status = 1 then 'AGUARDANDO ENVIO'
                WHEN sp.status = 2 then 'AGUARDANDO RETORNO'
                WHEN sp.status = 3 then 'PAGO'
                WHEN sp.status = 4 then 'ERRO'
                end as status,
                sp.error as motivo_erro,
                sp.parcela as parcela	
            from
                public.registration as r
                    left join public.registration_evaluation as re
                        ON re.registration_id = r.id
                    INNER join public.registration_meta as rm
                        ON rm.object_id = r.id
                    left join public.secultce_payments as sp
                        on sp.registration_id = r.id
            where
                opportunity_id = 2852
                and r.status = 1
                and rm.key = 'field_26552'
                and sp.parcela in (1,2)
        ";

        $stmt = $app->em->getConnection()->prepare($sqlData);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $json_data = [];
        $json_array = [];
        foreach ($data as $d) {
            $funcao = json_decode($d['funcao']);
            $funcaoString = $funcao[0];
            $json_data[] = [
                'n_inscricao' => $d['n_inscricao'],
                "nome" => $d['nome'],
                "funcao" => $funcaoString,
                "municipio" => $d['municipio'],
                "parcela_1" => $d['status'],
                "motivo_1" => $d['motivo_erro'],
                "parcela_2" => $d['status'],
                "motivo_2" => $d['motivo_erro']
            ];
        }
        $json_array[] = [
            'n_inscricao' => $json_data[0]['n_inscricao'],
            "nome" => $json_data[0]['nome'],
            "funcao" => $funcaoString,
            "municipio" => $json_data[0]['municipio'],
            "parcela_1" => $json_data[0]['parcela_1'],
            "motivo_1" => $json_data[0]['motivo_1'],
            "parcela_2" => $json_data[1]['parcela_2'],
            "motivo_2" => $json_data[1]['motivo_2']
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
