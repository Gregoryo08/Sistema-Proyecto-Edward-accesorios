// /src/assets/js/ecommerce/estadosPedido.js
// Lógica de estados de pedidos (Gustavo)

const EstadoPedido = {
    // Mapeo de estados a colores
    colores: {
        'por aprobar': 'secondary',
        'pendiente pago': 'warning',
        'pago aprobado': 'success',
        'rechazado': 'danger',
        'enviado': 'info',
        'entregado': 'success'
    },
    
    // Mapeo de estados a íconos
    iconos: {
        'por aprobar': 'fa-clock',
        'pendiente pago': 'fa-exclamation-triangle',
        'pago aprobado': 'fa-check-circle',
        'rechazado': 'fa-times-circle',
        'enviado': 'fa-truck',
        'entregado': 'fa-check-double'
    },
    
    // Mensajes por estado
    mensajes: {
        'por aprobar': {
            titulo: 'Pedido en revisión',
            cuerpo: 'Tu pedido está siendo validado. Espera la confirmación.'
        },
        'pendiente pago': {
            titulo: 'Pago incompleto',
            cuerpo: 'El pago reportado no coincide con el monto o referencia.'
        },
        'pago aprobado': {
            titulo: '¡Pago aprobado!',
            cuerpo: 'Tu pedido está siendo preparado para despacho.'
        },
        'rechazado': {
            titulo: 'Pago Rechazado',
            cuerpo: 'No se especificó motivo.'
        },
        'enviado': {
            titulo: '¡Pedido en camino!',
            cuerpo: 'Tu pedido ya fue enviado.'
        },
        'entregado': {
            titulo: '¡Pedido entregado!',
            cuerpo: 'Gracias por comprar en Edward Accesorios.'
        }
    },
    
    getColor(estado) {
        return this.colores[estado] || 'secondary';
    },
    
    getIcono(estado) {
        return this.iconos[estado] || 'fa-info-circle';
    },
    
    getMensaje(estado, motivo = null) {
        const msg = this.mensajes[estado] || {
            titulo: 'Estado desconocido',
            cuerpo: 'Contacta con soporte.'
        };
        if (motivo && (estado === 'pendiente pago' || estado === 'rechazado')) {
            msg.cuerpo = motivo;
        }
        return msg;
    },
    
    mostrarBoton(estado) {
        return ['pendiente pago', 'rechazado'].includes(estado);
    }
};

// Exportar para usar en otros archivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EstadoPedido;
}