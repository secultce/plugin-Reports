<?php
require __DIR__ . './../vendor/autoload.php';
use PHPJasper\PHPJasper;
require_once './lib/IRerport.php';

class ReportLib implements IReport
{

    public function buildReport($inputJRXML)
    {
        
        //$input = __DIR__."/../jasper/resultado-preliminar.jrxml";
        //echo $input; die();
        $jasper = new PHPJasper();
        $jasper->compile($inputJRXML)->execute();
    }
    public function executeReport($inputReport, $outputReport, $dataFile, $format, $driver, $query)
    {   
        //var_dump($format); die();
        $options = [
            'format' => [$format],
            'params' => [
                "data_divulgacao" => '19-12-2020',
            ],
            'locale' => 'en',
            'db_connection' => [
                'driver' => $driver,
                'data_file' => $dataFile,
                //'json_query' => $query || null,
            ],
        ];
        $jasper = new PHPJasper();

       return $jasper->process(
            $inputReport,
            $outputReport,
            $options
        )->execute();

    }

}
