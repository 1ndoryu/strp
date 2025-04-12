# WANDORIUS TODO 
# START 10 de abril de 2025
# Objectivo 

El formulario debe crear correctamente los anuncios. 

# COMPROBACIONES
- [x] Comprobar si procesa varias imagenes. [TRUE]
- [x] El tipo de usuario se seleciona [TRUE]
  - [x] Centro [TRUE]
  - [x] Particular [TRUE]
  - [x] Publicista [TRUE]
- [x] La categoría se selecciona [TRUE]
  - [x] Masaje terapeutico [TRUE]
  - [x] Masajista erotica [TRUE]
  - [x] Masajista hetero/gay [TRUE]
  - [x] Bolsa de empleo [TRUE]
- [x] Correo [TRUE]
- [x] Nombre [TRUE]
- [x] Provincia [TRUE]
- [x] Estado [TRUE]
- [x] Titulo anuncio [TRUE]
- [x] Numero [TRUE]
- [x] Descripcion [TRUE]
- [x] Tienes whatsapp [TRUE]

# Falta ajustar 
- [ ] Idiomas
- [ ] Salidas a domiciolo
- [ ] Los servicios ofrecidos se selecionan
  - [x] Ver como se envian los datos de los servicios: servicios ofrecidos, salidas, 


# TODO

- [ ] Aplicar diseño deseado por el cliente


# BUG

- [ ] Las imagenes se muestran de forma incorrecta al subirse 
- [ ] El horario se maneja de forma incorrecta al editar
- [ ] El formulario debe avisar que el nombre de no debe exceder los 30 caracteres

# OMITIDO PORQUE NO ESTA EN EL PRESUPUESTO

- [ ] Visitante no existe al editar [OMITIR]
  

# LOGS DE ENVIO 
Intentando enviar formulario...
newPost.js:136 Validando etapa: etapa-tipo-usuario
newPost.js:136 Validando etapa: etapa-plan
newPost.js:136 Validando etapa: etapa-perfil
newPost.js:136 Validando etapa: etapa-extras
newPost.js:571 Actualizando campos ocultos finales...
newPost.js:295 hidden_seller_type actualizado a: 1
newPost.js:344 hidden_dis actualizado a: 1
newPost.js:345 hidden_horario_inicio actualizado a: 00:00
newPost.js:346 hidden_horario_final actualizado a: 23:30
newPost.js:355 hidden_lang_1 actualizado a: 
newPost.js:359 hidden_lang_2 actualizado a: 
newPost.js:577 --- Verificando datos antes de submit ---
newPost.js:578 Token: f6c0b28df0ba4b6c0eaaf14fa29a42b4
newPost.js:579 Order: 0
newPost.js:580 Seller Type (Hidden): 1
newPost.js:581 Dis (Hidden): 1
newPost.js:582 Horario Inicio (Hidden): 00:00
newPost.js:583 Horario Final (Hidden): 23:30
newPost.js:584 Lang 1 (Hidden): 
newPost.js:585 Lang 2 (Hidden): 
newPost.js:586 Fotos (Hidden Inputs): ['20250412/ad4550223f725fd4f5dc88fc5108cad20.jpg']
newPost.js:591 Categoría: 331
newPost.js:592 Título: bolsa empleo
newPost.js:593 Teléfono: 0418545687
newPost.js:594 Email: andoryyu1@gmail.com
newPost.js:595 Términos: true
newPost.js:596 ---------------------------------------

newPost.js:599 FormData a enviar:
newPost.js:601 token: f6c0b28df0ba4b6c0eaaf14fa29a42b4
newPost.js:601 order: 0
newPost.js:601 seller_type: 1
newPost.js:601 dis: 1
newPost.js:601 horario-inicio: 00:00
newPost.js:601 horario-final: 23:30
newPost.js:601 lang-1: 
newPost.js:601 lang-2: 
newPost.js:601 photo_name[]: 20250412/ad4550223f725fd4f5dc88fc5108cad20.jpg
newPost.js:601 tipo_usuario: 1
newPost.js:601 plan: gratis
newPost.js:601 name: bolsa empleo
newPost.js:601 category: 331
newPost.js:601 region: 3
newPost.js:601 city: Ciudad test
newPost.js:601 tit: bolsa empleo
newPost.js:601 text: Descripcion bolsa emple obolsa empleo
newPost.js:601 servicios[]: masaje_relajante
newPost.js:601 servicios[]: masaje_podal
# Aqui solo selecione martes, miercoles y sabado, parece funcionar correctamente
newPost.js:601 horario_dia[lunes][inicio]: 00:00
newPost.js:601 horario_dia[lunes][fin]: 23:30
newPost.js:601 horario_dia[martes][activo]: 1
newPost.js:601 horario_dia[martes][inicio]: 00:00
newPost.js:601 horario_dia[martes][fin]: 23:30
newPost.js:601 horario_dia[miercoles][activo]: 1
newPost.js:601 horario_dia[miercoles][inicio]: 04:30
newPost.js:601 horario_dia[miercoles][fin]: 23:30
newPost.js:601 horario_dia[jueves][inicio]: 00:00
newPost.js:601 horario_dia[jueves][fin]: 23:30
newPost.js:601 horario_dia[viernes][inicio]: 00:00
newPost.js:601 horario_dia[viernes][fin]: 23:30
newPost.js:601 horario_dia[sabado][activo]: 1
newPost.js:601 horario_dia[sabado][inicio]: 06:30
newPost.js:601 horario_dia[sabado][fin]: 23:30
newPost.js:601 horario_dia[domingo][inicio]: 00:00
newPost.js:601 horario_dia[domingo][fin]: 23:30
newPost.js:601 phone: 0418545687
# Volver a probar idioma
newPost.js:601 idioma_1: 
newPost.js:601 nivel_idioma_1: 
newPost.js:601 idioma_2: 
newPost.js:601 nivel_idioma_2: 
newPost.js:601 out: 1
newPost.js:601 email: andoryyu1@gmail.com
newPost.js:601 terminos: 1
newPost.js:601 notifications: 1
newPost.js:603 ---------------------------------------
newPost.js:605 Validación completa OK. Enviando formulario directamente vía form.submit()...