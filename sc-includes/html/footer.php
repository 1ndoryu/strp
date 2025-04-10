  <div class="prueba">
    
  </div>

  <?php 
    Notice::printNotices();
  ?>

  <div class="footer-content">
    <footer class="py-md-4">
        <div class="center_content f-1150">
        <!--<font color="white" > 2020 </font><img src="/src/images/copy.png" width="14" height="14">&nbsp;&nbsp;<a href="<?=getConfParam('SITE_URL');?>" class="txt"><font color="white" ><b><?=getConfParam('SITE_NAME');?>.com     </b></font></a><a href="#"  style="text-decoration:none"><font color="white" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Política de Privacidad  - </font></a> <a href="#"  style="text-decoration:none"><font color="white" >  Aviso  - </font></a> <a href="http://subdominioanunciclas.solomasajistas.com/terminos-y-condiciones-de-uso"  style="text-decoration:none"><font color="white" > Condiciones de uso  -   </font></a> <a href="#"style="text-decoration:none"><font color="white" >Política de Cookies - </font></a> <a href="http://subdominioanunciclas.solomasajistas.com/contactar/"style="text-decoration:none"><font color="white" >Contactar</font></a>-->
        <div class="row w-100 mx-0">
          <div class="col-md-4">
            <p class="footer-parr text-md-left">
              <a href="quienes-somos"  style="text-decoration:none"><font color="white" >Quiénes somos</font></a>
            </p>
            <p class="footer-parr text-md-left">
              <a href="https://www.solomasajistas.com/blog/"  style="text-decoration:none">
                Blog de solomasajistas
              </a>
            </p>
            <p class="footer-parr text-md-left">
              <a href="contactar/"style="text-decoration:none"><font color="white" >Contactar</font></a>
            </p>

          </div>
          <div class="col-md-4">
            <div class="footer-row mt-0">
              <div>
                Siguenos en:
              </div>
              <div>
                <a href="https://www.facebook.com/solomasajistascom-1621774288114928/" target="_blank">
                  <img loading="lazy" src="<?=Images::getImage("facebook.png")?>" alt="facebook">
                </a>
                <a href="https://twitter.com/solomasajistas?lang=es" target="_blank">
                  <img loading="lazy" src="<?=Images::getImage("twitter.png")?>" alt="twitter">
                </a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <p class="footer-parr">
              <a target="_blank" class="link-green" href="https://www.solomasajistas.com/blog/consulta-las-visitas-de-solomasajistas-com/"style="text-decoration:none">
                Consulta las visitas de solomasajistas
              </a>
            </p>  
            <p class="footer-parr">
              <a target="_blank" class="link-green" href="https://www.solomasajistas.com/blog/conoce-el-panel-publicista-de-solomasajistas/"style="text-decoration:none">
                Información para publicistas
              </a>
            </p>  
      
          </div>
        </div>
        
        <div class="footer-row">

          <div class="col-footer">
            <a href="proteccion-de-datos/"  style="text-decoration:none"><font color="white" >Política de Privacidad</font></a>
          </div>
          <div class="col-footer">
            <a href="aviso-legal/"  style="text-decoration:none"><font color="white" >Aviso legal</font></a>
          </div>
          <div class="col-footer">
            <a href="terminos-y-condiciones-de-uso"  style="text-decoration:none"><font color="white" > Condiciones de uso</font></a>
          </div>
          <div class="col-footer">
            <a href="politica-de-cookies/"style="text-decoration:none"><font color="white" >Política de Cookies</font></a>
          </div>
          <div class="col-footer">
            <a href="precios/"  style="text-decoration:none">
              <img loading="lazy"  class="img-tarifas" src="<?=Images::getImage("clickhere.webp")?>" alt="pago">
            </a>
          </div>
          
        </div>
        <div class="footer-cards">
          <img loading="lazy" src="<?=Images::getImage("card-logo-visa.svg")?>" alt="visa">
          <img loading="lazy" src="<?=Images::getImage("card-logo-mastercard.svg")?>" alt="mastercard">
          <img loading="lazy" src="<?=Images::getImage("paypal_logo.png")?>" alt="paypal">
          <img loading="lazy" src="<?=Images::getImage("Bizum.png")?>" alt="bizum">
        </div>

        <div class="footer-row mb-0">
            <div>
              copyright
              <img alt="copyright" loading="lazy" class="copy" src="src/images/copy.png" width="14" height="14">
              <font color="white" >  </font>
              <a href="<?=getConfParam('SITE_URL');?>" class="txt">
                <font color="white" >
                  <b><?=getConfParam('SITE_NAME');?>.com  </b>
                </font>
              </a>
            </div>
        </div>
      </div>
    </footer>
  </div>
</div><?php //end main content ?>

<div id="popup_overlay"></div>
<div id="popup_floating"></div>

<?php include_once(ABSPATH. 'sc-includes/access.php'); ?>


<script defer type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>alertify.min.js"></script>
</body>
<script defer type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>jquery.mask.js"></script>
<script defer type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>js.js?v=0.1"></script>

<?
if(isset($_GET['id']) && $_GET['id']=="item"){ ?>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>jquery.slides.min.js"></script>
<script>
    $(function() {
      $('#slides').slidesjs({
        width: 780,
        height: 480,
		navigation: {
      		active: false,
			effect: "slide"
		}
      });
    });
  </script>
<?
}
?>
</html>
<? clean_items_off(); // IMPORTANT NOT REMOVE ?>
<? clean_images_db(); // IMPORTANT NOT REMOVE ?>
<!-- ScriptClasificados.com !-->


