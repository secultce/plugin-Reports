<?php
namespace Report\Controllers;

require_once PLUGINS_PATH . '/Report/business/ReportLib.php';

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\i;
use ReportLib;

class ReportEvaluationsTechnical extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function GET_reportTest() {
        echo "Hello World!";
    }
}