<?php

namespace Report;


use MapasCulturais\App;
use MapasCulturais\Entities;
use MapasCulturais\i;
use ReportLib;

class Plugin extends \MapasCulturais\Plugin
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function _init()
    {
        $app = App::i();


        //HOOK ADD BOTÃO NOS EDITAIS DOCUMENTAIS
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunityId = $this->controller->requestedEntity->id;
            $opportunity = $this->controller->requestedEntity;
            $type_evaluation = $opportunity->evaluationMethodConfiguration->getDefinition()->slug;
            if ($type_evaluation == 'documentary' && $opportunityId != '2852') {
                $opportunity = $this->controller->requestedEntity;
                $this->part('reports/button-report', ['entity' => $opportunity]);
            }
        });
        //HOOK ADD BOTÃO NOS EDITAIS DE PARECER TÉCNICO
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunity = $this->controller->requestedEntity;
            $type_evaluation = $opportunity->evaluationMethodConfiguration->getDefinition()->slug;
            if ($type_evaluation == 'technical') {
                $opportunity = $this->controller->requestedEntity;
                $this->part('reports/technical--buton-report', ['entity' => $opportunity]);
            }
        });

        //HOOK ADD BOTÃO NO EDITAL DE AUXÍLIO FINANCEIRO AO SETOR DE EVENTOS
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunityId = $this->controller->requestedEntity->id;
            $opportunity = $this->controller->requestedEntity;
            if ($opportunityId == '2852') {
                $this->part('reports/button-auxilio-eventos-report', ['entity' => $opportunity]);
            }
        });
        //HOOK ADD BOTÃO DE PAGAMENTOS NO EDITAL DE AUXÍLIO FINANCEIRO AO SETOR DE EVENTOS
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunityId = $this->controller->requestedEntity->id;
            $opportunity = $this->controller->requestedEntity;
            if ($opportunityId == '2852') {
                $this->part('reports/button-pagamento-auxilio-eventos', ['entity' => $opportunity]);
            }
        });

        //HOOK ADD BOTÃO DE RANKING NO EDITAL DE AUXÍLIO FINANCEIRO AO SETOR DE EVENTOS
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $opportunityId = $this->controller->requestedEntity->id;
            $opportunity = $this->controller->requestedEntity;
            if ($opportunityId == '2852') {
                $this->part('reports/button-ranking-auxilio-eventos', ['entity' => $opportunity]);
            }
        });
    }
    public function register()
    {
        // register metadata, taxonomies
        $app = App::i();
        $app->registerController('documental', 'Report\Controllers\Documental');
        $app->registerController('tecnico', 'Report\Controllers\Tecnico');
        $app->registerController('auxilioEventos', 'Report\Controllers\AuxilioEventos');
        $app->registerController('pagamentoAuxilioEventos', 'Report\Controllers\PagamentoAuxilioEventos');
        $app->registerController('rakingAuxilioEventos', 'Report\Controllers\RankingAuxilioEventos');
    }
}
