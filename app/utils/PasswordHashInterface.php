<?php

interface PasswordHashInterface
{
    /**
     * Genera un hash seguro de la contraseña
     * @param string $password - La contraseña en texto plano
     * @return string - El hash de la contraseña
     */
    public function hash($password);

    /**
     * Verifica que una contraseña coincida con su hash
     * @param string $password - La contraseña en texto plano
     * @param string $hash - El hash almacenado
     * @return bool - true si coinciden, false si no
     */
    public function verify($password, $hash);

    /**
     * Verifica si un hash necesita ser rehashed (por cambios de algoritmo)
     * @param string $hash - El hash a verificar
     * @return bool - true si necesita rehashing
     */
    public function needsRehash($hash);
}
