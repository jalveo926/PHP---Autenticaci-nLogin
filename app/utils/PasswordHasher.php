<?php

require_once 'PasswordHashInterface.php';

class PasswordHasher implements PasswordHashInterface
{
    private $algorithm;
    private $options;

    /**
     * Constructor
     * @param string $algorithm - Algoritmo a usar (default: PASSWORD_BCRYPT)
     * @param array $options - Opciones del algoritmo
     */
    public function __construct($algorithm = PASSWORD_BCRYPT, $options = [])
    {
        $this->algorithm = $algorithm;
        
        // Opciones por defecto para BCRYPT
        if ($algorithm === PASSWORD_BCRYPT && empty($options)) {
            $this->options = ['cost' => 12];
        } else {
            $this->options = $options;
        }
    }

    /**
     * Genera un hash seguro de la contraseña
     * @param string $password - La contraseña en texto plano
     * @return string - El hash de la contraseña
     * @throws Exception Si falla el hashing
     */
    public function hash($password)
    {
        if (empty($password)) {
            throw new Exception("La contraseña no puede estar vacía");
        }

        if (strlen($password) > 72) {
            throw new Exception("La contraseña no puede exceder 72 caracteres");
        }

        $hash = password_hash($password, $this->algorithm, $this->options);

        if ($hash === false) {
            throw new Exception("Error al generar el hash de la contraseña");
        }

        return $hash;
    }

    /**
     * Verifica que una contraseña coincida con su hash
     * @param string $password - La contraseña en texto plano
     * @param string $hash - El hash almacenado
     * @return bool - true si coinciden, false si no
     */
    public function verify($password, $hash)
    {
        if (empty($password) || empty($hash)) {
            return false;
        }

        return password_verify($password, $hash);
    }

    /**
     * Verifica si un hash necesita ser rehashed
     * @param string $hash - El hash a verificar
     * @return bool - true si necesita rehashing
     */
    public function needsRehash($hash)
    {
        if (empty($hash)) {
            return true;
        }

        return password_needs_rehash($hash, $this->algorithm, $this->options);
    }
}
