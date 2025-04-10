loadCSS("https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css");


function setModalRefund(id, refund, date){

    if(refund == 0)
    {
        $('#form-refund-date').attr('disabled', false);
        $('#form-refund-type').attr('disabled', false);
        $('#modal-refund-btn').show();
    }
    else
    {
        $('#form-refund-date').attr('disabled', true);
        $('#form-refund-date').val(date);
        $('#form-refund-type').attr('disabled', true);
        $('#modal-refund-btn').hide();
    }

    $('#form-refund-id').val(id);
    $('#form-refund-type').val(refund);
    $('#modal-refund').modal('show');
}

function setModalValidate(id){
    $('#form-pending-id').val(id);
    $('#modal_validate').modal('show');
}

function enviarFactura(id_ticket, id_factura)
{
    $("#factura_id").val(id_factura);
    $("#ticket_id").val(id_ticket);
    $("#enviar-modal").modal('show');
    $("#showtickets-modal").modal('hide');
}

$(document).ready(function() {
    if($('#facturas').length > 0)
    {
        $('#facturas').DataTable({
            "ajax": {
                "url": site_url + "sc-admin/inc/ajax/load_facturas.php",

            },
            "paging": true,
            "order": [[8, "desc"]],
            "language": {
                "search": "Buscar:",
                "lengthMenu": "_MENU_" // Esto deja solo el selector sin texto
                
            },
            "info": false,
            "columns":[
                { title: "Nº" },
                { title: "Nombre" },
                { title: "DNI o CIF" },
                { title: "Direccion" },
                { title: "Codigo postal" },
                { title: "Zona" },
                { title: "Provincia" },
                { title: "Correo" },
                { title: "Fecha" },
                { 
                    title: "Tipo",
                    data: null,
                    render: function(data, type, row)
                    {
                        if(row[9] == 0)
                            return "Autónomo"
                        else if(row[9] == 1)
                            return "Empresa"
                        else
                            return ""
                    }
                },
                { 
                    title: "Ticket",
                    data: null,
                    render: function(data, type, row) {
                        // Puedes ajustar el contenido HTML de los botones aquí
                        if(row[10] == 0)
                            return `
                                <a href="javascript:void(0)" >0</a>
                            `;
                        else
                            return `
                                <a href="javascript:void(0)" onclick="verTickets(${row[0]})" >${row[10]}</a>
                            `;
                    }

                },
                { 
                    title: "Facturas",
                    data: null,
                    render: function(data, type, row) {
             
                        return `
                            <button onclick="verFacturas('${row[0]}')" class="btn btn-secondary">Ver Facturas</button>
                        `;
                    }

                 },
            ]
        });
    }

    if($('#form-pending-date').length > 0)
    {
        $('#form-pending-date').flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            locale: "es" // Esto lo pone en español
        });
    }

});

function verTickets(id)
{
    $("#display-tickets").load(site_url + "sc-admin/inc/ajax/load_tickets.php?id=" + id);
    $("#showtickets-modal").modal('show');
}

function verFacturas(id)
{
    $("#display-facturas").load(site_url + "sc-admin/inc/ajax/load_files.php?id=" + id);
    $("#showtfacturas-modal").modal('show');
}