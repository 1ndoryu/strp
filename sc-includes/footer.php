<footer class="py-md-4">
    <div class="center_content f-1150">
    <!--<font color="white" > 2020 </font><img src="/src/images/copy.png" width="14" height="14">&nbsp;&nbsp;<a href="<?=getConfParam('SITE_URL');?>" class="txt"><font color="white" ><b><?=getConfParam('SITE_NAME');?>.com     </b></font></a><a href="#"  style="text-decoration:none"><font color="white" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Política de Privacidad  - </font></a> <a href="#"  style="text-decoration:none"><font color="white" >  Aviso  - </font></a> <a href="http://subdominioanunciclas.solomasajistas.com/terminos-y-condiciones-de-uso"  style="text-decoration:none"><font color="white" > Condiciones de uso  -   </font></a> <a href="#"style="text-decoration:none"><font color="white" >Política de Cookies - </font></a> <a href="http://subdominioanunciclas.solomasajistas.com/contactar/"style="text-decoration:none"><font color="white" >Contactar</font></a>-->
    <div class="row w-100 text-center d-block d-md-none">
      <div class="col-md">
        <font color="white" > 2020 </font>
        <img src="src/images/copy.png" width="14" height="14">&nbsp;&nbsp;
        <a href="<?=getConfParam('SITE_URL');?>" class="txt">
          <font color="white" >
            <b><?=getConfParam('SITE_NAME');?>.com  </b>
          </font>
        </a>
      </div>
      <div class="col-md">
        <a href="#"  style="text-decoration:none"><font color="white" >Política de Privacidad</font></a>
      </div>
      <div class="col-md">
        <a href="#"  style="text-decoration:none"><font color="white" >Aviso</font></a>
      </div>
      <div class="col-md">
        <a href="http://subdominioanunciclas.solomasajistas.com/terminos-y-condiciones-de-uso"  style="text-decoration:none"><font color="white" > Condiciones de uso</font></a>
      </div>
      <div class="col-md">
        <a href="#"style="text-decoration:none"><font color="white" >Política de Cookies</font></a>
      </div>
      <div class="col-md">
        <a href="http://subdominioanunciclas.solomasajistas.com/contactar/"style="text-decoration:none"><font color="white" >Contactar</font></a>
      </div>
       
    </div>
    <div class="d-none d-md-block">
      <font color="white" > 2020 </font><img src="src/images/copy.png" width="14" height="14">&nbsp;&nbsp;<a href="<?=getConfParam('SITE_URL');?>" class="txt"><font color="white" ><b><?=getConfParam('SITE_NAME');?>.com     </b></font></a><a href="#"  style="text-decoration:none"><font color="white" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Política de Privacidad  - </font></a> <a href="#"  style="text-decoration:none"><font color="white" >  Aviso  - </font></a> <a href="http://subdominioanunciclas.solomasajistas.com/terminos-y-condiciones-de-uso"  style="text-decoration:none"><font color="white" > Condiciones de uso  -   </font></a> <a href="#"style="text-decoration:none"><font color="white" >Política de Cookies - </font></a> <a href="http://subdominioanunciclas.solomasajistas.com/contactar/"style="text-decoration:none"><font color="white" >Contactar</font></a>
    </div>
	</div>
</footer>

<div id="popup_overlay"></div>
<div id="popup_floating"></div>

</body>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>jquery.mask.js"></script>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>js.js"></script>
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
<?
// updateSQL("sc_ad",$s=array('premium2'=>0,'date_premium2'=>0),$w=array('premium2'=>1, 'date_premium2'=>time()."<"));
// updateSQL("sc_ad",$s=array('premium1'=>0,'date_premium1'=>0),$w=array('premium1'=>1, 'date_premium1'=>time()."<"));
?>

