<?php
include("./lib/Report.php");

class ReportController 
{

    private $report;

    public function __construct()
    {
        $this->report = new Report();
    }
    
    public function reportGeneration()
    {
        $file = __DIR__ . '/jasper/reports/resultado-preliminar.pdf';
        $inputJRXML = __DIR__ . '/jasper/resultado-preliminar.jrxml';
        $outputReportFile = __DIR__ . '/jasper/reports';
        $inputJasper = __DIR__ . '/jasper/resultado-preliminar.jasper';
        $dataFile = __DIR__ . '/data/data.json';
        $format = 'PDF';
        $driver = 'JSON';
        $query = null;
        if (file_exists($inputJasper)) {
            $this->report->executeReport($inputJasper, $outputReportFile, $dataFile, $format,$driver, $query);
            $this->report->downloadFiles($file);
        } else {
            $this->report->buildReport($inputJRXML);
            $this->report->executeReport($inputJasper, $outputReportFile, $dataFile, $format,$driver, $query);
            $this->report->downloadFiles($file);
        }

    }
}
