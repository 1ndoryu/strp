<h2>Solicitudes de Factura</h2>
<table id="facturas" class="display table table-responsive-md" >
    <thead>
        <tr>
            <th>Nº</th>
            <th>Nombre</th>
            <th>DNI o CIF</th>
            <th>Direccion</th>
            <th>Codigo postal</th>
            <th>Zona</th>
            <th>Provincia</th>
            <th>Correo</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Tickets</th>
            <th>Facturas</th>
         
        </tr>
    </thead>
</table>

<div class="modal" id="enviar-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Subir Factura</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="delad-form" method="post" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="form-group">
                <input type="file" name="file">
            </div>

			    <input type="hidden" name="id-factura" id="factura_id">
			    <input type="hidden" name="id-ticket" id="ticket_id">

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Aceptar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
		</div>
		</form>
      </div>
    </div>
</div>
<div class="modal" id="showtickets-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">  
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tickets</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
       
        <div class="modal-body">
            <div class="list-tickets" id="display-tickets">

            </div>

        </div>
        <div class="modal-footer">
    
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

      </div>
  </div>
</div>
<div class="modal" id="showtfacturas-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">  
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Facturas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
       
        <div class="modal-body">
            <div class="list-tickets" id="display-facturas">

            </div>

        </div>
        <div class="modal-footer">
    
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

      </div>
  </div>
</div>