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
- [x] Idiomas
- [x] Salidas a domiciolo
- [x] Los servicios ofrecidos se selecionan
  - [x] Ver como se envian los datos de los servicios: servicios ofrecidos, salidas, 
- 


# TODO

- [ ] Aplicar diseño deseado por el cliente


# BUG

- [ ] Las imagenes se muestran de forma incorrecta al subirse 
- [ ] El horario se maneja de forma incorrecta al editar
- [ ] El formulario debe avisar que el nombre de no debe exceder los 30 caracteres

# OMITIDO PORQUE NO ESTA EN EL PRESUPUESTO

- [ ] Visitante no existe al editar [OMITIR]
  

#############################################


Estos son los datos que se envian

--- Preparando para enviar formulario ---
newPost.js:802 Valores que se enviarían:
newPost.js:808 Datos del FormData:
newPost.js:817 token: 8e6ec36a61867b977eb70dd8f53f8fab
newPost.js:817 order: 0
newPost.js:817 seller_type: 2
newPost.js:817 dis: 1
newPost.js:817 horario-inicio: 01:00
newPost.js:817 horario-final: 18:30
newPost.js:817 lang-1: 
newPost.js:817 lang-2: 
newPost.js:817 photo_name[]: 20250417/cbb1f5347cffb4b5af6550bc4bee882e0.jpg
newPost.js:817 photo_name[]: 20250417/34ac26801dc49b7d9530bade261eecc70.jpg
newPost.js:817 tipo_usuario: 2
newPost.js:817 plan: gratis
newPost.js:817 name: Test a 
newPost.js:817 category: 328
newPost.js:817 region: 2
newPost.js:817 tit: Test a Test a Test a 
newPost.js:817 text: Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a Test a 
newPost.js:817 servicios[]: masaje_deportivo
newPost.js:817 horario_dia[martes][inicio]: 01:00
newPost.js:817 horario_dia[martes][fin]: 18:30
newPost.js:817 phone: 04120825234
newPost.js:817 idioma_1: 
newPost.js:817 nivel_idioma_1: 
newPost.js:817 idioma_2: 
newPost.js:817 nivel_idioma_2: 
newPost.js:817 out: 1
newPost.js:817 email: andoryyou@gmail.com
newPost.js:817 plan_seleccionado: gratis
newPost.js:817 terminos: 1
newPost.js:817 notifications: 1
newPost.js:829 Datos como objeto (puede ocultar valores si hay claves repetidas): {token: '8e6ec36a61867b977eb70dd8f53f8fab', order: '0', seller_type: '2', dis: '1', horario-inicio: '01:00', …}

necesitamos que el usuario pueda elegir el orden la imagen, 

# arrow left
<svg data-testid="geist-icon" height="16" stroke-linejoin="round" style="color:currentColor" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.46966 13.7803L6.99999 14.3107L8.06065 13.25L7.53032 12.7197L3.56065 8.75001H14.25H15V7.25001H14.25H3.56065L7.53032 3.28034L8.06065 2.75001L6.99999 1.68935L6.46966 2.21968L1.39644 7.2929C1.00592 7.68342 1.00592 8.31659 1.39644 8.70711L6.46966 13.7803Z" fill="currentColor"></path></svg>

# y arrow right 
<svg data-testid="geist-icon" height="16" stroke-linejoin="round" style="color:currentColor" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.53033 2.21968L9 1.68935L7.93934 2.75001L8.46967 3.28034L12.4393 7.25001H1.75H1V8.75001H1.75H12.4393L8.46967 12.7197L7.93934 13.25L9 14.3107L9.53033 13.7803L14.6036 8.70711C14.9941 8.31659 14.9941 7.68342 14.6036 7.2929L9.53033 2.21968Z" fill="currentColor"></path></svg>

que la primera se va a indicar en el servidor que es la primera

en la base de datos de la imagen existe la columna posicion que supongo que define la posicion de la imagen, habría que modificar el js y php, supongo
