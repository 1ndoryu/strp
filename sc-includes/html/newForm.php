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
            <h2 class="titulo-etapa-plan">Elige un Plan</h2>
            <div class="lista-opciones grupo-radios-plan">

                <!-- Inputs de Radio (pueden estar ocultos con CSS si se prefiere) -->
                <!-- Es crucial que tengan name="plan" y los valores correctos -->
                <input type="radio" name="plan" id="plan-gratis" value="gratis" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gratis') ? 'checked' : (!isset($form_data['plan']) ? 'checked' : ''); ?> required style="display: none;">
                <input type="radio" name="plan" id="plan-silver" value="silver" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'silver') ? 'checked' : ''; ?> style="display: none;">
                <input type="radio" name="plan" id="plan-gold" value="gold" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gold') ? 'checked' : ''; ?> style="display: none;">

                <!-- Divs visuales (como los proporcionaste) -->
                <div class="primer-div-plan">
                    <div class="planes-primer-div" style="position: relative">
                        <p>0 €</p>
                        <p>Plan Gratuito</p>
                        <div class="tiempo-plan">
                            30 días
                        </div>
                    </div>
                    <div class="planes-primer-div" style="position: relative">
                        <p>12 €</p>
                        <p>Plan Silver</p>
                        <div class="tiempo-plan">
                            60 días
                        </div>
                    </div>
                    <div class="planes-primer-div" style="position: relative">
                        <p>30 €</p>
                        <p>Plan Gold</p>
                        <div class="tiempo-plan">
                            90 días
                        </div>
                    </div>
                </div>

                <div class="segundo-div-plan">
                    <!-- Plan Gratis -->
                    <div class="planes-segundo-div plan-selectable <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gratis') || !isset($form_data['plan']) ? 'marcado' : ''; ?>" data-plan-value="gratis">
                        <div class="contenido-planes-segundo-div">
                            <p class="titulosegundodiv">Plan gratuito</p>
                            <p class="descripcionsegundodiv">Prueba gratis por 30 días</p>
                        </div>
                        <button type="button" class="btn-seleccionar-plan">Seleccionar</button>
                    </div>
                    <!-- Plan Silver -->
                    <div class="planes-segundo-div plan-selectable <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'silver') ? 'marcado' : ''; ?>" data-plan-value="silver">
                        <div class="contenido-planes-segundo-div">
                            <p class="titulosegundodiv">Plan Silver</p>
                            <p class="descripcionsegundodiv">Visibilidad mejorada por 60 días.</p> <!-- Texto original -->
                        </div>
                        <button type="button" class="btn-seleccionar-plan">Seleccionar</button>
                    </div>
                    <!-- Plan Gold -->
                    <div class="planes-segundo-div plan-selectable <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gold') ? 'marcado' : ''; ?>" data-plan-value="gold">
                        <div class="contenido-planes-segundo-div">
                            <p class="titulosegundodiv">Plan Gold</p>
                            <p class="descripcionsegundodiv">Máxima visibilidad por 90 días.</p> <!-- Texto original -->
                        </div>
                        <button type="button" class="btn-seleccionar-plan">Seleccionar</button>
                    </div>
                </div>

                <div class="tercer-div-plan">
                    <p class="titulotercerdiv">Comparar características</p>
                    <table class="comparar-caracteristicas-tabla">
                        <!-- Tu tabla de comparación aquí... -->
                        <thead> <!-- Es buena práctica usar thead -->
                            <tr>
                                <th></th>
                                <th>Plan gratuito</th>
                                <th>Plan Silver</th>
                                <th>Plan Gold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Renovar anuncio</td>
                                <td>cada 24h</td>
                                <td>cada 12h</td>
                                <td>cada 12h</td>
                            </tr>
                            <tr>
                                <td>Edición de anuncio</td>
                                <td><?php echo $GLOBALS['cross']; ?></td>
                                <td>2</td>
                                <td>3</td>
                            </tr>
                            <tr>
                                <td>Chat</td>
                                <td><?php echo $GLOBALS['cross']; ?></td>
                                <td><?php echo $GLOBALS['check']; ?></td>
                                <td><?php echo $GLOBALS['check']; ?></td>
                            </tr>
                            <tr>
                                <td>Ocultar anuncio</td>
                                <td><?php echo $GLOBALS['cross']; ?></td>
                                <td><?php echo $GLOBALS['cross']; ?></td>
                                <td><?php echo $GLOBALS['check']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Fin Tabla Comparación -->

            </div> <!-- Fin lista-opciones -->

            <!-- Div de error para la validación JS (importante mantener el ID) -->
            <div class="error-msg oculto" id="error-plan">Debes seleccionar un plan.</div>


        </div>
        <!-- ======================= ETAPA 2: DATOS DEL PERFIL ======================= -->
        <div id="etapa-perfil" class="etapa oculto">
            <div class="divisor-anuncio-principal">

                <div class="imagen-anuncio">
                    <img src="<?php echo getConfParam('SITE_URL') ?>src/photos/20250412/form-imagen.jpg" alt="">
                </div>

                <div class="divisor-anuncio">
                    <div class="titulo-etapa-anuncio-div">
                        <p class="numero-etapa">1</p>
                        <h2 class="titulo-etapa">Publicar perfil</h2>
                    </div>
                    <fieldset class="frm-seccion">


                        <div class="frm-grupo">
                            <label for="nombre" class="frm-etiqueta">Crea un nombre para tu perfil</label>
                            <!-- MAPEO: name="name" esperado por backend -->
                            <input type="text" placeholder="Ejemplo: Ana"="name" id="nombre" class="frm-campo" required maxlength="50" value="<?php echo htmlspecialchars($form_data['name'] ?? ($_SESSION['data']['name'] ?? '')); ?>">
                            <div class="error-msg oculto" id="error-nombre">El nombre es obligatorio.</div>
                        </div>

                        <div class="frm-grupo">
                            <label for="categoria" class="frm-etiqueta">¿Donde quieres que se muestre tu anuncio? *</label>

                            <!-- Contenedor del selector personalizado para Categoría -->
                            <div class="custom-select-wrapper" id="custom-categoria-wrapper">
                                <!-- Botón que simula el select (trigger) -->
                                <button type="button" class="frm-campo custom-select-trigger" aria-haspopup="listbox" aria-expanded="false" data-select-id="categoria">
                                    <span class="custom-select-value">+ Categoría</span> <!-- Texto inicial del placeholder -->
                                    <span class="custom-select-arrow">▾</span>
                                </button>

                                <!-- Dropdown/Modal personalizado (inicialmente oculto) -->
                                <div class="custom-select-dropdown" role="listbox" hidden>
                                    <div class="custom-select-header">
                                        <input type="search" class="custom-select-search" placeholder="Buscar categoría..." aria-label="Buscar categoría">
                                        <button type="button" class="custom-select-close" aria-label="Cerrar selector">×</button>
                                    </div>
                                    <ul class="custom-select-options">
                                        <!-- Las opciones se generarán aquí con JS -->
                                    </ul>
                                </div>
                            </div>


                            <select name="category" id="categoria" class="frm-select visually-hidden" required>
                                <option value="">+ Categoría</option>
                                <?php

                                $parent = selectSQL("sc_category", $where = array('parent_cat' => -1), "ord ASC");
                                $selected_cat = $form_data['category'] ?? null;
                                foreach ($parent as $p) {
                                    $child = selectSQL("sc_category", $where = array('parent_cat' => $p['ID_cat']), "name ASC");
                                    if (count($child) > 0) { // Solo procesar si hay hijos
                                        $options_html = ''; // Para acumular opciones normales
                                        $otros_html_grp = ''; // Para acumular opciones 'Otros'
                                        foreach ($child as $c) {
                                            $selected = ($selected_cat == $c['ID_cat']) ? 'selected' : '';
                                            // CORRECCIÓN: Se eliminó el espacio inicial antes del nombre de la categoría
                                            $option_tag = '<option value="' . $c['ID_cat'] . '" ' . $selected . '>' . htmlspecialchars($c['name']) . '</option>';

                                            if ((strpos($c['name'], 'Otros') !== false) || (strpos($c['name'], 'Otras') !== false)) {
                                                $otros_html_grp .= $option_tag; // Acumular 'Otros'
                                            } else {
                                                $options_html .= $option_tag; // Acumular normales
                                            }
                                        }
                                        // Imprimir normales primero, luego 'Otros'
                                        echo $options_html;
                                        echo $otros_html_grp;
                                        // No se imprime </optgroup> porque no se abrió uno
                                    }
                                }
                                ?>
                            </select>
                            <div class="error-msg oculto" id="error-categoria">Debes seleccionar una categoría.</div>
                        </div>

                        <div class="frm-grupo">
                            <label for="provincia" class="frm-etiqueta">Seleciona una provincia *</label>

                            <!-- Contenedor del selector personalizado -->
                            <div class="custom-select-wrapper" id="custom-provincia-wrapper">
                                <!-- Botón que simula el select (trigger) -->
                                <button type="button" class="frm-campo custom-select-trigger" aria-haspopup="listbox" aria-expanded="false" data-select-id="provincia">
                                    <span class="custom-select-value">+ Selecciona una provincia</span>
                                    <span class="custom-select-arrow">▾</span> <!-- Flechita (opcional) -->
                                </button>

                                <!-- Dropdown/Modal personalizado (inicialmente oculto) -->
                                <div class="custom-select-dropdown" role="listbox" hidden>
                                    <div class="custom-select-header">
                                        <input type="search" class="custom-select-search" placeholder="Buscar provincia..." aria-label="Buscar provincia">
                                        <button type="button" class="custom-select-close" aria-label="Cerrar selector">×</button>
                                    </div>
                                    <ul class="custom-select-options">
                                        <!-- Las opciones se generarán aquí con JS -->
                                    </ul>
                                </div>
                            </div>

                            <!-- El select original AHORA ESTARÁ OCULTO, pero funcional -->
                            <!-- MAPEO: name="region" esperado por backend -->
                            <select name="region" id="provincia" class="frm-select visually-hidden" required>
                                <option value="">+ Selecciona una provincia</option>
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
                    </fieldset>

                    <fieldset class="frm-seccion">

                        <div class="frm-grupo">
                            <label for="titulo_anuncio" class="frm-etiqueta">Título del Anuncio *</label>
                            <!-- MAPEO: name="tit" esperado por backend -->
                            <input type="text" name="tit" id="titulo_anuncio" class="frm-campo" required minlength="10" maxlength="50" placeholder="Ej: Masajista Profesional en Madrid Centro" value="<?php echo htmlspecialchars($form_data['tit'] ?? ''); ?>">
                            <div class="contador-caracteres">Caracteres: <span id="cont-titulo">0</span> (min 10 / máx 50)</div>
                            <div class="error-msg oculto" id="error-titulo">El título es obligatorio (entre 10 y 50 caracteres).</div>
                            <div class="error-msg oculto" id="error-titulo-palabras">El título contiene palabras no permitidas.</div>
                        </div>

                        <div class="frm-grupo">
                            <label for="descripcion" class="frm-etiqueta">Descripción</label>
                            <label for="descripcion" class="frm-etiqueta">Acerca de mí *</label>
                            <!-- MAPEO: name="text" esperado por backend -->
                            <textarea name="text" id="descripcion" class="frm-campo frm-textarea" rows="6" required minlength="100" maxlength="500" placeholder="Describe tus servicios, experiencia, ambiente, etc."><?php echo htmlspecialchars($form_data['text'] ?? ''); ?></textarea>
                            <div class="contador-caracteres">Caracteres: <span id="cont-desc">0</span> (min 100 / máx 500)</div>
                            <div class="error-msg oculto" id="error-descripcion">La descripción es obligatoria (entre 100 y 500 caracteres).</div>
                            <div class="error-msg oculto" id="error-desc-palabras">La descripción contiene palabras no permitidas.</div>
                        </div>

                        <div class="frm-grupo">
                            <label class="frm-etiqueta">Servicios *</label>

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

                    <div class="progresos-etapa">
                        <div class="numero-etapa-progreso etapa-actual-progreso ">
                            <p>1</p>
                        </div>
                        <div class="linea-etapa-progreso"></div>
                        <div class="numero-etapa-progreso">
                            <p>2</p>
                        </div>
                        <div class="linea-etapa-progreso"></div>
                        <div class="numero-etapa-progreso">
                            <p>3</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================= ETAPA 3: ETAPA ANUNCIO ======================= -->
        <div id="etapa-anuncio" class="etapa oculto">
            <div class="divisor-anuncio-principal">
                <div class="imagen-anuncio">
                    <img src="<?php echo getConfParam('SITE_URL') ?>src/photos/20250412/form-imagen.jpg" alt="">
                </div>
                <div class="divisor-anuncio">

                    <div class="titulo-etapa-anuncio-div">
                        <p class="numero-etapa">2</p>
                        <h2 class="titulo-etapa">Detalles</h2>
                    </div>

                    <div class="frm-grupo">
                        <label class="frm-etiqueta">Sube tus fotos (hasta <?= htmlspecialchars($DATAJSON['max_photos'] ?? 3) ?>)</label>
                        <div class="ayuda-texto">Puedes arrastrar y soltar las imágenes. Tamaño máx. 2MB (JPG, PNG). La primera foto será la principal.</div>
                        <div class="subida-fotos-contenedor">
                            <div id="boton-subir-foto" class="boton-subir">
                                <span>Haz click o arrastra para subir</span>
                                <input type="file" id="campo-subir-foto" multiple accept="image/jpeg, image/png" style="/* display: none; */ position:absolute; opacity: 0; top:0; left:0; bottom:0; right:0; cursor:pointer;">
                            </div>
                            <div id="lista-fotos-subidas" class="lista-fotos sortable">

                            </div>

                        </div>
                        <div class="error-msg oculto" id="error-fotos">Debes subir al menos una foto. La primera que subas será la principal.</div>
                        <div class="error_msg" id="error_photo_generic" style="<?php echo (isset($form_data['photo_name']) && count($form_data['photo_name']) == 0 && $error_insert) ? 'display:block;' : 'display:none;'; ?>">Sube al menos una foto para tu anuncio.</div>
                    </div>

                    <div class="frm-grupo">
                        <label class="frm-etiqueta">Administrar horario*</label>

                        <?php /* Botón para mostrar/ocultar el horario */ ?>
                        <button type="button" id="btn-mostrar-horario" class="frm-boton">Administrar horario</button>

                        <div class="ayuda-texto oculto" id="ayuda-horario">Marca los días que estás disponible y selecciona tu horario.</div>

                        <div class="horario-semanal oculto" id="contenedor-horario"> <?php /* Añadido ID y clase oculto */ ?>
                            <?php
                            $dias = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
                            foreach ($dias as $key => $nombre) {
                            ?>
                                <div class="dia-horario" id="horario-<?= $key ?>" data-dia="<?= $key ?>"> <?php /* Añadido data-dia */ ?>
                                    <span class="nombre-dia"><?= $nombre ?>:</span> <?php /* Nombre del día */ ?>

                                    <?php /* Botón de estado (Disponible/No disponible) */ ?>
                                    <button type="button" class="btn-dia-estado no-disponible" data-dia="<?= $key ?>">No disponible</button>

                                    <div class="horas-dia oculto"> <?php /* Sigue oculto por defecto */ ?>
                                        <label>De:</label>
                                        <select name="horario_dia[<?= $key ?>][inicio]" class="frm-campo frm-select corto" disabled> <?php /* Añadido disabled inicial */ ?>
                                            <?php for ($h = 0; $h < 24; $h++) {
                                                $hora = sprintf('%02d', $h);
                                                echo "<option value='{$hora}:00'>{$hora}:00</option><option value='{$hora}:30'>{$hora}:30</option>";
                                            } ?>
                                        </select>
                                        <label>A:</label>
                                        <select name="horario_dia[<?= $key ?>][fin]" class="frm-campo frm-select corto" disabled> <?php /* Añadido disabled inicial */ ?>
                                            <?php for ($h = 0; $h < 24; $h++) {
                                                $hora = sprintf('%02d', $h);
                                                // Ajuste para que la hora final por defecto sea razonable (p.ej., 18:00)
                                                $selected_fin = ($hora == 18) ? 'selected' : '';
                                                echo "<option value='{$hora}:00' " . (($h == 18 && !$selected_fin) ? 'selected' : '') . ">{$hora}:00</option><option value='{$hora}:30' " . (($h == 18) ? 'selected' : '') . ">{$hora}:30</option>";
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="error-msg oculto" id="error-horario">Debes marcar al menos un día como disponible y configurar su horario.</div>
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

                    <div class="navegacion-etapa">
                        <button type="button" class="frm-boton btn-anterior">Anterior</button>
                        <button type="button" class="frm-boton btn-siguiente">Siguiente</button>
                    </div>

                    <div class="progresos-etapa">
                        <div class="numero-etapa-progreso">
                            <p>1</p>
                        </div>
                        <div class="linea-etapa-progreso"></div>
                        <div class="numero-etapa-progreso etapa-actual-progreso">
                            <p>2</p>
                        </div>
                        <div class="linea-etapa-progreso"></div>
                        <div class="numero-etapa-progreso">
                            <p>3</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================= ETAPA 4: EXTRAS OPCIONALES ======================= -->
        <div id="etapa-extras" class="etapa oculto">
            <div class="divisor-anuncio-principal">

                <div class="imagen-anuncio">
                    <img src="<?php echo getConfParam('SITE_URL') ?>src/photos/20250412/form-imagen.jpg" alt="">
                </div>

                <?php
                // --- INICIO: Añadido para cálculo de fechas ---
                $hoy = new DateTime(); // Fecha actual
                $fecha_creacion = $hoy->format('d/m/Y');
                // Clonamos el objeto para no modificar el original
                $fecha_expiracion_obj = clone $hoy;
                // Añadimos 30 días (asumiendo la opción gratuita por defecto)
                $fecha_expiracion_obj->modify('+30 days');
                $fecha_expiracion = $fecha_expiracion_obj->format('d/m/Y');
                // --- FIN: Añadido para cálculo de fechas ---
                ?>

                <!-- ========= INICIO: Estilos CSS para el Tooltip ========= -->
                <!-- Nota: Idealmente, mover esto a tu archivo CSS principal -->
                <style>
                    .icono-clock {
                        position: relative;
                        /* Necesario para posicionar el tooltip relativo al icono */
                        cursor: pointer;
                        /* Indica que se puede interactuar */
                    }

                    .clock-tooltip {
                        display: none;
                        position: absolute;
                        bottom: 110%;
                        margin-top: -30px;
                        left: 50%;
                        transform: translateX(-50%);
                        background-color: #f4f4f4;
                        color: #1b1b1b;
                        padding: 5px 10px;
                        height: 30px;
                        border-radius: 4px;
                        font-size: 12px;
                        white-space: nowrap;
                        z-index: 10;
                        opacity: 0;
                        transition: opacity 0.3s ease;
                        pointer-events: none;
                    }

                    /* Opcional: Pequeño triángulo/flecha para el tooltip */
                    .clock-tooltip::after {
                        content: "";
                        position: absolute;
                        top: 100%;
                        /* En la parte inferior del tooltip */
                        left: 50%;
                        margin-left: -5px;
                        border-width: 5px;
                        border-style: solid;
                        border-color: #333 transparent transparent transparent;
                    }

                    /* Mostrar tooltip al hacer hover en el icono-clock (usando JS) */
                    /* No se necesita regla CSS directa para hover si se maneja con JS */
                </style>
                <!-- ========= FIN: Estilos CSS para el Tooltip ========= -->


                <div class="divisor-anuncio">

                    <div class="titulo-etapa-anuncio-div">
                        <p class="numero-etapa">3</p>
                        <h2 class="titulo-etapa">Finalizar</h2>
                    </div>

                    <div class="lista-opciones grupo-checkboxes-extra">
                        <?php $selected_extras = $form_data['extras'] ?? []; ?>

                        <label class="opcion-checkbox opcion-extra">
                            <input type="checkbox" name="extras[]" value="premium" <?php echo in_array('premium', $selected_extras) ? 'checked' : ''; ?>>
                            <?php echo $GLOBALS['sonrisa_uno']; ?>
                            <div class="opcion-contenido">
                                <strong>Premium</strong>
                                <span>Tu anuncio aparecerá aleatoriamente en las posiciones superiores.</span>
                            </div>
                            <div class="precio-y-tiempo">
                                <p>35 €</p>
                                <!-- Cambiado 'dias' por 'data-dias' y solo el número -->
                                <div class="icono-clock" data-dias="30">
                                    <?php echo $GLOBALS['clock']; ?>
                                </div>
                            </div>
                        </label>

                        <label class="opcion-checkbox opcion-extra">
                            <input type="checkbox" name="extras[]" value="premium_mini" <?php echo in_array('premium_mini', $selected_extras) ? 'checked' : ''; ?>>
                            <?php echo $GLOBALS['sonrisa_uno']; ?>
                            <div class="opcion-contenido"><strong>Premium Mini</strong><span>Tu anuncio aparecerá aleatoriamente bajo los Premium.</span></div>
                            <div class="precio-y-tiempo">
                                <p>30 €</p>
                                <!-- Cambiado 'dias' por 'data-dias' y solo el número -->
                                <div class="icono-clock" data-dias="30">
                                    <?php echo $GLOBALS['clock']; ?>
                                </div>
                            </div>
                        </label>

                        <label class="opcion-checkbox opcion-extra">
                            <input type="checkbox" name="extras[]" value="destacado" <?php echo in_array('destacado', $selected_extras) ? 'checked' : ''; ?>>
                            <?php echo $GLOBALS['sonrisa_uno']; ?>
                            <div class="opcion-contenido"><strong>Destacado</strong><span>Tu anuncio aparecerá aleatoriamente con un diseño destacado.</span></div>
                            <div class="precio-y-tiempo">
                                <p>27 €</p>
                                <!-- Cambiado 'dias' por 'data-dias' y solo el número -->
                                <div class="icono-clock" data-dias="15">
                                    <?php echo $GLOBALS['clock']; ?>
                                </div>
                            </div>
                        </label>

                        <label class="opcion-checkbox opcion-extra">
                            <input type="checkbox" name="extras[]" value="autosubida" <?php echo in_array('autosubida', $selected_extras) ? 'checked' : ''; ?>>
                            <?php echo $GLOBALS['sonrisa_uno']; ?>
                            <div class="opcion-contenido"><strong>Autosubida</strong><span>Tu anuncio subirá posiciones automáticamente (debajo de Destacados).</span></div>
                            <div class="precio-y-tiempo">
                                <p>25 €</p>
                                <!-- Cambiado 'dias' por 'data-dias' y solo el número -->
                                <div class="icono-clock" data-dias="30">
                                    <?php echo $GLOBALS['clock']; ?>
                                </div>
                            </div>
                        </label>

                        <label class="opcion-checkbox opcion-extra">
                            <input type="checkbox" name="extras[]" value="banner_superior" <?php echo in_array('banner_superior', $selected_extras) ? 'checked' : ''; ?>>
                            <?php echo $GLOBALS['sonrisa_uno']; ?>
                            <div class="opcion-contenido"><strong>Banner Superior</strong><span>Muestra tu banner aleatoriamente en la cabecera de la página.</span></div>
                            <div class="precio-y-tiempo">
                                <p>50 €</p>
                                <!-- Cambiado 'dias' por 'data-dias' y solo el número -->
                                <div class="icono-clock" data-dias="30">
                                    <?php echo $GLOBALS['clock']; ?>
                                </div>
                            </div>
                        </label>

                        <label class="opcion-checkbox opcion-extra">
                            <input type="checkbox" name="extras[]" value="banner_lateral" <?php echo in_array('banner_lateral', $selected_extras) ? 'checked' : ''; ?>>
                            <?php echo $GLOBALS['sonrisa_uno']; ?>
                            <div class="opcion-contenido"><strong>Banner Lateral</strong><span>Muestra tu banner aleatoriamente en la barra lateral.</span></div>
                            <div class="precio-y-tiempo">
                                <p>50 €</p>
                                <!-- Cambiado 'dias' por 'data-dias' y solo el número -->
                                <div class="icono-clock" data-dias="30">
                                    <?php echo $GLOBALS['clock']; ?>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="frm-grupo opcion-gratis-extra">
                        <label class="opcion-checkbox opcion-extra">

                            <input type="radio" name="plan_seleccionado" value="gratis" checked> 

                            <?php echo $GLOBALS['sonrisa_dos']; ?>
                            <div class="opcion-contenido"><strong>Gratis</strong><span>Realiza tu publicación sin costo alguno.</span></div>
                            <div class="precio-y-tiempo">
                                <p class="gratuito">Gratuito</p>
                                <!-- Cambiado 'dias' por 'data-dias' y solo el número -->
                                <div class="icono-clock" data-dias="30">
                                    <?php echo $GLOBALS['clock']; ?>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- ========= INICIO: Sección de Fechas Añadida ========= -->
                    <div class="info-fechas-anuncio" style="margin-top: 15px; margin-bottom: 15px; padding: 10px; border: 1px solid #eee; text-align: center;">
                        <p><strong>Fecha de creación:</strong> <?php echo $fecha_creacion; ?></p>
                        <p><strong>Fecha de expiración:</strong> <?php echo $fecha_expiracion; ?></p>
                    </div>
                    <!-- ========= FIN: Sección de Fechas Añadida ========= -->


                    <div class="frm-grupo">
                        <label class="frm-checkbox">
                            <input name="terminos" type="checkbox" id="terminos" value="1" required <?php echo (isset($form_data['terminos']) && $form_data['terminos'] == '1') ? 'checked' : ''; ?> />
                            He leído y acepto los <a href="/terminos-y-condiciones" target="_blank">Términos y Condiciones </a> y la <a href="/politica-privacidad" target="_blank"> Política de Privacidad</a>. *
                        </label>
                        <div class="error-msg oculto" id="error-terminos">Debes aceptar los términos y condiciones.</div>
                    </div>

                    <div class="frm-grupo" style="display: none;">
                        <label class="frm-checkbox">
                            <?php
                            $notifications_checked = true;
                            if (isset($form_data['notifications'])) {
                                $notifications_checked = ($form_data['notifications'] == '1');
                            }
                            ?>
                            <input name="notifications" type="checkbox" id="notifications" value="1" <?php echo $notifications_checked ? 'checked' : ''; ?> />
                            Quiero recibir notificaciones por email cuando alguien contacte a través de mi anuncio.
                        </label>
                    </div>


                    <div class="navegacion-etapa">
                        <button type="button" class="frm-boton btn-anterior">Anterior</button>
                        <button type="submit" id="btn-finalizar" class="frm-boton btn-publicar">Finalizar y Publicar</button>
                    </div>

                    <div class="progresos-etapa">
                        <div class="numero-etapa-progreso ">
                            <p>1</p>
                        </div>
                        <div class="linea-etapa-progreso"></div>
                        <div class="numero-etapa-progreso">
                            <p>2</p>
                        </div>
                        <div class="linea-etapa-progreso"></div>
                        <div class="numero-etapa-progreso etapa-actual-progreso">
                            <p>3</p>
                        </div>
                    </div>

                </div> <!-- Fin de .divisor-anuncio -->

                <!-- ========= INICIO: Tooltip HTML (solo uno, se reutiliza) ========= -->
                <div id="clock-tooltip" class="clock-tooltip"></div>
                <!-- ========= FIN: Tooltip HTML ========= -->


                <!-- ========= INICIO: JavaScript para el Tooltip ========= -->
                <!-- Nota: Idealmente, mover esto a tu archivo JS principal y ejecutarlo cuando el DOM esté listo -->
                <script>
                    // Espera a que el contenido del DOM esté completamente cargado
                    document.addEventListener('DOMContentLoaded', function() {

                        const tooltipElement = document.getElementById('clock-tooltip');
                        const clockIcons = document.querySelectorAll('.icono-clock');

                        if (!tooltipElement) {
                            console.error('Elemento tooltip no encontrado.');
                            return;
                        }

                        clockIcons.forEach(icon => {
                            icon.addEventListener('mouseenter', (event) => {
                                const dias = event.target.getAttribute('data-dias');
                                if (dias) {
                                    // Construir el texto del tooltip
                                    tooltipElement.textContent = `El servicio estará activo durante ${dias} días`;

                                    // Posicionar el tooltip (esto puede necesitar ajustes finos)
                                    const iconRect = event.target.getBoundingClientRect();
                                    const tooltipRect = tooltipElement.getBoundingClientRect();

                                    // Calcula la posición: encima y centrado respecto al icono
                                    // Se usa scrollX/scrollY para considerar el desplazamiento de la página
                                    let top = window.scrollY + iconRect.top - tooltipRect.height - 10; // 10px de espacio
                                    let left = window.scrollX + iconRect.left + (iconRect.width / 2) - (tooltipRect.width / 2);

                                    // Ajustes para que no se salga de la pantalla (básico)
                                    if (left < 0) left = 5;
                                    if (top < window.scrollY) top = window.scrollY + iconRect.bottom + 5; // Poner debajo si no cabe arriba

                                    // Aplicar estilos para mostrar y posicionar
                                    tooltipElement.style.top = `${top}px`;
                                    tooltipElement.style.left = `${left}px`;
                                    tooltipElement.style.display = 'block';
                                    tooltipElement.style.opacity = '1'; // Para la transición suave

                                } else {
                                    console.warn('El icono no tiene el atributo data-dias:', event.target);
                                }
                            });

                            icon.addEventListener('mouseleave', () => {
                                // Ocultar el tooltip
                                tooltipElement.style.display = 'none';
                                tooltipElement.style.opacity = '0'; // Para la transición suave
                            });
                        });

                        // Añadir el tooltip al body para evitar problemas de z-index/overflow con contenedores padres
                        // Esto es generalmente más robusto para tooltips
                        document.body.appendChild(tooltipElement);

                    });
                </script>
                <!-- ========= FIN: JavaScript para el Tooltip ========= -->
            </div>

    </form>

<?php

    ob_end_flush();
}

?>