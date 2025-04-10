<?php
    $stats = Statistic::getStats();
?>
<div class="stats-container">
    <!-- informacione esencial -->
    <div class="row mx-0">
        <div class="col-md-4 p-2">
            <div class="stats-card">
                <div class="row mx-0">
                    <div class="col-4 px-0">
                        <img src="<?=Images::getImage("group-blue.svg")?>" alt="icono visitas">
                    </div>
                    <div class="col-8 px-0">
                        <h4>Usuarios totales</h4>
                        <b><?=$stats['usuarios']?></b>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 p-2">
            <div class="stats-card">
                <div class="row mx-0">
                    <div class="col-4 px-0">
                        <img src="<?=Images::getImage("clock-blue.svg")?>" alt="icono visitas">
                    </div>
                    <div class="col-8 px-0">
                        <h4>Publicados ultimas 24h</h4>
                        <div class="card-row">
                            <div class="group">
                                <b><?=$stats['publicados']['normal']?></b>
                                <span>Normales</span>
                            </div>
                            <div class="group">
                                <b><?=$stats['publicados']['premium']?></b>
                                <span>pagados</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 p-2">
            <div class="stats-card">
                <div class="row mx-0">
                    <div class="col-4 px-0">
                        <img src="<?=Images::getImage("tooltip-blue.svg")?>" alt="icono visitas">
                    </div>
                    <div class="col-8 px-0">
                        <h4>Renovados ultimas 24h</h4>
                        
                        <div class="card-row">
                            <div class="group">
                                <b><?=$stats['renovados']['normal']?></b>
                                <span>Normales</span>
                            </div>
                            <div class="group">
                                <b><?=$stats['renovados']['premium']?></b>
                                <span>pagados</span>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="banner-stats">
        <h1>Site Manager</h1>
       
        <button>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>    
        Site Manager</button>
    </div>
    <div class="row mx-0">
      
        <div class="col-md-8 p-2">
            <div class="stats-card">
                <h3>Servicios Destacados</h3>
                <hr>
                <ul class="stats-list">
                    <li>Anuncios Destacados <b><?=$stats['destacados']['destacados']?></b></li>
                    <li>Anuncios Top <b><?=$stats['destacados']['top']?></b></li>
                    <li>Anuncios Autosubida <b><?=$stats['destacados']['autorenueva']?></b></li>
                    <li>Anuncios Autodiario <b><?=$stats['destacados']['autodiario']?></b></li>
                </ul>
            </div>
        </div>
        <div class="col-md-4 p-2">
            <div class="stats-card">
                <h3>Servicios ultimas 24 horas</h3>
                <hr>
                <ul class="stats-list">
                    <li><span class="stats-colored green">Pagados</span> <b><?=$stats['servicios']['pagados']?></b></li>
                    <li><span class="stats-colored blue">Pendientes</span> <b><?=$stats['servicios']['pendientes']?></b></li>
                    <li><span class="stats-colored red">Fallido</span> <b><?=$stats['servicios']['fallidos']?></b></li>
                    <li><span class="stats-colored black">Cancelado</span> <b><?=$stats['servicios']['eliminados']?></b></li>
                   
                </ul>
            </div>
        </div>
    </div>

    <div class="row mx-0">
      
        <div class="col-md-4 p-2">
            <div class="stats-card">
                <h3>Usuarios nuevos</h3>
                <hr>
                <ul class="stats-list">
                    
                    <?php foreach($stats['meses'] as $mes => $data): ?>
                        <li><?=$mes?> <b><?=$data['nuevos_usuarios']?></b></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-4 p-2">
            <div class="stats-card">
                <h3>Nuevos Pedidos</h3>
                <hr>
                <ul class="stats-list">
                    <?php foreach($stats['meses'] as $mes => $data): ?>
                        <li><?=$mes?> <b><?=$data['pedidos']?></b></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-4 p-2">
            <div class="stats-card">
                <h3>Banners para validar</h3>
                <hr class="my-2">
                <ul class="stats-list mt-3">
                    <li>banners <b><?=$stats['banners']?></b></li>
                </ul>
                <h3>Emails recibidos</h3>
                <hr class="my-2">
                <ul class="stats-list mt-3">
                    <li>Email <b><?=$stats['contactos']?></b></li>
                </ul>
                <h3>Solicitud de facturas</h3>
                <hr class="my-2">
                <ul class="stats-list mt-3">
                    <li>Facturas <b><?=$stats['facturas']?></b></li>
                </ul>
            </div>
        </div>
    </div>


</div>