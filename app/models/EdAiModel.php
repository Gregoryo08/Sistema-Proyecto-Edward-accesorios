<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;

/**
 * ED-AI: asistente virtual basado en base de conocimientos (FAQ/reglas) local.
 * - responde de forma determinista según contexto (intranet o ecommerce)
 * - controla el numero maximo de usuarios de chat simultaneos por contexto
 */
class EdAiModel
{
    const MAX_SIMULTANEOS = 50;
    const EXPIRA_MIN = 5; // minutos sin actividad para liberar el "slot"

    private $conexionUsuario;

    public function __construct()
    {
        // Conexión a BD usuario (sistema_edward_usuario) para el control de sesiones activas.
        try {
            $this->conexionUsuario = Conexion::getShared('usuario')->getConexion();
        } catch (\Exception $e) {
            $this->conexionUsuario = null;
        }
    }

    /**
     * Base de conocimiento (intranet). Cada regla: [claves => respuesta].
     * Las claves se alinean con los NOMBRES DE MODULOS del menu lateral para
     * que el alcance sea coherente. Como el puntaje es la suma de longitudes
     * de las claves coincidentes, los nombres de modulo mas largos/especificos
     * (ej: "servicio tecnico", "ventas online", "pago de cuotas") ganan sobre
     * los genericos ("ventas", "financiamiento").
     */
    private function conocimientoIntranet(): array
    {
        return [
            [["servicio tecnico", "servicio", "reparacion", "equipo dañado", "mantenimiento de equipo", "diagnostico", "cambio de pantalla", "cambio de bateria", "orden de servicio", "chequeo_orden"], "El modulo 'Servicio Tecnico' gestiona el ingreso y reparacion de equipos. Registras una orden de servicio con el equipo del cliente, el diagnostico, repuestos y el costo. Llevas el seguimiento del estado (recibido, en reparacion, listo, entregado) y puedes emitir la orden/chequeo. Desde ahi tambien se controla el inventario de repuestos usados."],
            [["ventas online", "venta online", "pedidos online", "aprobar pago", "despachar", "en transito", "por aprobar", "por despachar", "aprobacion de pago", "verificar pago online"], "En 'Ventas Online' puedes ver cuantas ventas se han hecho y su estado: cuantas estan por aprobar, por despachar y cuales estan en transito. Para aprobar una venta asegurate de que la referencia del pago coincida con los pagos abonados a la cuenta que el cliente determino abonar."],
            [["pago de cuotas", "pagar cuota", "cuota", "cuotas", "abono", "cliente financiamiento", "solicitud de pago"], "En 'Pago De Cuotas' (dentro de Financiamiento) registras los abonos/pagos de las cuotas de los equipos financiados. Puedes registrar un pago de cuota, ver el historial del cliente financiamiento y anular o aprobar solicitudes de pago."],
            [["entrada de productos", "entrada", "entradas", "ingreso de productos", "recibir mercancia", "reposicion", "actualizar stock"], "En 'Entrada De Productos' (dentro de Inventario) registras la mercancia que entra al almacen, actualizando el stock de los productos. Indicar producto, cantidad, proveedor y costo; el sistema incrementa la existencia."],
            [["reporte de ventas", "reporte ventas", "balance de ventas", "reportes ventas", "ventas del mes", "total de ventas"], "En 'Reporte Ventas' (dentro de Reportes) consultas las estadisticas y totales de ventas por periodo, con filtros y exportacion a PDF."],
            [["mi perfil", "mis datos", "perfil", "cambiar mi clave", "mi contraseña", "actualizar mis datos"], "En 'Mi Perfil' o 'Mis Datos' puedes ver y actualizar tu informacion personal, y cambiar tu contraseña. Tambien accedes a tus Notificaciones."],
            [["administrar modulos", "modulos", "permisos de modulos", "permisos"], "En 'Administrar Modulos' gestionas los modulos del sistema y sus permisos (listar, registrar, modificar, eliminar, control_total) que luego se asignan a los roles."],
            [["financiamiento", "financiar", "financiado", "credito", "plan de cuotas", "equipo financiado", "inicial"], "El modulo 'Financiamiento' gestiona los planes de equipos a cuotas. Registras el financiamiento del cliente, defines la inicial y las cuotas; luego desde 'Pago De Cuotas' se registran los abonos. Puedes consultar historial, moras y aprobar/anular solicitudes."],
            [["backup base de datos", "backup", "restaurar base de datos", "restaurar", "respaldar", "copia de seguridad"], "En 'Mantenimiento' puedes hacer un Backup de la Base De Datos (guardar copia) y Restaurar una copia guardada. Usalo con cuidado: restaurar sobrescribe la informacion actual."],
            [["reporte financiamiento", "reporte financiamiento", "reportes de financiamiento", "balance financiamiento"], "En 'Reporte Financiamiento' (dentro de Reportes) consultas los movimientos y totales de los financiamientos, con exportacion a PDF."],
            [["marcas", "marca", "categoria", "categorias", "especialidad", "cargos", "metodo de pago", "metodos pago", "repertorio", "administrar bancos", "repertorio"], "En el grupo 'Repertorio' administras los catalogos base del sistema: Metodos Pago, Bancos, Especialidad, Marcas, Categoria y Cargos. Son los datos maestros que se usan en ventas, financiamiento y servicios."],
            [["empleados", "empleado", "personal", "agregar empleado", "turnos", "turno", "horario", "jornada"], "En 'Personal' gestionas los Empleados (registrar, modificar, inactivar) y sus Turnos/horarios de trabajo."],
            [["clientes", "cliente", "persona", "registrar cliente", "nuevo cliente", "perfil financiero del cliente"], "En el modulo 'Clientes' registras, modificas o consultas clientes, y gestionas su perfil financiero. La cedula se valida (formato V-/E-). Tambien puedes ver clientes inactivos."],
            [["usuarios", "usuario", "roles", "rol", "crear usuario", "gestionar accesos", "seguridad"], "En 'Seguridad' gestionas los Usuarios, los Roles y sus permisos por modulo, y consultas la Bitacora. Los roles definen que puede hacer cada usuario."],
            [["producto", "productos", "inventario", "stock", "existencia"], "En el modulo 'Productos' (dentro de Inventario) puedes registrar, modificar o eliminar productos, y gestionar su imagen y stock (minimo/maximo/actual). Para el catalogo del e-commerce asegurate de que el producto tenga imagen y estado activo."],
            [["proveedores", "proveedor", "proveedor de productos"], "En 'Proveedores' administras quienes suministran los productos. Registras sus datos y se usan en las Entradas De Productos."],
            [["bancos", "banco", "cuenta bancaria", "cuenta del banco"], "En el modulo 'Bancos' (Repertorio) administras las cuentas donde se reciben los pagos. Se usan al registrar metodos de pago y al verificar pagos online."],
            [["bitacora", "auditoria", "movimientos", "log", "historial de acciones"], "La 'Bitacora' (Seguridad) registra cada accion importante del sistema: quien, cuando y que hizo, con los valores antiguo y nuevo. Se consulta para auditoria."],
            [["reporte", "reportes", "reporte", "estadisticas", "indicadores"], "En el grupo 'Reportes' tienes Reporte Ventas, Reporte Financiamiento y Reporte de Servicio Tecnico. Consulta balances y exporta a PDF."],
            [["notificaciones", "notificacion", "avisos", "campana"], "En 'Notificaciones' (Mi Perfil) ves los avisos del sistema, por ejemplo alertas de stock bajo o eventos que requieren tu atencion."],
            [["tasa", "dolar", "bcv", "cambio", "tasa de cambio", "dolar hoy"], "La tasa de cambio (USD a Bs) se gestiona manualmente. Si el indicador del header (simbolo $) esta en rojo significa que esta DESACTUALIZADA: pulsa el simbolo y sigue el flujo (clave de administrador + valor de la tasa del dia). La actualizacion queda registrada en la bitacora."],
            [["ventas", "venta", "factura", "facturar", "cajera", "caja"], "Para hacer una venta ve al modulo Ventas (menu principal). Registras el/los productos, seleccionas el cliente o lo registras como invitado, indicas el metodo de pago y completas el pago. Si el pago requiere verificacion (transferencia/movil), la cajera lo verifica y luego se descuenta el stock."],
            [["hola", "buenas", "saludo", "hey", "que tal", "saludos"], "¡Hola! Soy Ed-AI, el asistente del sistema Edward Accesorios. Preguntame por cualquiera de los modulos del menu: Ventas, Ventas Online, Productos, Financiamiento, Pago De Cuotas, Servicio Tecnico, Clientes, Empleados, Usuarios, Repertorio, Reportes, etc."],
            [["ayuda", "como", "que es", "manual", "ayudarme", "funciona", "usar", "paso"], "Puedo explicarte como se usa cada modulo del menu lateral. Dime cual (Ventas, Ventas Online, Productos, Financiamiento, Pago De Cuotas, Servicio Tecnico, Entrada De Productos, Bancos, etc.) y te explico el procedimiento."],
            [["gracias", "perfecto", "genial", "ok", "entendido"], "¡De nada! Cualquier otra duda que tengas aqui estoy para ayudarte. Puedes escribir 'ayuda' para ver lo que se."],
            [["salir", "chao", "adios", "nos vemos"], "¡Hasta luego! Recuerda cerrar tu sesion cuando termines de usar el sistema por seguridad."],
        ];
    }

