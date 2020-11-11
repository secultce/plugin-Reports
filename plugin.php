<?php
// namespace ReportsPlugin;

// class Plugin extends \MapasCulturais\Plugin
// {
//     public function _init()
//     {
//         // enqueue scripts and styles

//         // add hooks
//     }

//     public function register()
//     {
//         // register metadata, taxonomies

//     }
// }


require './controller/ReportController.php';


class Plugin extends ReportController{

    private $report;
    
    function exec(){
       $this->report = new ReportController();
       return $this->report->reportGeneration();
    }
}

$obj = new Plugin();

echo $obj->exec();