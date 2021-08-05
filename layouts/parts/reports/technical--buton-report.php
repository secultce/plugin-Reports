<?php

use MapasCulturais\App;
use MapasCulturais\i;

$route = App::i()->createUrl('tecnico', 'report', ['id' => $entity->id]);

?>

<!--botão de imprimir-->
<a class="btn btn-default download" ng-click="editbox.open('report-evaluation-tecnico-options', $event)" rel="noopener noreferrer">Imprimir Resultado Técnico ***Henrique*** </a>

<!-- Formulário -->
<edit-box id="report-evaluation-tecnico-options" position="top" title="<?php i::esc_attr_e('Imprimir Resultado') ?>" cancel-label="Cancelar" close-on-cancel="true">
    <form class="form-report-evaluation-tecnico-options" action="<?= $route ?>" method="POST">
        <label for="publishDate">Data publicação</label>
        <input type="date" name="publishDate" id="publishDate">
        <label for="from">Formato</label>
        <select name="fileFormat" id="fileFormat">
            <option value="pdf" selected>PDF</option>
            <option value="xls">XLS</option>
            <!-- <option value="docx">DOC</option> -->
        </select>
        <button class="btn btn-primary download" type="submit">Imprimir Resultado ***Henrique***</button>
    </form>
</edit-box>