    /**
     * Base de conocimiento (e-commerce / portal externo).
     */
    private function conocimientoEcommerce(): array
    {
        return [
            [["pedido", "pedidos", "mis pedidos", "seguimiento", "donde esta"], "Tus pedidos los puedes revisar en la seccion 'Mis Pedidos'. Ahi ves cada pedido con su estado: pendiente, aprobado, en despacho o entregado. Si tienes dudas sobre un pedido especifico, usa la opcion de verificacion del pedido."],
            [["pago", "pagar", "ya pague", "transferencia", "pago movil", "voucher", "comprobante", "reportar pago"], "Despues de hacer tu pedido, debes reportar el pago. Elegiste un metodo (transferencia o pago movil); en la pagina de datos de pago veras los datos del banco y el monto exacto en Bs. segun la tasa del dia. Cuando hagas el pago pulsas 'YA PAGUE' y adjuntas el comprobante. La cajera verificara tu pago y aprobara el pedido."],
            [["envio", "envio", "despacho", "entrega", "direccion", "llegada"], "Al confirmar tu pedido registras tu direccion de envio. Cuando el pago sea verificado, tu pedido pasa a 'en despacho' y llegara a la direccion indicada. Puedes ver el estado actualizado en 'Mis Pedidos'."],
            [["precio", "precio", "costo", "cuanto cuesta", "usd", "dolares", "bs", "bolivares"], "Los precios de los productos se muestran en USD. Al momento de pagar, el monto en bolivares se calcula con la tasa del dia (USD a Bs.), que podras confirmar en la pagina de datos de pago."],
            [["producto", "catalogo", "comprar", "agregar carrito", "carrito", "que venden"], "En el Catalogo puedes ver los productos disponibles, agregarlos al carrito y hacer tu pedido. Cada producto tiene su precio y disponibilidad."],
            [["financiamiento", "financiado", "cuotas", "plan de pago"], "Ofrecemos equipos financiados a cuotas con una inicial. Si te interesa, consulta en la tienda fisica o via WhatsApp, ya que el financiamiento se gestiona directamente con el personal."],
            [["registro", "registrarme", "crear cuenta", "cuenta", "iniciar sesion", "login"], "Para comprar necesitas una cuenta. Usa 'Iniciar Sesion' o el registro en el portal. Registra tus datos y podras hacer pedidos y ver su estado."],
            [["tasa", "dolar", "bcv", "tipo de cambio"], "El tipo de cambio (USD a Bs.) se actualiza manualmente por el personal. En los datos de pago veras el monto exacto en Bs. segun la tasa vigente."],
            [["soporte", "tecnico", "reparacion", "servicio", "whatsapp", "contacto", "donde estan", "ubicacion"], "Estamos en el C.C. COSMOS, Mesanina Local N°8, Carrera 21 entre Calle 25 y 22, Barquisimeto, Lara. Para soporte tecnico o consultas puedes contactarnos por WhatsApp o visitarnos en la tienda."],
            [["hola", "buenas", "saludo", "hey", "que tal"], "¡Hola! Soy Ed-AI, el asistente de la tienda Edward Accesorios. Preguntame sobre pedidos, pagos, envios, catalogo o la tasa de cambio."],
            [["gracias", "perfecto", "genial", "ok", "entendido"], "¡De nada! Si necesitas algo mas sobre tu pedido o compra, aqui estoy."],
            [["ayuda", "como", "que es", "ayudarme", "funciona"], "Preguntame sobre tu pedido, como pagar, envios, el catalogo o la tasa de cambio y te ayudo al instante."],
        ];
    }

