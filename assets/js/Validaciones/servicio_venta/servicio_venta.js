$(document).ready(function () {
    $('#tablaServicioVenta').DataTable({
        destroy: true,
        ajax: {
            url: '?pagina=servicio_venta&ajax=true&accion=listarServicioVenta',
            dataSrc: ''
        },
        columns: [
            {
                data: 'fecha',
                render: function (data) {
                    return data ? new Date(data).toLocaleString() : '-';
                }
            },
            { data: 'direccion' },
            { data: 'metodo_pago' },
            { data: 'referencia_pago' },
            {
                data: 'total',
                render: function (data) {
                    return `<b>$${parseFloat(data || 0).toFixed(2)}</b>`;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 8,
        columnDefs: [{ className: 'dt-head-center', targets: '_all' }],
       language: {
    search: "Buscar:",
    lengthMenu: "Mostrar _MENU_ registros por página",
    info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
    infoEmpty: "Mostrando 0 a 0 de 0 registros",
    zeroRecords: "No se encontraron resultados",
    emptyTable: "No hay datos disponibles en la tabla",
    paginate: { 
        first: "Primero", 
        last: "Último", 
        next: "Siguiente", 
        previous: "Anterior" 
    }
}
    });
});
