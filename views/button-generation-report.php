
<a class="btn btn-default download btn-report-evaluation-documental" href="" ng-click="editbox.open('report-evaluation-documental-options', $event)" rel="noopener noreferrer">Imprimir Resultado</a>


<a class="btn btn-default download"  href="">aqui</a>



<edit-box id="report-evaluation-documental-options" position="top" title="" cancel-label="Cancelar" close-on-cancel="true">
    <form class="form-report-evaluation-documental-options" action="" method="POST">

        <label for="publishDate">Data publicação</label>
        <input type="date" name="publishDate" id="publishDate">

        <label for="from">Formato</label>
        <select name="fileFormat" id="fileFormat">
            <option value="pdf" selected >PDF</option>
            <option value="xls">XLS</option>
            <option value="doc">DOC</option>
        </select>

        <button class="btn btn-primary download" type="submit">Imprimir Resultado</button>
    </form>
</edit-box> 