    /**
     * Responde una pregunta segun el contexto. Devuelve string.
     */
    public function responder(string $pregunta, string $contexto = 'intranet'): string
    {
        $contexto = ($contexto === 'ecommerce') ? 'ecommerce' : 'intranet';
        $reglas = ($contexto === 'ecommerce') ? $this->conocimientoEcommerce() : $this->conocimientoIntranet();

        $pregunta = mb_strtolower(trim($pregunta));

        if ($pregunta === '') {
            return "Escribe tu duda o pregunta para poder ayudarte.";
        }

        $mejor = null;
        $mejorScore = 0;

        foreach ($reglas as [$claves, $respuesta]) {
            $score = 0;
            foreach ($claves as $clave) {
                if (mb_strpos($pregunta, mb_strtolower($clave)) !== false) {
                    // ponderar: claves mas largas pesan mas (mas especificas)
                    $score += mb_strlen($clave);
                }
            }
            if ($score > $mejorScore) {
                $mejorScore = $score;
                $mejor = $respuesta;
            }
        }

        if ($mejor && $mejorScore > 0) {
            return $mejor;
        }

        return "No estoy seguro de entender tu pregunta. Prueba con palabras como: '" .
            (($contexto === 'ecommerce')
                ? "pedido, pago, envio, catalogo, tasa' para ayudarte mejor."
                : "venta, producto, financiamiento, cliente, usuario, reporte, tasa, ayuda'.") .
            " Tambien puedes escribir 'ayuda'.";
    }

