<?php
use PHPJasper\PHPJasper;

class ReportLib implements ReportLibInterface
{

    public function buildReport($inputJRXML, $path_build)
    {
        $jasper = new PHPJasper;
        $output_build = $path_build;
        $compile = $jasper->compile($inputJRXML);
        return $jasper -> process($compile, $output_build);
    }
    public function executeReport($inputReport, $outputReport, $dataFile, $format, $params, $driver, $query)
    {
        $options = [
            'format' => [$format],
            'params' => [
                $params
            ],
            'locale' => 'en',
            'db_connection' => [
                'driver' => $driver,
                'data_file' => $data_file,
                'json_query' => $query || null
            ]
        ];

    }

}
