<?php
use PHPJasper\PHPJasper;

class ReportLib implements ReportLibInterface
{

    public function buildReport($inputJRXML)
    {
        $jasper = new PHPJasper;
        return $jasper->compile($inputJRXML)->execute();
    }
    public function executeReport($inputReport, $outputReport, $dataFile, $format, $driver, $query)
    {   
        
        $options = [
            'format' => [$format],
            'params' => [
                "data_divulgacao" => '19-12-2020',
            ],
            'locale' => 'en',
            'db_connection' => [
                'driver' => $driver,
                'data_file' => $data_file,
                'json_query' => $query || null,
            ],
        ];
        $jasper = new PHPJasper;

       return $jasper->process(
            $inputReport,
            $outputReport,
            $options
        )->execute();

    }

}