    // ============================================================
    //  CONTROL DE USUARIOS SIMULTANEOS
    // ============================================================

    private function _limpiarCaducados(): void
    {
        if (!$this->conexionUsuario) return;
        try {
            $this->conexionUsuario->exec(
                "DELETE FROM chat_edai_activos WHERE ultimo_ping < (NOW() - INTERVAL " . (int) self::EXPIRA_MIN . " MINUTE)"
            );
        } catch (\Exception $e) {
        }
    }

    /**
     * Registra/renueva el "slot" de chat del usuario actual.
     * @param string $token identificar unico del cliente/instancia
     * @param string $contexto intranet|ecommerce
     * @return array ['ok'=>bool, 'lleno'=>bool, 'activos'=>int]
     */
    public function registrarActivo(string $token, string $usuarioId, string $contexto = 'intranet'): array
    {
        $contexto = ($contexto === 'ecommerce') ? 'ecommerce' : 'intranet';
        $this->_limpiarCaducados();

        if (!$this->conexionUsuario) {
            // Si no hay BD, no bloquear al usuario (fallo abierto).
            return ['ok' => true, 'lleno' => false, 'activos' => 1];
        }

        $token = substr(preg_replace('/[^A-Za-z0-9\-_,]/', '', $token), 0, 64);
        $usuarioId = substr($usuarioId ?? '', 0, 20);

        try {
            // Ya tiene slot -> renovar
            $stmt = $this->conexionUsuario->prepare(
                "UPDATE chat_edai_activos SET ultimo_ping = NOW(), usuario_id = ? WHERE token = ? AND contexto = ?"
            );
            $stmt->execute([$usuarioId, $token, $contexto]);
            if ($stmt->rowCount() > 0) {
                return ['ok' => true, 'lleno' => false, 'activos' => $this->_contarActivos($contexto)];
            }

            // Contar activos actuales
            $activos = $this->_contarActivos($contexto);
            if ($activos >= self::MAX_SIMULTANEOS) {
                return ['ok' => false, 'lleno' => true, 'activos' => $activos];
            }

            // Nuevo slot
            $ins = $this->conexionUsuario->prepare(
                "INSERT IGNORE INTO chat_edai_activos (token, contexto, usuario_id) VALUES (?, ?, ?)"
            );
            $ins->execute([$token, $contexto, $usuarioId]);

            return ['ok' => true, 'lleno' => false, 'activos' => $this->_contarActivos($contexto)];
        } catch (\Exception $e) {
            return ['ok' => true, 'lleno' => false, 'activos' => $this->_contarActivos($contexto)];
        }
    }

    /**
     * Libera el slot al cerrar el chat.
     */
    public function liberarActivo(string $token, string $contexto = 'intranet'): void
    {
        if (!$this->conexionUsuario) return;
        $contexto = ($contexto === 'ecommerce') ? 'ecommerce' : 'intranet';
        try {
            $stmt = $this->conexionUsuario->prepare(
                "DELETE FROM chat_edai_activos WHERE token = ? AND contexto = ?"
            );
            $stmt->execute([$token, $contexto]);
        } catch (\Exception $e) {
        }
    }

    private function _contarActivos(string $contexto): int
    {
        try {
            $stmt = $this->conexionUsuario->prepare(
                "SELECT COUNT(*) FROM chat_edai_activos WHERE contexto = ?"
            );
            $stmt->execute([$contexto]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
