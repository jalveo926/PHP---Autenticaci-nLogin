<?php 
require_once '../../vendor/autoload.php';
require_once '../config/databaseConfig.php';
require_once 'Database.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

class Autenticador2FA
{
    private $googleAuthenticator;
    private $db;
    private $idUsuario;

    public function __construct($pdo, $idUsuario)
    {
        $this->googleAuthenticator = new GoogleAuthenticator();
        $this->db = new Database($pdo);
        $this->idUsuario = $idUsuario;
    }

    /**
     * Genera un secret para 2FA
     * @return string - El secret generado
     */
    public function generarSecret()
    {
        return $this->googleAuthenticator->generateSecret();
    }

    /**
     * Guarda el secret en la base de datos
     * @param string $secret - El secret a guardar
     * @return bool - true si se guardó correctamente
     */
    public function guardarSecret($secret)
    {
        try {
            $filasAfectadas = $this->db->guardarSecret2FA($this->idUsuario, $secret);
            return $filasAfectadas > 0;
        } catch (Exception $e) {
            error_log("Error al guardar secret: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la URL del código QR
     * @param string $secret - El secret
     * @param string $usuario - Nombre del usuario
     * @param string $issuer - Nombre de la aplicación/compañía
     * @return string - URL del código QR
     */
    public function obtenerUrlQR($secret, $usuario, $issuer = 'MiAplicacion')
    {
        return GoogleQrUrl::generate($usuario, $secret, $issuer);
    }

    /**
     * Verifica si un código TOTP es válido
     * @param string $codigo - El código a verificar
     * @param string $secret - El secret del usuario
     * @return bool - true si el código es válido
     */
    public function verificarCodigo($codigo, $secret)
    {
        return $this->googleAuthenticator->checkCode($secret, $codigo);
    }
}

?>