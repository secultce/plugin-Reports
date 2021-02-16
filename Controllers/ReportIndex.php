<?php
namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use ReportLib;

class ReportIndex
{
    public function generationJSONFile($json_array)
    {
        $jsonFile = json_encode($json_array);
        $stringFile = '{"data":'.$jsonFile.'}';
        $extensao = '.json';
        $somenteNome = 'data';
        $rand = rand(0, 99999999999999999);
        $arquivoData = $somenteNome.$rand.$extensao;
        $dataFile = __DIR__.'/../jasper/data-adapter-json/'.$arquivoData;
        $file = fopen(__DIR__ . '/../jasper/data-adapter-json/' . $arquivoData, 'w');
        fwrite($file, $stringFile);
        fclose($file);
        return $dataFile;
    }
}
