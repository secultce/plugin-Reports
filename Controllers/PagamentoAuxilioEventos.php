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
                CASE
                    WHEN sp1.status = 0 then 'PEDENTE'
                    WHEN sp1.status = 1 then 'AGUARDANDO ENVIO'
                    WHEN sp1.status = 2 then 'AGUARDANDO RETORNO'
                    WHEN sp1.status = 3 then 'PAGO'
                    WHEN sp1.status = 4 then 'ERRO'
                    WHEN sp1.status is null then 'PENDENTE'
                end as parcela_1,
                CASE
                    WHEN sp2.status = 0 then 'PEDENTE'
                    WHEN sp2.status = 1 then 'AGUARDANDO ENVIO'
                    WHEN sp2.status = 2 then 'AGUARDANDO RETORNO'
                    WHEN sp2.status = 3 then 'PAGO'
                    WHEN sp2.status = 4 then 'ERRO'
                    WHEN sp2.status is null then 'PENDENTE'
                end as parcela_2,
                CASE
                    WHEN sp1.error IS NULL THEN 'PAGAMENTO NÃO ENVIADO AO BANCO'
                    ELSE sp1.error
                END  as motivo_1,
                CASE
                    WHEN sp2.error IS NULL THEN 'PAGAMENTO NÃO ENVIADO AO BANCO'
                    ELSE sp2.error
                END  as motivo_2
            from
                public.registration as r
                    INNER join public.registration_meta as rm
                        ON rm.object_id = r.id
                    left join public.secultce_payments as sp1
                        on sp1.registration_id = r.id
                        and sp1.parcela = 1
                    left join public.secultce_payments as sp2
                        on sp2.registration_id = r.id
                        and sp2.parcela = 2
                        
            where
                opportunity_id = 2852
                and r.status = 1
                and rm.key = 'field_26552'
        ";

        $stmt = $app->em->getConnection()->prepare($sqlData);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $json_array = [];
        foreach ($data as $d) {
            $funcao = json_decode($d['funcao']);
            $funcaoString = $funcao[0];
            $json_array[] = [
                'n_inscricao' => $d['n_inscricao'],
                "nome" => $d['nome'],
                "funcao" => $funcaoString,
                "municipio" => $d['municipio'],
                "parcela_1" => $d['parcela_1'],
                "motivo_1" => $d['motivo_1'],
                "parcela_2" => $d['parcela_2'],
                "motivo_2" => $d['motivo_2']
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
