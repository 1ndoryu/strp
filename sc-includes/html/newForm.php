<?php


function newForm()
{
    $formActionUrl = "/index.php?id=post_item";
    $insert = false; // Inicializar resultado
    $last_ad = 0;   // Inicializar ID
    $error_insert = false; // Flag de error explícito en la lógica

    ob_start();

?>

    <form id="form-nuevo-anuncio" class="formulario-multi-etapa" method="post" action="<?php echo htmlspecialchars($formActionUrl); ?>" enctype="multipart/form-data" autocomplete="off">
        <?php
        // Generar Token CSRF
        $token_q = generateFormToken('postAdToken');


        ?>
        <input type="hidden" name="token" id="token" value="<?= $token_q; ?>">
        <input type="hidden" id="new_order" name="order" value="<?php echo htmlspecialchars($form_data['order'] ?? '0'); ?>" />

        <?php // Campos ocultos (asegúrate que JS los actualice) 
        ?>
        <input type="hidden" name="seller_type" id="hidden_seller_type" value="<?php echo htmlspecialchars($form_data['seller_type'] ?? ''); ?>">
        <input type="hidden" name="dis" id="hidden_dis" value="<?php echo htmlspecialchars($form_data['dis'] ?? ''); ?>">
        <input type="hidden" name="horario-inicio" id="hidden_horario_inicio" value="<?php echo htmlspecialchars($form_data['horario-inicio'] ?? ''); ?>">
        <input type="hidden" name="horario-final" id="hidden_horario_final" value="<?php echo htmlspecialchars($form_data['horario-final'] ?? ''); ?>">
        <input type="hidden" name="lang-1" id="hidden_lang_1" value="<?php echo htmlspecialchars($form_data['lang-1'] ?? ''); ?>">
        <input type="hidden" name="lang-2" id="hidden_lang_2" value="<?php echo htmlspecialchars($form_data['lang-2'] ?? ''); ?>">
        <div id="hidden-photo-inputs">
            <?php // Repoblar fotos es complejo, mejor que JS lo maneje al cargar si hubo error 
            ?>
        </div>

        <?php // --- Etapas del Formulario (Tipo Usuario, Plan, Perfil, Extras) --- 
        ?>
        <?php // (El HTML de las etapas va aquí, usando $form_data para repoblar valores) 
        ?>
        <!-- ======================= ETAPA 0: TIPO DE USUARIO (Solo si no está logueado) ======================= -->
        <?php if (!checkSession()): ?>
            <div id="etapa-tipo-usuario" class="etapa activa">
                <h2 class="titulo-etapa-tipo-usuario">CREAR UNA CUENTA COMO:</h2>
                <div class="lista-opciones grupo-radios">
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="1" <?php echo (isset($form_data['seller_type']) && $form_data['seller_type'] == '1') ? 'checked' : ''; ?> required>
                        <div class="opcion-contenido">
                            <strong>Masajista Particular</strong>
                            <div class="separador-opcion-perfil"></div>
                            <span>Crea tu perfil individual para ofrecer tus servicios.</span>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">1 perfil individual</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Alerta de mensajes</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">4 imagenes</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Chat</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Ocultar Anuncio</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Mucho mas</span>
                            </div>
                        </div>
                        <div class="boton-selecionar-perfil" id="Masajista">Registrarse</div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="2" <?php echo (isset($form_data['seller_type']) && $form_data['seller_type'] == '2') ? 'checked' : ''; ?>>
                        <div class="opcion-contenido">
                            <strong>Centro de Masajes</strong>
                            <div class="separador-opcion-perfil"></div>
                            <span>Gestiona varios perfiles de masajistas de tu centro.</span>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Perfiles individuales</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Alerta de mensajes</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">4 imagenes</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Chat</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Ocultar Anuncio</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Mucho mas</span>
                            </div>
                        </div>
                        <div class="boton-selecionar-perfil" id="Centro">Registrarse</div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="3" <?php echo (isset($form_data['seller_type']) && $form_data['seller_type'] == '3') ? 'checked' : ''; ?>>
                        <div class="opcion-contenido">
                            <strong>Publicista</strong>
                            <div class="separador-opcion-perfil"></div>
                            <span>Promociona productos o servicios relacionados.</span>
                            <div class="opcion-contenido">
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Perfiles individuales</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Alerta de mensajes</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">4 imagenes</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Chat</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Ocultar Anuncio</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Mucho mas</span>
                                </div>
                            </div>
                        </div>
                        <div class="boton-selecionar-perfil" id="Publicista">Registrarse</div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="visitante" <?php echo (isset($form_data['seller_type']) && !in_array($form_data['seller_type'], ['1', '2', '3'])) ? 'checked' : ''; ?>>
                        <div class="opcion-contenido">
                            <strong>Visitante</strong>
                            <div class="separador-opcion-perfil"></div>
                            <span>Guarda perfiles favoritos y contacta fácilmente. (No publica anuncios)</span>
                            <div class="opcion-contenido">
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Marcar perfiles favoritos</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Ver foto de perfil</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Alerta de mensajes</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Puede contactar por chat</span>
                                </div>
                                <div class="beneficio-tipo-usuario">
                                    <?php echo $GLOBALS['check']; ?>
                                    <span class="beneficio-value">Mucho mas</span>
                                </div>
                            </div>
                        </div>
                        <div class="boton-selecionar-perfil" id="Visitante">Registrarse</div>
                    </label>
                </div>
                <div class="error-msg oculto" id="error-tipo-usuario">Debes seleccionar un tipo de usuario.</div>

                <div class="navegacion-etapa">
                    <button type="button" class="frm-boton btn-siguiente">Siguiente</button>
                </div>
            </div>
        <?php else: ?>
            <?php // Script para setear hidden_seller_type si está logueado 
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (document.getElementById("hidden_seller_type")) {
                        document.getElementById("hidden_seller_type").value = "<?php echo htmlspecialchars($_SESSION['data']['rol'] ?? ''); ?>";
                    }
                });
            </script>
        <?php endif; ?>

        <!-- ======================= ETAPA 1: ELECCIÓN DE PLAN ======================= -->
        <div id="etapa-plan" class="etapa <?php echo checkSession() ? 'activa' : 'oculto'; ?>">
            <h2 class="titulo-etapa"><?php echo checkSession() ? 'Paso 1' : 'Paso 2'; ?>: Elige tu Plan</h2>
            <p>Selecciona el plan que mejor se adapte a tus necesidades.</p>

            <div class="lista-opciones grupo-radios-plan">
                <label class="opcion-radio opcion-plan">
                    <div class="tiempo-plan">
                        30 días
                    </div>
                    <input type="radio" name="plan" value="gratis" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gratis') ? 'checked' : (!isset($form_data['plan']) ? 'checked' : ''); ?> required>
                    <div class="opcion-contenido">
                        <span class="precio-plan">0 €</span>
                        <strong>Plan Gratis</strong>
                        <div class="separador-opcion-perfil" style="border-bottom: 2px solid #fbc300;"></div>
                        <span class="pruebaSpan">Prueba gratuita de 30 días.</span>
                        <div class="opcion-contenido">
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Renovar anuncios 24h</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['cross']; ?>
                                <span class="beneficio-value">Chat</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['cross']; ?>
                                <span class="beneficio-value">Edición de anuncios</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['cross']; ?>
                                <span class="beneficio-value">Ocultar anuncio</span>
                            </div>
                        </div>

                    </div>
                    <div class="boton-selecionar-plan">Selecionar</div>
                </label>
                <label class="opcion-radio opcion-plan">
                    <div class="tiempo-plan">
                        60 días
                    </div>
                    <input type="radio" name="plan" value="silver" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'silver') ? 'checked' : ''; ?>>
                    <div class="opcion-contenido">
                        <span class="precio-plan">12 €</span>
                        <strong>Plan Silver</strong>
                        <div class="separador-opcion-perfil" style="border-bottom: 2px solid #fbc300;"></div>
                        <span class="pruebaSpan">Visibilidad mejorada por 60 días.</span>
                        <div class="opcion-contenido">
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Renovar anuncios 12h</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Chat</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">2 Ediciónes de anuncios</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['cross']; ?>
                                <span class="beneficio-value">Ocultar anuncio</span>
                            </div>
                        </div>

                    </div>
                    <div class="boton-selecionar-plan">Selecionar</div>
                </label>
                <label class="opcion-radio opcion-plan">
                    <div class="tiempo-plan">
                        90 días
                    </div>
                    <input type="radio" name="plan" value="gold" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gold') ? 'checked' : ''; ?>>
                    <div class="opcion-contenido">
                        <span class="precio-plan">30 €</span>
                        <strong>Plan Gold</strong>
                        <div class="separador-opcion-perfil" style="border-bottom: 2px solid #fbc300;"></div>
                        <span class="pruebaSpan">Máxima visibilidad por 90 días.</span>
                        <div class="opcion-contenido">
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Renovar anuncios 12h</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Chat</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">2 Ediciónes de anuncios</span>
                            </div>
                            <div class="beneficio-tipo-usuario">
                                <?php echo $GLOBALS['check']; ?>
                                <span class="beneficio-value">Ocultar anuncio</span>
                            </div>
                        </div>

                    </div>
                    <div class="boton-selecionar-plan">Selecionar</div>
                </label>
            </div>
            <div class="error-msg oculto" id="error-plan">Debes seleccionar un plan.</div>

            <div class="navegacion-etapa">
                <?php if (!checkSession()): ?>
                    <button type="button" class="frm-boton btn-anterior">Anterior</button>
                <?php endif; ?>
                <button type="button" class="frm-boton btn-siguiente">Siguiente</button>
            </div>
        </div>

        <!-- ======================= ETAPA 2: DATOS DEL PERFIL ======================= -->
        <div id="etapa-perfil" class="etapa oculto">
            <div class="divisor-anuncio-principal">
                <div class="imagen-anuncio">
                    <img src="<?php echo getConfParam('SITE_URL') ?>src/photos/20250412/form-imagen.jpg" alt="">

                </div>
                <div class="divisor-anuncio">
                    <h2 class="titulo-etapa">Publicar perfil</h2>

                    <fieldset class="frm-seccion">
                        <legend>Información Básica</legend>

                        <div class="frm-grupo">
                            <label for="nombre" class="frm-etiqueta">Crea un nombre para tu perfil</label>
                            <!-- MAPEO: name="name" esperado por backend -->
                            <input type="text" name="name" id="nombre" class="frm-campo" required maxlength="50" value="<?php echo htmlspecialchars($form_data['name'] ?? ($_SESSION['data']['name'] ?? '')); ?>">
                            <div class="error-msg oculto" id="error-nombre">El nombre es obligatorio.</div>
                        </div>

                        <div class="frm-grupo">
                            <label for="categoria" class="frm-etiqueta">¿Donde quieres que se muestre tu anuncio?</label>
                            <!-- MAPEO: name="category" esperado por backend -->
                            <!-- Asegúrate que los 'value' coincidan con los ID_cat del sistema antiguo -->
                            <select name="category" id="categoria" class="frm-campo frm-select" required>
                                <option value="">Categoría</option>
                                <?php
                                // Copiado del form antiguo para asegurar compatibilidad
                                $parent = selectSQL("sc_category", $where = array('parent_cat' => -1), "ord ASC");
                                $selected_cat = $form_data['category'] ?? null;
                                foreach ($parent as $p) {
                                    $child = selectSQL("sc_category", $where = array('parent_cat' => $p['ID_cat']), "name ASC");
                                    if (count($child) > 0) { // Solo mostrar optgroup si hay hijos
                                        $otros_html_grp = '';
                                        foreach ($child as $c) {
                                            $selected = ($selected_cat == $c['ID_cat']) ? 'selected' : '';
                                            if ((strpos($c['name'], 'Otros') !== false) || (strpos($c['name'], 'Otras') !== false)) {
                                                $otros_html_grp .= '<option value="' . $c['ID_cat'] . '" ' . $selected . '>  ' . htmlspecialchars($c['name']) . '</option>';
                                            } else {
                                                echo '<option value="' . $c['ID_cat'] . '" ' . $selected . '>  ' . htmlspecialchars($c['name']) . '</option>';
                                            }
                                        }
                                        echo $otros_html_grp; // Imprimir 'Otros' al final del grupo
                                        echo '</optgroup>';
                                    }
                                }
                                ?>
                            </select>
                            <div class="error-msg oculto" id="error-categoria">Debes seleccionar una categoría.</div>
                        </div>

                        <div class="frm-grupo">
                            <label for="provincia" class="frm-etiqueta">Seleciona una provincia *</label>
                            <!-- MAPEO: name="region" esperado por backend -->
                            <!-- Asegúrate que los 'value' coincidan con los ID_region -->
                            <select name="region" id="provincia" class="frm-campo frm-select" required>
                                <option value="">-- Selecciona una provincia --</option>
                                <?php
                                $provincias = selectSQL("sc_region", [], "name ASC");
                                $selected_region = $form_data['region'] ?? null;
                                foreach ($provincias as $prov) {
                                    $selected = ($selected_region == $prov['ID_region']) ? 'selected' : '';
                                    echo '<option value="' . $prov['ID_region'] . '" ' . $selected . '>' . htmlspecialchars($prov['name']) . '</option>';
                                }
                                ?>
                            </select>
                            <div class="error-msg oculto" id="error-provincia">Debes seleccionar una provincia.</div>
                        </div>

                        <div class="frm-grupo">
                            <label for="ciudad" class="frm-etiqueta">Ciudad / Zona (Opcional)</label>
                            <!-- MAPEO: name="city" esperado por backend -->
                            <input type="text" name="city" id="ciudad" class="frm-campo" maxlength="100" placeholder="Ej: Centro, Nervión, etc." value="<?php echo htmlspecialchars($form_data['city'] ?? ''); ?>">
                        </div>
                    </fieldset>

                    <fieldset class="frm-seccion">
                        <legend>Detalles del Anuncio</legend>

                        <div class="frm-grupo">
                            <label for="titulo_anuncio" class="frm-etiqueta">Título del Anuncio *</label>
                            <!-- MAPEO: name="tit" esperado por backend -->
                            <input type="text" name="tit" id="titulo_anuncio" class="frm-campo" required minlength="10" maxlength="50" placeholder="Ej: Masajista Profesional en Madrid Centro" value="<?php echo htmlspecialchars($form_data['tit'] ?? ''); ?>">
                            <div class="contador-caracteres">Caracteres: <span id="cont-titulo">0</span> (min 10 / máx 50)</div>
                            <div class="error-msg oculto" id="error-titulo">El título es obligatorio (entre 10 y 50 caracteres).</div>
                            <div class="error-msg oculto" id="error-titulo-palabras">El título contiene palabras no permitidas.</div>
                        </div>

                        <div class="frm-grupo">
                            <label for="descripcion" class="frm-etiqueta">Descripción, acerca de mí *</label>
                            <!-- MAPEO: name="text" esperado por backend -->
                            <textarea name="text" id="descripcion" class="frm-campo frm-textarea" rows="6" required minlength="30" maxlength="500" placeholder="Describe tus servicios, experiencia, ambiente, etc."><?php echo htmlspecialchars($form_data['text'] ?? ''); ?></textarea>
                            <div class="contador-caracteres">Caracteres: <span id="cont-desc">0</span> (min 30 / máx 500)</div>
                            <div class="error-msg oculto" id="error-descripcion">La descripción es obligatoria (entre 30 y 500 caracteres).</div>
                            <div class="error-msg oculto" id="error-desc-palabras">La descripción contiene palabras no permitidas.</div>
                        </div>

                        <div class="frm-grupo">
                            <label class="frm-etiqueta">Servicios Ofrecidos *</label>
                            <!-- ADVERTENCIA: El campo 'servicios[]' no existía. El backend podría ignorarlo o dar error. -->
                            <!-- Considera quitar el atributo 'name' si causa problemas: name="servicios_DISABLED[]" -->
                            <div class="grupo-checkboxes">
                                <?php
                                $servicios = ["Masaje relajante", "Masaje deportivo", "Masaje podal", "Masaje antiestrés", "Masaje linfático", "Masaje shiatsu", "Masaje descontracturante", "Masaje ayurvédico", "Masaje circulatorio", "Masaje tailandés"];
                                // Repoblar si hubo error
                                $selected_services = $form_data['servicios'] ?? [];
                                foreach ($servicios as $servicio) {
                                    $valor = strtolower(str_replace(' ', '_', $servicio));
                                    $checked = in_array($valor, $selected_services) ? 'checked' : '';
                                    echo '<label class="frm-checkbox"><input type="checkbox" name="servicios[]" value="' . htmlspecialchars($valor) . '" ' . $checked . '> ' . htmlspecialchars($servicio) . '</label>';
                                }
                                ?>
                            </div>
                            <div class="error-msg oculto" id="error-servicios">Debes seleccionar al menos un servicio.</div>
                        </div>

                    </fieldset>

                    <div class="navegacion-etapa">
                        <button type="button" class="frm-boton btn-anterior">Anterior</button>
                        <button type="button" class="frm-boton btn-siguiente">Siguiente</button>
                    </div>
                </div>


            </div>


        </div>

        <!-- ======================= ETAPA 3: ETAPA ANUNCIO ======================= -->
        <div id="etapa-anuncio" class="etapa oculto">
            <h2 class="titulo-etapa">Publicar anuncio</h2>



            <fieldset class="frm-seccion">
                <legend>Fotografías</legend>
                <!-- JS NECESARIO: Se necesita JS para manejar la subida (AJAX), previsualización, ordenación y generación de inputs ocultos name="photo_name[]" -->
                <div class="frm-grupo">
                    <label class="frm-etiqueta">Sube tus fotos (hasta <?= htmlspecialchars($DATAJSON['max_photos'] ?? 3) ?>)</label>
                    <div class="ayuda-texto">Puedes arrastrar y soltar las imágenes. Tamaño máx. 2MB (JPG, PNG). La primera foto será la principal.</div>
                    <div class="subida-fotos-contenedor">
                        <div id="boton-subir-foto" class="boton-subir">
                            <span>Haz click o arrastra para subir</span>
                            <!-- Este input es para SELECCIONAR. La subida real y la creación de 'photo_name[]' necesita JS -->
                            <input type="file" id="campo-subir-foto" multiple accept="image/jpeg, image/png" style="/* display: none; */ position:absolute; opacity: 0; top:0; left:0; bottom:0; right:0; cursor:pointer;">
                        </div>
                        <div id="lista-fotos-subidas" class="lista-fotos sortable">
                            <!-- Las previsualizaciones de las fotos se añadirán aquí vía JS -->
                            <!-- JS también debe añadir aquí los inputs ocultos photo_name[] O en el div #hidden-photo-inputs -->
                        </div>
                        <!-- El backend viejo no usa 'foto_principal_input'. Probablemente usa el orden del array 'photo_name[]'. -->
                        <!-- <input type="hidden" name="foto_principal" id="foto_principal_input" value="0"> -->
                    </div>
                    <div class="error-msg oculto" id="error-fotos">Debes subir al menos una foto. La primera que subas será la principal.</div>
                    <div class="error_msg" id="error_photo_generic" style="<?php echo (isset($form_data['photo_name']) && count($form_data['photo_name']) == 0 && $error_insert) ? 'display:block;' : 'display:none;'; ?>">Sube al menos una foto para tu anuncio.</div>
                </div>
            </fieldset>


            <fieldset class="frm-seccion">
                <legend>Disponibilidad y Contacto</legend>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Horario Detallado *</label>
                    <div class="ayuda-texto">Marca los días que estás disponible y selecciona tu horario.</div>
                    <!-- JS NECESARIO: Este bloque debe usarse para calcular y rellenar los campos ocultos: hidden_dis, hidden_horario_inicio, hidden_horario_final -->
                    <div class="horario-semanal">
                        <?php
                        // Repoblar horario es complejo con la estructura nueva y el error_data viejo. Requiere JS.
                        $dias = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
                        foreach ($dias as $key => $nombre) {
                        ?>
                            <div class="dia-horario" id="horario-<?= $key ?>">
                                <label class="frm-checkbox check-dia">
                                    <input type="checkbox" name="horario_dia[<?= $key ?>][activo]" value="1"> <?= $nombre ?>
                                </label>
                                <div class="horas-dia oculto">
                                    <label>De:</label>
                                    <select name="horario_dia[<?= $key ?>][inicio]" class="frm-campo frm-select corto">
                                        <?php for ($h = 0; $h < 24; $h++) {
                                            $hora = sprintf('%02d', $h);
                                            echo "<option value='{$hora}:00'>{$hora}:00</option><option value='{$hora}:30'>{$hora}:30</option>";
                                        } ?>
                                    </select>
                                    <label>A:</label>
                                    <select name="horario_dia[<?= $key ?>][fin]" class="frm-campo frm-select corto">
                                        <?php for ($h = 0; $h < 24; $h++) {
                                            $hora = sprintf('%02d', $h);
                                            $selected = ($hora == 23) ? 'selected' : ''; // Default end time selection might need JS adjustment
                                            echo "<option value='{$hora}:00'>{$hora}:00</option><option value='{$hora}:30' " . (($hora == 23) ? 'selected' : '') . ">{$hora}:30</option>"; // Simplified default end selection
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="error-msg oculto" id="error-horario">Debes marcar al menos un día y configurar su horario.</div>
                    <!-- Mensaje de error si el backend falló por horario (mapeado) -->
                    <div class="error_msg" id="error_backend_horario" style="<?php echo (isset($form_data['dis'], $form_data['horario-inicio'], $form_data['horario-final']) && (!$form_data['dis'] || !$form_data['horario-inicio'] || !$form_data['horario-final']) && $error_insert) ? 'display:block;' : 'display:none;'; ?>">Error al procesar el horario. Asegúrate de marcar días y horas.</div>
                </div>

                <div class="frm-grupo">
                    <label for="telefono" class="frm-etiqueta">Teléfono de Contacto *</label>
                    <div class="grupo-telefono">
                        <!-- MAPEO: name="phone" esperado por backend -->
                        <input type="tel" name="phone" id="telefono" class="frm-campo" required pattern="[0-9]{9,15}" placeholder="Ej: 612345678" value="<?php echo htmlspecialchars($form_data['phone'] ?? ($_SESSION['data']['phone'] ?? '')); ?>">
                        <label class="frm-checkbox check-whatsapp">
                            <!-- MAPEO: name="whatsapp" esperado por backend, value debe ser 1 -->
                            <input type="checkbox" name="whatsapp" value="1" <?php echo (isset($form_data['whatsapp']) && $form_data['whatsapp'] == 1) || (!isset($form_data['whatsapp']) && isset($_SESSION['data']['whatsapp']) && $_SESSION['data']['whatsapp'] == 1) ? 'checked' : ''; ?>> ¿Tienes WhatsApp?
                        </label>
                    </div>
                    <div class="error-msg oculto" id="error-telefono">Introduce un teléfono válido (solo números, 9-15 dígitos).</div>
                </div>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Idiomas que Hablas (Opcional)</label>
                    <!-- JS NECESARIO: Los selects idioma_1 e idioma_2 deben usarse para rellenar los campos ocultos hidden_lang_1 y hidden_lang_2 -->
                    <div class="grupo-idiomas">
                        <?php
                        $selected_lang1 = $form_data['lang-1'] ?? null;
                        $selected_lang2 = $form_data['lang-2'] ?? null;
                        ?>
                        <div class="par-idioma">
                            <select name="idioma_1" id="idioma_1" class="frm-campo frm-select">
                                <option value="">-- Idioma 1 --</option>
                                <?php // TODO: Cargar lista de idiomas COMPLETA como en el form antiguo
                                $idiomas_lista = ['es' => 'Español', 'en' => 'Inglés', 'fr' => 'Francés', 'de' => 'Alemán', 'pt' => 'Portugués', 'it' => 'Italiano']; // Ejemplo
                                foreach ($idiomas_lista as $code => $name) {
                                    // Usar el valor del campo oculto mapeado para seleccionar
                                    echo '<option value="' . htmlspecialchars($code) . '" ' . ($selected_lang1 == $code ? 'selected' : '') . '>' . htmlspecialchars($name) . '</option>';
                                }
                                ?>
                            </select>
                            <select name="nivel_idioma_1" class="frm-campo frm-select">
                                <option value="">-- Nivel --</option>
                                <option value="basico">Básico</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                                <option value="nativo">Nativo</option>
                            </select>
                        </div>
                        <div class="par-idioma">
                            <select name="idioma_2" id="idioma_2" class="frm-campo frm-select">
                                <option value="">-- Idioma 2 --</option>
                                <?php foreach ($idiomas_lista as $code => $name) {
                                    echo '<option value="' . htmlspecialchars($code) . '" ' . ($selected_lang2 == $code ? 'selected' : '') . '>' . htmlspecialchars($name) . '</option>';
                                } ?>
                            </select>
                            <select name="nivel_idioma_2" class="frm-campo frm-select">
                                <option value="">-- Nivel --</option>
                                <option value="basico">Básico</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                                <option value="nativo">Nativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="frm-grupo">
                    <label for="realiza_salidas" class="frm-etiqueta">¿Realizas salidas a domicilio/hotel? *</label>
                    <!-- MAPEO: name="out" esperado por backend -->
                    <select name="out" id="realiza_salidas" class="frm-campo frm-select" required>
                        <?php $selected_out = $form_data['out'] ?? '0'; ?>
                        <option value="0" <?php echo ($selected_out == '0') ? 'selected' : ''; ?>>No</option>
                        <option value="1" <?php echo ($selected_out == '1') ? 'selected' : ''; ?>>Sí</option>
                    </select>
                    <div class="error-msg oculto" id="error-salidas">Debes indicar si realizas salidas.</div>
                </div>

                <?php if (!checkSession()): ?>
                    <div class="frm-grupo">
                        <label for="email" class="frm-etiqueta">Tu Email de Contacto *</label>
                        <!-- MAPEO: name="email" esperado por backend -->
                        <input type="email" name="email" id="email" class="frm-campo" required placeholder="Necesario para gestionar tu anuncio" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                        <div class="ayuda-texto">Si ya tienes cuenta, usa el mismo email. Si no, crearemos una cuenta para ti.</div>
                        <div class="error-msg oculto" id="error-email">Introduce un email válido.</div>
                    </div>
                <?php else: ?>
                    <!-- Si está logueado, el backend espera el email igualmente -->
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['data']['mail']); ?>">
                    <div class="frm-grupo">
                        <label class="frm-etiqueta">Email de Contacto:</label>
                        <span class="texto-fijo"><?php echo htmlspecialchars($_SESSION['data']['mail']); ?></span>
                    </div>
                <?php endif; ?>
            </fieldset>

            <div class="navegacion-etapa">
                <button type="button" class="frm-boton btn-anterior">Anterior</button>
                <button type="button" class="frm-boton btn-siguiente">Siguiente</button>
            </div>
        </div>

        <!-- ======================= ETAPA 4: EXTRAS OPCIONALES ======================= -->
        <div id="etapa-extras" class="etapa oculto">
            <h2 class="titulo-etapa"><?php echo checkSession() ? 'Paso 3' : 'Paso 4'; ?>: Destaca tu Anuncio (Opcional)</h2>
            <p>Aumenta la visibilidad de tu anuncio con nuestros servicios extra.</p>

            <!-- ADVERTENCIA: El campo 'extras[]' no existía. El backend podría ignorarlo o dar error. -->
            <!-- Considera quitar el atributo 'name' si causa problemas: name="extras_DISABLED[]" -->
            <div class="lista-opciones grupo-checkboxes-extra">
                <?php $selected_extras = $form_data['extras'] ?? []; ?>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="premium" <?php echo in_array('premium', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Premium (35 €)</strong><span>Tu anuncio aparecerá aleatoriamente en las posiciones superiores.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="premium_mini" <?php echo in_array('premium_mini', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Premium Mini (27 €)</strong><span>Tu anuncio aparecerá aleatoriamente bajo los Premium.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="destacado" <?php echo in_array('destacado', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Destacado (20 €)</strong><span>Tu anuncio aparecerá aleatoriamente con un diseño destacado.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="autosubida" <?php echo in_array('autosubida', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Autosubida (25 €)</strong><span>Tu anuncio subirá posiciones automáticamente (debajo de Destacados).</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="banner_superior" <?php echo in_array('banner_superior', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Banner Superior (50 €)</strong><span>Muestra tu banner aleatoriamente en la cabecera de la página.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="banner_lateral" <?php echo in_array('banner_lateral', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Banner Lateral (50 €)</strong><span>Muestra tu banner aleatoriamente en la barra lateral.</span></div>
                </label>
                <!-- TODO: Añadir lógica JS para mostrar campos de subida de banner si se seleccionan -->
            </div>

            <fieldset class="frm-seccion terminos-finales">
                <div class="frm-grupo">
                    <label class="frm-checkbox">
                        <!-- MAPEO: name="terminos" esperado por backend -->
                        <input name="terminos" type="checkbox" id="terminos" value="1" required <?php echo (isset($form_data['terminos']) && $form_data['terminos'] == '1') ? 'checked' : ''; ?> />
                        He leído y acepto los <a href="/terminos-y-condiciones" target="_blank">Términos y Condiciones</a> y la <a href="/politica-privacidad" target="_blank">Política de Privacidad</a>. *
                    </label>
                    <div class="error-msg oculto" id="error-terminos">Debes aceptar los términos y condiciones.</div>
                </div>

                <div class="frm-grupo">
                    <label class="frm-checkbox">
                        <!-- MAPEO: name="notifications" esperado por backend -->
                        <?php
                        // Marcado por defecto si no hay datos previos, o según los datos previos si existen
                        $notifications_checked = true; // Por defecto
                        if (isset($form_data['notifications'])) {
                            $notifications_checked = ($form_data['notifications'] == '1');
                        }
                        ?>
                        <input name="notifications" type="checkbox" id="notifications" value="1" <?php echo $notifications_checked ? 'checked' : ''; ?> />
                        Quiero recibir notificaciones por email cuando alguien contacte a través de mi anuncio.
                    </label>
                </div>
            </fieldset>

            <div class="navegacion-etapa">
                <button type="button" class="frm-boton btn-anterior">Anterior</button>
                <!-- JS NECESARIO: El texto/acción de este botón podría cambiar según plan/extras -->
                <!-- El type="submit" es correcto para enviar el formulario directamente (sin JS reCAPTCHA) -->
                <button type="submit" id="btn-finalizar" class="frm-boton btn-publicar">Finalizar y Publicar</button>
            </div>

    </form>

<?php
    ob_end_flush();
}
