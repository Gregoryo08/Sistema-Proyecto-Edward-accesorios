<?php
namespace App\Sistema\models;

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

    public function ejecutar($accion, $datos = []) {
        switch ($accion) {
            case 'registrar': return $this->_registrar($datos);
            case 'login': return $this->_login($datos['cedula'], $datos['password']);
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
        $stmt = $connUser->prepare("SELECT clave, estatus FROM usuarios WHERE cedula_usuario = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function _registrar($datos) {
        try {
            $cedula = $datos['cedula_persona'];
            $clienteExistente = $this->_obtenerPorCedula($cedula);
            $connUser = Conexion::getShared('usuario')->getConexion();

            if ($clienteExistente) {
                // La persona ya existe en clientes (pudo haber comprado en ventas/financiamiento)
                // 1. Verificar si ya posee usuario en el sistema
                $stmtU = $connUser->prepare("SELECT cedula_usuario FROM usuarios WHERE cedula_usuario = ?");
                $stmtU->execute([$cedula]);
                if ($stmtU->fetch(PDO::FETCH_ASSOC)) {
                    return ['success' => false, 'message' => 'Esta cédula ya está registrada como usuario. Inicia sesión.'];
                }

                // 2. Validar que el correo no pertenezca a otra persona distinta
                $stmtCorreo = $this->conn->prepare("SELECT cedula_persona FROM persona WHERE correo = ? AND cedula_persona <> ?");
                $stmtCorreo->execute([$datos['correo'], $cedula]);
                if ($stmtCorreo->fetch(PDO::FETCH_ASSOC)) {
                    return ['success' => false, 'message' => 'El correo ya está registrado a otra persona'];
                }

                // 3. Actualizar datos de contacto del cliente existente (opcional/útil)
                $stmtUpd = $this->conn->prepare("UPDATE persona SET correo = ?, telefono = ? WHERE cedula_persona = ?");
                $stmtUpd->execute([$datos['correo'], $datos['telefono'], $cedula]);

                // 4. Solo crear el usuario (rol Cliente = 6)
                $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
                $connUser->beginTransaction();
                $stmt = $connUser->prepare("INSERT INTO usuarios (cedula_usuario, clave, estatus, id_rol) VALUES (?, ?, 'Activo', 6)");
                $stmt->execute([$cedula, $password_hash]);
                $connUser->commit();

                $nombre = $clienteExistente['nombre'];
                $apellido = $clienteExistente['apellido'] ?? '';
                $_SESSION['es_ecommerce'] = true;
                $_SESSION['cliente_cedula'] = $cedula;
                $_SESSION['cliente_nombre'] = $nombre . ' ' . $apellido;
                $_SESSION['cliente_correo'] = $clienteExistente['correo'];
                $_SESSION['cliente_telefono'] = $clienteExistente['telefono'];
                return ['success' => true, 'message' => 'Registro exitoso', 'existente' => true];
            }

            // La persona NO existe → flujo normal: persona + clientes + usuario
            $stmtCorreo = $this->conn->prepare("SELECT cedula_persona FROM persona WHERE correo = ?");
            $stmtCorreo->execute([$datos['correo']]);
            if ($stmtCorreo->fetch(PDO::FETCH_ASSOC)) {
                return ['success' => false, 'message' => 'El correo ya está registrado'];
            }

            $this->conn->beginTransaction();
            $connUser->beginTransaction();
            $stmt = $this->conn->prepare("INSERT INTO persona (cedula_persona, nombre, apellido, correo, telefono, direccion, fecha_nacimiento, sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cedula, $datos['nombre'], $datos['apellido'] ?? '', $datos['correo'], $datos['telefono'], $datos['direccion'] ?? '', $datos['fecha_nacimiento'] ?? null, $datos['sexo'] ?? null]);
            $stmt = $this->conn->prepare("INSERT INTO clientes (cedula_persona, residencia, estado) VALUES (?, ?, 'activo')");
            $stmt->execute([$cedula, $datos['direccion'] ?? 'No especificado']);
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $stmt = $connUser->prepare("INSERT INTO usuarios (cedula_usuario, clave, estatus, id_rol) VALUES (?, ?, 'Activo', 6)");
            $stmt->execute([$cedula, $password_hash]);
            $this->conn->commit();
            $connUser->commit();
            $_SESSION['es_ecommerce'] = true;
            $_SESSION['cliente_cedula'] = $cedula;
            $_SESSION['cliente_nombre'] = $datos['nombre'] . ' ' . ($datos['apellido'] ?? '');
            $_SESSION['cliente_correo'] = $datos['correo'];
            $_SESSION['cliente_telefono'] = $datos['telefono'];
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
            $_SESSION['es_ecommerce'] = true;
            $_SESSION['cliente_cedula'] = $usuarioData['cedula_usuario'];
            $cliente = $this->_obtenerPorCedula($usuarioData['cedula_usuario']);
            $_SESSION['cliente_nombre'] = $cliente ? ($cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '')) : 'Usuario';
            $_SESSION['cliente_correo'] = $cliente['correo'] ?? '';
            $_SESSION['cliente_telefono'] = $cliente['telefono'] ?? '';
            return ['success' => true, 'message' => 'Login exitoso', 'cliente' => ['cedula' => $usuarioData['cedula_usuario'], 'nombre' => $_SESSION['cliente_nombre'], 'correo' => $_SESSION['cliente_correo'], 'telefono' => $_SESSION['cliente_telefono']]];
        }

        $cliente = $this->_obtenerPorCorreo($cedula);
        if (!$cliente) return ['success' => false, 'message' => 'Usuario o cédula no registrados'];

        $credenciales = $this->_verificarCredenciales($cliente['cedula_persona']);
        if (!$credenciales || !password_verify($password, $credenciales['clave']) || $credenciales['estatus'] !== 'Activo') {
            return ['success' => false, 'message' => 'Credenciales incorrectas o cuenta inactiva'];
        }

        $_SESSION['es_ecommerce'] = true;
        $_SESSION['cliente_cedula'] = $cliente['cedula_persona'];
        $_SESSION['cliente_nombre'] = $cliente['nombre'] . ' ' . ($cliente['apellido'] ?? '');
        $_SESSION['cliente_correo'] = $cliente['correo'];
        $_SESSION['cliente_telefono'] = $cliente['telefono'];
        return ['success' => true, 'message' => 'Login exitoso', 'cliente' => ['cedula' => $cliente['cedula_persona'], 'nombre' => $_SESSION['cliente_nombre'], 'correo' => $_SESSION['cliente_correo'], 'telefono' => $_SESSION['cliente_telefono']]];
    }

    private function _logout() {
        $_SESSION = [];
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada'];
    }
}