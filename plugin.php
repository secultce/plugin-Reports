<?php
namespace Report;

require_once PLUGINS_PATH . 'Report/Controllers/ReportEvaluationsDocumental.php';

use MapasCulturais\App,
    MapasCulturais\Entities,
    MapasCulturais\Definitions,
    MapasCulturais\Exceptions,
    MapasCulturais\i;
use Report\Controllers\ReportEvaluationsDocumental;
class Plugin extends \MapasCulturais\Plugin
{

    private $cinemaVideo;
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
            $this->part('reports/button-report', ['entity' => $opportunity]);
        });
        $app->hook("<<GET|POST>>(reportevaluationdocumental.documentqualificationsummary)", function () use ($app) {
            $opportunityId = (int) $this->data['id'];
            $format = isset($this->data['fileFormat']) ? $this->data['fileFormat'] : 'pdf';
            $date = isset($this->data['publishDate']) ? $this->data['publishDate'] : date("d/m/Y");
            $datePubish = date("d/m/Y", strtotime($date));
            $cinemaVideo = new ReportEvaluationsDocumental();
            $cinemaVideo->documentqualificationsummary($datePubish, $format, $opportunityId);
        });
    }
    public function register()
    {   
        // register metadata, taxonomies
        $app = App::i();
        $app->registerController('reportevaluationdocumental', 'Report\Controllers\ReportEvaluationsDocumental');
    }
}