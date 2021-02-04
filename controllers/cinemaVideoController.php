<?php

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';

class CinemaVideoController
{
    // private $report;
    // public function __construct(ReportLib $report)
    // {
    //     $this->report = $report;
    // }


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
}
