<h3 class="title_main text-center">Todas las Categorías</h3>
<span class="separator main"></span>

<div class="row">
	<div class="col-md-12 mt-2 mb-5 pb-2">
		<div class="col_single categories_list">
				<? // Categorías fercode: ord ASC
				$listadoCat=selectSQL("sc_category",$a=array('parent_cat'=>"0<"),"ord ASC");
				for($i=0;$i<count($listadoCat);$i++){
					
				?>
				<a href="<?=$listadoCat[$i]['name_seo'];?>/" title="<?=$language['main.ads_of']?><?=$listadoCat[$i]['name'];?>">
					<div class="categories">
						<div class="categorie-wraper">
							<img src="<?=IMG_CATEGORY . $listadoCat[$i]['image'];?>?v=2" alt="">
							<h2><?=$listadoCat[$i]['title_html'];?></h2>
						</div>
					</div>
				</a>
				<? $otros_html=''; 
				}
				?>
		</div>
	</div>
	<div class="col-md-12">
		<div class="text-right">
			<a onclick="openMoreInfo(this)" href="javascript:void(0)" class="more-info-button">
				Sobre nosotros <span>clica aquí</span>
			</a>
		</div>
		<div class="more-info">
			<p><b>Solomasajistas.com</b> hace mucho más porque <b>ofrece la posibilidad de publicar anuncios clasificados por provincias</b> para aumentar la probabilidad de que los usuarios encuentren tus servicios profesionales en los resultados de los buscadores más importantes. Nuestras <b>categorías principales</b> son: <b>Masajistas terapéuticos, Masajistas eróticas, Masajistas hetero/gays y Bolsa de empleo.</b>
				En la categoría , también podrás publicar anuncios no relacionados con los masajes.
			</p>
			<p>
				<b>Solomasajistas.com</b> no cobra comisiones por ventas, ya que solamente somos un medio publicitário. Los anuncios aquí publicados no tienen ninguna vinculación laboral o profesional con solomasajistas.com.
			</p>
			<p>
				<b>Este sitio web nació como un punto de encuentro entre profesionales y clientes en España.</b>
			</p>
			<p>
				Publica tus anuncios gratis para ofrecer sus productos o servicios haz <a href="<?=getConfParam("SITE_URL")?>faq.php">clic aquí </a>para más información.
			</p>
			<h5 class="title-links">Los más buscados</h5>
      		<hr>
			<div class="main-links">
				<a href="/masajistas-eroticos-en-madrid/">masajes eróticos <span>Madrid</span></a>
				<a href="/masajistas-eroticos-en-barcelona/">masajes eróticos <span>Barcelona</span></a>
				<a href="/masajistas-eroticos-en-valencia/">masajes eróticos <span>Valencia</span></a>
				<a href="/masajistas-eroticos-en-alicante/">masajes eróticos <span>Alicante</span></a>
				<a href="/masajistas-eroticos-en-sevilla/">masajes eróticos <span>Sevilla</span> </a>
				<a href="/masajistas-eroticos-en-malaga/">masajes eróticos <span>Málaga</span></a>
				<a href="/masajistas-terapeuticos-en-madrid/">masajes terapéuticos <span>Madrid</span></a>
				<a href="/masajistas-terapeuticos-en-barcelona/">masajes terapéuticos <span>Barcelona</span></a>
				<a href="/masajistas-terapeuticos-en-valencia/">masajes terapéuticos <span>Valencia</span></a>
				<a href="/masajistas-terapeuticos-en-barcelona/keyword/masaje-descontracturante/">masajes descontracturantes <span>Barcelona</span></a>
				<a href="/masajistas-terapeuticos-en-madrid/keyword/masaje-descontracturante/">masajes descontracturantes <span>Madrid</span></a>
				<a href="/masajistas-terapeuticos-en-madrid/keyword/masaje-tailandes/">masaje tailandés <span>Madrid</span></a>
				<a href="/masajistas-terapeuticos-en-barcelona/keyword/masaje-tailandes/">masaje tailandés <span>Barcelona</span></a>
				<a href="/masajistas-terapeuticos-en-madrid/keyword/masajes-a-domicilio/">masajes a domicilio <span>Madrid</span></a>
				<a href="/masajistas-terapeuticos-en-barcelona/keyword/masajes-a-domicilio/">masajes a domicilio <span>Barcelona</span></a>
				<a href="/masajistas-eroticos-en-madrid/keyword/masaje-tantrico/">masajes tántricos <span>Madrid</span></a>
				<a href="/masajistas-eroticos-en-barcelona/keyword/masaje-tantrico/">masajes tántricos <span>Barcelona</span></a>
				<a href="/masajistas-eroticos-en-valencia/keyword/masaje-tantrico/">masajes tántricos <span>Valencia</span></a>
				<a href="/masajistas-eroticos-en-madrid/keyword/masajes-eroticos-en-pareja/">masajes en parejas <span>Madrid</span></a>
				<a href="/masajistas-eroticos-en-barcelona/keyword/masajes-eroticos-en-pareja/">masajes en parejas <span>Barcelona</span></a>
				<a href="https://www.solomasajistas.com/masajistas-eroticos-en-barcelona/">masajes final feliz Barcelona</a>
				<a href="https://www.solomasajistas.com/masajistas-eroticos-en-barcelona/">masajistas final feliz Barcelona</a>
				<a href="https://www.solomasajistas.com/masajistas-eroticos-en-barcelona/keyword/masajes-lingam/">masajes lingam Barcelona</a>
				<a href="https://www.solomasajistas.com/masajistas-eroticos-en-madrid/">masajes final feliz Madrid</a>
				<a href="https://www.solomasajistas.com/masajistas-eroticos-en-madrid/">masajistas final feliz Madrid</a>
				<a href="https://www.solomasajistas.com/masajistas-eroticos-en-madrid/keyword/masajes-lingam/">masajes lingam Madrid</a>
			</div>
		</div>
	</div>

</div>



<? 
// $items = selectSQL("sc_ad", $w = array('price'=>'7777<'));
		
// 		console_log($items);
loadBlock('adv-msg');
?>



<?php if(isset($_GET['login'])): ?>
  <script>
	$(document).ready(function(){
		popupLogin();
	});
  </script>
<?php endif ?>
