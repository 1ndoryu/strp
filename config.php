<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

// Configuramos parámetros Servidor MySQL
define( 'DB_HOST', 'PMYSQL187.dns-servicio.com:3306');
define( 'DB_USER', 'prueba');
define( 'DB_PASS', 'o0~51Kj1m');
define( 'DB_NAME', '10760600_prueba');

define( 'DEBUG', true ); //false

//Configuramos
//define('SITE_KEY', '6Lf99qUZAAAAAFj81VklB-mbobzIljUiKcYyrAXq');
define('SITE_KEY', '6LfnLtMZAAAAAKXxmM-DUVNwIHoCkCYE2zXQ32Lp');
//define('SECRET_KEY', '6Lf99qUZAAAAAA3N-761jR2zGSB-HHWtkZeOh9Mz');
define('SECRET_KEY', '6LfnLtMZAAAAAAZREi07jQJ3o7sLwwTeLjxhAZaj');

//paypal
if(DEBUG){
    define('PAYPAL_ID', 'Aa_UZpsEKuAT1xxrmHGBLBiBudTspxVes6w5NTC4Jj34qCFKGjW5hBa5Jqs0yZyA0XEY2znoQ8QErT0O');
}else{
    define('PAYPAL_ID', 'ASRqtMRwJ31FHU8DiY-T1ksaAW9q_85JUhyzCvUELfwWcOTkJQIWU56YUCNaPHJEKRherV60YluCfK68'); 
}
?>
