<?php
namespace App\Sistema\models;

use App\Sistema\config\Acceso;
use App\Sistema\config\Conexion;
use PDO;
use PDOException;

class ClienteEcommerce {
    private $conn;
    private $cedula_persona;
    private $nombre;
    private $apellido;
    private $correo;
    private $telefono;
    private $direccion;

    public function setCedulaPersona($valor) {
        $this->cedula_persona = $valor;
        return $this;
    }

    public function getCedulaPersona() {
        return $this->cedula_persona;
    }

    public function setNombre($valor) {
        $valor = trim($valor);
        if (empty($valor)) {
            throw new \InvalidArgumentException('Nombre inválido');
        }
        $this->nombre = $valor;
        return $this;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setApellido($valor) {
        $this->apellido = trim($valor);
        return $this;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function setCorreo($valor) {
        $valor = trim($valor);
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Correo inválido');
        }
        $this->correo = $valor;
        return $this;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function setTelefono($valor) {
        $valor = trim($valor);
        if (empty($valor)) {
            throw new \InvalidArgumentException('Teléfono inválido');
        }
        $this->telefono = $valor;
        return $this;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function setDireccion($valor) {
        $this->direccion = trim($valor);
        return $this;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function __construct() {
        $this->conn = Conexion::getShared('sistema')->getConexion();
    }

    public function captchaHabilitado() {
        return defined('RECAPTCHA_HABILITADO') && RECAPTCHA_HABILITADO === true;
    }

    public function verificarCaptcha($token) {
        if (!$this->captchaHabilitado()) {
            return true;
        }

        $token = trim((string) $token);

        if ($token === '') {
            return false;
        }

        if (!defined('RECAPTCHA_VERIFY_URL')) {
            define('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify');
        }

        $respuesta = @file_get_contents(
            RECAPTCHA_VERIFY_URL . '?' . http_build_query([
                'secret' => RECAPTCHA_SECRET_KEY,
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ])
        );

        $datos = json_decode((string) $respuesta, true);

        return isset($datos['success']) && $datos['success'] === true;
    }

    public function ejecutar($accion, $datos = []) {
        switch ($accion) {
            case 'registrar': return $this->_registrar($datos);
            case 'login': return $this->_login($datos['cedula'], $datos['password']);
            case 'verificarCedula': return $this->_verificarCedula($datos['cedula']);
            case 'estaLogueado': return $this->_estaLogueado();
            case 'obtenerPorCorreo': return $this->_obtenerPorCorreo($datos['email']);
            case 'obtenerPorCedula': return $this->_obtenerPorCedula($datos['cedula']);
            case 'verificarCredenciales': return $this->_verificarCredenciales($datos['cedula']);
            case 'logout': return $this->_logout();
            default: return ['error' => 'Acción no válida'];
        }
    }

    private function _estaLogueado() {
        return isset($_SESSION['es_ecommerce']) && $_SESSION['es_ecommerce'] === true
               && !empty($_SESSION['cliente_cedula']);
    }

    private function _obtenerPorCorreo($correo) {
        $stmt = $this->conn->prepare("SELECT cedula_persona, nombre, apellido, correo, telefono, direccion FROM persona WHERE correo = ?");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function _obtenerPorCedula($cedula) {
        $stmt = $this->conn->prepare("SELECT cedula_persona, nombre, apellido, correo, telefono, direccion FROM persona WHERE cedula_persona = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function _verificarCredenciales($cedula) {
        $connUser = Conexion::getShared('usuario')->getConexion();
        $stmt = $connUser->prepare("SELECT clave, estatus, id_rol FROM usuarios WHERE cedula_usuario = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function _iniciarSesionTienda($cedula, $nombre, $correo, $telefono, $rol) {
        $rol = (int) $rol;

        Acceso::registrarIngresoTienda($cedula, $nombre, $correo, $telefono, $rol);

        if ($rol !== Acceso::ROL_CLIENTE) {
            $_SESSION['username'] = $cedula;
            $_SESSION['nombre_completo'] = $nombre;
        }

        return [
            'cedula'   => $cedula,
            'nombre'   => $nombre,
            'correo'   => $correo,
            'telefono' => $telefono,
            'rol'      => $rol
        ];
    }

    private function _rolDeUsuario($cedula) {
        $connUser = Conexion::getShared('usuario')->getConexion();
        $stmt = $connUser->prepare("SELECT id_rol FROM usuarios WHERE cedula_usuario = ?");
        $stmt->execute([$cedula]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        return $fila ? (int) $fila['id_rol'] : 6;
    }

    private function _claveConfirmacion() {
        $semilla = __DIR__ . '|' . (defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : 'edward');

        return hash_hmac('sha256', 'confirmacion-registro', $semilla);
    }

    private function _emitirTokenConfirmacion($cedula, $persona) {
        $huella = hash_hmac(
            'sha256',
            $cedula . '|' . ($persona['correo'] ?? '') . '|' . ($persona['telefono'] ?? ''),
            $this->_claveConfirmacion()
        );

        return $huella;
    }

    private function _validarConfirmacion($cedula, $token, $persona) {
        if (!is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($this->_emitirTokenConfirmacion($cedula, $persona), $token);
    }

    private function _verificarCedula($cedula) {
        try {
            $connUser = Conexion::getShared('usuario')->getConexion();

            $stmt = $connUser->prepare("SELECT cedula_usuario, estatus FROM usuarios WHERE cedula_usuario = ?");
            $stmt->execute([$cedula]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                return [
                    'success'     => false,
                    'estado'      => 'tiene_usuario',
                    'estatus'     => $usuario['estatus'],
                    'needsReset'  => true,
                    'message'     => 'Esta cédula ya tiene una cuenta en el sistema. Inicia sesión o recupera tu contraseña.'
                ];
            }

            $stmtPersona = $this->conn->prepare("SELECT cedula_persona, nombre, apellido, correo, telefono, direccion FROM persona WHERE cedula_persona = ?");
            $stmtPersona->execute([$cedula]);
            $persona = $stmtPersona->fetch(PDO::FETCH_ASSOC);

            if ($persona) {
                $stmtEstado = $this->conn->prepare("SELECT estado FROM clientes WHERE cedula_persona = ?");
                $stmtEstado->execute([$cedula]);
                $filaCliente = $stmtEstado->fetch(PDO::FETCH_ASSOC);

                $esCliente = (bool) $filaCliente;

                return [
                    'success' => true,
                    'estado'  => $esCliente ? 'cliente_existente' : 'persona_existente',
                    'requiereConfirmacion' => true,
                    'tokenConfirmacion' => $this->_emitirTokenConfirmacion($cedula, $persona),
                    'message' => $esCliente
                        ? 'Esta cédula ya está registrada como cliente. Confirma que tus datos son correctos para crear tu acceso.'
                        : 'Encontramos datos con esta cédula. Confirma que la información es correcta para continuar.',
                    'datos'   => $persona
                ];
            }

            return [
                'success' => true,
                'estado'  => 'nueva',
                'requiereConfirmacion' => false,
                'message' => 'Cédula disponible. Completa el formulario para registrarte.'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al verificar la cédula.'];
        }
    }

    private function _registrar($datos) {
        try {
            $cedula = $datos['cedula_persona'];
            $clienteExistente = $this->_obtenerPorCedula($cedula);
            $connUser = Conexion::getShared('usuario')->getConexion();

            if ($clienteExistente) {

                if (!$this->_validarConfirmacion($cedula, $datos['token_confirmacion'] ?? '', $clienteExistente)) {
                    return [
                        'success' => false,
                        'estado'  => 'requiere_confirmacion',
                        'requiereConfirmacion' => true,
                        'message' => 'Debes revisar y confirmar los datos ya registrados con esta cédula antes de continuar.',
                        'datos'   => $clienteExistente
                    ];
                }

                $stmtU = $connUser->prepare("SELECT cedula_usuario FROM usuarios WHERE cedula_usuario = ?");
                $stmtU->execute([$cedula]);
                if ($stmtU->fetch(PDO::FETCH_ASSOC)) {
                    return ['success' => false, 'message' => 'Esta cédula ya está registrada como usuario. Inicia sesión o recupera tu contraseña.', 'needsReset' => true];
                }

                $stmtCorreo = $this->conn->prepare("SELECT cedula_persona FROM persona WHERE correo = ? AND cedula_persona <> ?");
                $stmtCorreo->execute([$datos['correo'], $cedula]);
                if ($stmtCorreo->fetch(PDO::FETCH_ASSOC)) {
                    return ['success' => false, 'message' => 'El correo ya está registrado a otra persona'];
                }

                $stmtUpd = $this->conn->prepare("UPDATE persona SET correo = ?, telefono = ?, direccion = ? WHERE cedula_persona = ?");
                $stmtUpd->execute([
                    $datos['correo'],
                    $datos['telefono'],
                    $datos['direccion'] ?? '',
                    $cedula
                ]);

                $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
                $connUser->beginTransaction();
                $stmt = $connUser->prepare("INSERT INTO usuarios (cedula_usuario, clave, estatus, id_rol) VALUES (?, ?, 'Activo', 6)");
                $stmt->execute([$cedula, $password_hash]);
                $connUser->commit();

                $nombre = $clienteExistente['nombre'];
                $apellido = $clienteExistente['apellido'] ?? '';
                $this->_iniciarSesionTienda(
                    $cedula,
                    $nombre . ' ' . $apellido,
                    $clienteExistente['correo'],
                    $clienteExistente['telefono'],
                    6
                );
                return ['success' => true, 'message' => 'Registro exitoso', 'existente' => true];
            }

            $stmtCorreo = $this->conn->prepare("SELECT cedula_persona FROM persona WHERE correo = ?");
            $stmtCorreo->execute([$datos['correo']]);
            if ($stmtCorreo->fetch(PDO::FETCH_ASSOC)) {
                return ['success' => false, 'message' => 'El correo ya está registrado'];
            }

            $this->conn->beginTransaction();
            $connUser->beginTransaction();
            $stmt = $this->conn->prepare("INSERT INTO persona (cedula_persona, nombre, apellido, correo, telefono, direccion, fecha_nacimiento, sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cedula, $datos['nombre'], $datos['apellido'] ?? '', $datos['correo'], $datos['telefono'], $datos['direccion'] ?? '', $datos['fecha_nacimiento'] ?? null, $datos['sexo'] ?? null]);
            $stmt = $this->conn->prepare("INSERT INTO clientes (cedula_persona, estado) VALUES (?, 'activo')");
            $stmt->execute([$cedula]);
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $stmt = $connUser->prepare("INSERT INTO usuarios (cedula_usuario, clave, estatus, id_rol) VALUES (?, ?, 'Activo', 6)");
            $stmt->execute([$cedula, $password_hash]);
            $this->conn->commit();
            $connUser->commit();
            $this->_iniciarSesionTienda(
                $cedula,
                $datos['nombre'] . ' ' . ($datos['apellido'] ?? ''),
                $datos['correo'],
                $datos['telefono'],
                6
            );
            return ['success' => true, 'message' => 'Registro exitoso'];
        } catch (PDOException $e) {
            if (isset($this->conn) && $this->conn->inTransaction()) $this->conn->rollBack();
            if (isset($connUser) && $connUser->inTransaction()) $connUser->rollBack();
            return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
        }
    }

    private function _login($cedula, $password) {
        $conn = Conexion::getShared('usuario');
        $candidatos = [$cedula];
        $normalizada = strtoupper(trim($cedula));
        if (preg_match('/^([VE])-?(\d{5,9})$/', $normalizada, $m)) {
            $candidatos[] = $m[1] . '-' . $m[2];
            $candidatos[] = $m[1] . $m[2];
        } elseif (ctype_digit($normalizada)) {
            $candidatos[] = "V-" . $normalizada;
            $candidatos[] = "E-" . $normalizada;
        }
        $candidatos = array_values(array_unique($candidatos));

        $usuarioData = null;
        foreach ($candidatos as $c) {
            $stmt = $conn->getConexion()->prepare("SELECT * FROM usuarios WHERE cedula_usuario = ?");
            $stmt->execute([$c]);
            $temp = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($temp && password_verify($password, $temp['clave'])) {
                $usuarioData = $temp;
                break;
            }
        }

        if ($usuarioData) {
            if ($usuarioData['estatus'] !== 'Activo') {
                return ['success' => false, 'message' => 'Cuenta inactiva, contacte al administrador'];
            }
            if ((int) $usuarioData['id_rol'] !== Acceso::ROL_CLIENTE
                && Acceso::dominioActual() === Acceso::DOMINIO_TIENDA) {
                return ['success' => false, 'message' => 'Esta cuenta es del personal del sistema. Inicia sesión en el portal de intranet.'];
            }
            $cliente = $this->_obtenerPorCedula($usuarioData['cedula_usuario']);
            $nombreSesion = $cliente ? ($cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '')) : 'Usuario';
            $sesion = $this->_iniciarSesionTienda(
                $usuarioData['cedula_usuario'],
                $nombreSesion,
                $cliente['correo'] ?? '',
                $cliente['telefono'] ?? '',
                $usuarioData['id_rol']
            );
            return ['success' => true, 'message' => 'Login exitoso', 'cliente' => $sesion];
        }

        foreach ($candidatos as $c) {
            $personaExistente = $this->_obtenerPorCedula($c);
            if ($personaExistente) {

                if ($this->_verificarCredenciales($c)) {
                    return [
                        'success'   => false,
                        'message'   => 'Credenciales incorrectas. Si las olvidaste, usa la opción "¿Olvidaste tu contraseña?".',
                    ];
                }
                return [
                    'success'      => false,
                    'needsRegister' => true,
                    'message'      => 'Esta cédula tiene datos registrados pero aún no tiene cuenta en la tienda.',
                    'cliente'      => $personaExistente
                ];
            }
        }

        $cliente = $this->_obtenerPorCorreo($cedula);
        if (!$cliente) return ['success' => false, 'message' => 'Usuario o cédula no registrados'];

        $credenciales = $this->_verificarCredenciales($cliente['cedula_persona']);
        if (!$credenciales || !password_verify($password, $credenciales['clave']) || $credenciales['estatus'] !== 'Activo') {
            return ['success' => false, 'message' => 'Credenciales incorrectas o cuenta inactiva'];
        }

        $sesion = $this->_iniciarSesionTienda(
            $cliente['cedula_persona'],
            $cliente['nombre'] . ' ' . ($cliente['apellido'] ?? ''),
            $cliente['correo'],
            $cliente['telefono'],
            $this->_rolDeUsuario($cliente['cedula_persona'])
        );
        return ['success' => true, 'message' => 'Login exitoso', 'cliente' => $sesion];
    }

    private function _logout() {
        $_SESSION = [];
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada'];
    }
}
