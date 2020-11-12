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

require_once './lib/Report.php';
require_once './controller/ReportController.php';
require_once './lib/ReportLib.php';

$obj0 =  new ReportLib();

$obj2 = new Report($obj0);

$obj = new ReportController($obj2);


$a = $obj->reportGeneration();

echo $a;
