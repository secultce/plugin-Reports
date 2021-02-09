<?php
namespace Report;

require_once PLUGINS_PATH . 'Report/Controllers/ReportEvaluationsDocumental.php';

use MapasCulturais\App;
use MapasCulturais\Entities;
use MapasCulturais\Definitions;
use MapasCulturais\Exceptions;
use MapasCulturais\i;
use Report\Controllers\ReportEvaluationsDocumental;

class Plugin extends \MapasCulturais\Plugin
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function _init()
    {
        $app = App::i();

        //EDITAL CINEMA E VIDEO
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunity = $this->controller->requestedEntity;
            $type_evaluation = $opportunity->evaluationMethodConfiguration->getDefinition()->slug;
            if($type_evaluation == 'documentary'){
                $opportunity = $this->controller->requestedEntity;
                $this->part('reports/button-report', ['entity' => $opportunity]);
            }
        });
    }
    public function register()
    {
        // register metadata, taxonomies
        $app = App::i();
        $app->registerController('reportevaluationdocumental', 'Report\Controllers\ReportEvaluationsDocumental');
    }
}
