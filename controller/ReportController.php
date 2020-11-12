<?php
require_once './lib/Report.php';

class ReportController
{
    private $report;
    public function __construct( Report $report)
    {
       $this->report = $report;   
    }
    
    public function reportGeneration()
    {
        $file = __DIR__ . '/../jasper/reports/resultado-preliminar.pdf';
        $inputJRXML = __DIR__ . '/../jasper/resultado-preliminar.jrxml';
        $outputReportFile = __DIR__ . '/../jasper/reports';
        $inputJasper = __DIR__ . '/../jasper/resultado-preliminar.jasper';
        $dataFile = __DIR__ . '/../data/data.json';
        $format = 'pdf';
        $driver = 'json';
        $query = null;
        if (file_exists($inputJasper)) {
            //echo 'aqui 1 '; die();
            $this->report->executeReport($inputJasper, $outputReportFile, $dataFile, $format,$driver, $query);
            $this->report->downloadFiles($file);
        } else {
            //echo 'aqui'; die();
            $this->report->buildReport($inputJRXML);
            $this->report->executeReport($inputJasper, $outputReportFile, $dataFile, $format,$driver, $query);
            $this->report->downloadFiles($file);
        }

    }
}
