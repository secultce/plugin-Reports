<?php
use Report;

class ReportController
{

    private $report;


    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    public function reportGeneration()
    {
        $inputReport = __DIR__ . '/jasper/resultado-preliminar.jrxml';
        $outputReportBuild = __DIR__ . '/jasper';
        $inputReportBuild = __DIR__ . '/jasper/resultado-preliminar.jasper';
        $dataFile = __DIR__ . '/data/data.json';
        $format = 'PDF';
        $driver = 'JSON';
        $query = null;
        if (file_exists($inputReportBuild)) {
            $this->report->executeReport($inputReportBuild, $outputReportBuild, $dataFile, $format, )
        } else {

        }

    }
}
