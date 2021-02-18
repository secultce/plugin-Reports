<?php
use MapasCulturais\App;
use MapasCulturais\i;

$route = App::i()->createUrl('technical', 'report', ['id'=>$entity->id]);

?>

<!--botão de imprimir-->
<a class="btn btn-default download" ng-click="editbox.open('report-evaluation-technical-options', $event)"
    rel="noopener noreferrer">Imprimir Resultado Técnico</a>

<!-- Formulário -->
<edit-box id="report-evaluation-technical-options" position="top"
    title="<?php i::esc_attr_e('Imprimir Resultado')?>"
    cancel-label="Cancelar" close-on-cancel="true">
    <form class="form-report-evaluation-technical-options"
        action="<?=$route?>" method="POST">
        <label for="publishDate">Data publicação</label>
        <input type="date" name="publishDate" id="publishDate">
        <label for="from">Formato</label>
        <select name="fileFormat" id="fileFormat">
            <option value="pdf" selected>PDF</option>
            <option value="xls">XLS</option>
            <!-- <option value="docx">DOC</option> -->
        </select>
        <button class="btn btn-primary download" type="submit">Imprimir Resultado</button>
    </form>
</edit-box>