<h2>Estadisticas</h2>

<div class="statistic_options">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Mes</label>
                <select class="form-control" id="mes">
                    <option value="0">Todos</option>
                   
                </select>
            </div>
        </div>  
        <div class="col-md-4">
            <div class="form-group">
                <label>Tipo</label>
                <select class="form-control" id="data_key">
                    <option value="0">Todos</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="statistic_container">
    <canvas id="estadisticas"></canvas>
</div>

<script src="<?=getConfParam('SITE_URL');?>sc-admin/res/chart.min.js"></script>
<script src="<?=getConfParam('SITE_URL');?>sc-admin/res/statistics.js"></script>