<?php

class Sanitizer
{
    /**
     * Sanitiza una cadena de texto (trim y espacios múltiples)
     * @param string $valor - El valor a sanitizar
     * @return string - El valor sanitizado
     */
    public static function sanitizarTexto($valor)
    {
        if (is_null($valor)) {
            return '';
        }

        $valor = trim($valor);
        $valor = preg_replace('/\s+/', ' ', $valor);
        
        return $valor;
    }

    /**
     * Sanitiza un email
     * @param string $email - El email a sanitizar
     * @return string - El email sanitizado
     */
    public static function sanitizarEmail($email)
    {
        $email = self::sanitizarTexto($email);
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Valida que un email sea válido
     * @param string $email - El email a validar
     * @return bool - true si es válido
     */
    public static function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
    * Valida que una contraseña cumpla los requisitos mínimos
    * @param string $password - La contraseña a validar
    * @param int $minLength - Longitud mínima (default: 8)
     * @return array - ['valido' => bool, 'errores' => array]
     */
    public static function validarPassword($password, $minLength = 8)
    {
        $errores = [];

        if (empty($password)) {
            $errores[] = "La contraseña es requerida";
            return ['valido' => false, 'errores' => $errores];
        }

        if (strlen($password) < $minLength) {
            $errores[] = "La contraseña debe tener al menos $minLength caracteres";
        }

        if (strlen($password) > 72) {
            $errores[] = "La contraseña no puede exceder 72 caracteres";
        }

        // Opcional: validar complejidad
        // if (!preg_match('/[A-Z]/', $password)) {
        //     $errores[] = "La contraseña debe contener al menos una mayúscula";
        // }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }

    /**
     * Valida que dos contraseñas coincidan
     * @param string $password - Primera contraseña
     * @param string $passwordConfirm - Segunda contraseña
     * @return bool - true si coinciden
     */
    public static function validarPasswordCoinciden($password, $passwordConfirm)
    {
        return $password === $passwordConfirm;
    }

    /**
     * Valida que un nombre de usuario sea válido
     * @param string $usuario - El usuario a validar
     * @param int $minLength - Longitud mínima (default: 3)
     * @param int $maxLength - Longitud máxima (default: 20)
     * @return array - ['valido' => bool, 'errores' => array]
     */
    public static function validarUsuario($usuario, $minLength = 3, $maxLength = 20)
    {
        $errores = [];

        if (empty($usuario)) {
            $errores[] = "El usuario es requerido";
            return ['valido' => false, 'errores' => $errores];
        }

        $usuario = self::sanitizarTexto($usuario);

        if (strlen($usuario) < $minLength) {
            $errores[] = "El usuario debe tener al menos $minLength caracteres";
        }

        if (strlen($usuario) > $maxLength) {
            $errores[] = "El usuario no puede exceder $maxLength caracteres";
        }

        // Solo caracteres alfanuméricos y guiones
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $usuario)) {
            $errores[] = "El usuario solo puede contener letras, números, guiones y guiones bajos";
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }

    /**
     * Valida que un nombre sea válido
     * @param string $nombre - El nombre a validar
     * @param int $minLength - Longitud mínima (default: 2)
     * @return array - ['valido' => bool, 'errores' => array]
     */
    public static function validarNombre($nombre, $minLength = 2)
    {
        $errores = [];

        if (empty($nombre)) {
            $errores[] = "El nombre es requerido";
            return ['valido' => false, 'errores' => $errores];
        }

        $nombre = self::sanitizarTexto($nombre);

        if (strlen($nombre) < $minLength) {
            $errores[] = "El nombre debe tener al menos $minLength caracteres";
        }

        if (strlen($nombre) > 100) {
            $errores[] = "El nombre no puede exceder 100 caracteres";
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }

    /**
     * Valida que un valor no esté vacío
     * @param string $valor - El valor a validar
     * @param string $nombre - Nombre del campo (para mensaje de error)
     * @return array - ['valido' => bool, 'error' => string]
     */
    public static function validarRequerido($valor, $nombre = 'Campo')
    {
        if (empty($valor)) {
            return [
                'valido' => false,
                'error' => "$nombre es requerido"
            ];
        }

        return ['valido' => true, 'error' => ''];
    }

    /**
     * Obtiene un valor POST sanitizado
     * @param string $clave - Nombre de la clave POST
     * @param string $tipo - Tipo de sanitización ('texto', 'email', etc.)
     * @return string - El valor sanitizado o vacío
     */
    public static function obtenerPost($clave, $tipo = 'texto')
    {
        if (!isset($_POST[$clave])) {
            return '';
        }

        $valor = $_POST[$clave];

        switch ($tipo) {
            case 'email':
                return self::sanitizarEmail($valor);
            case 'texto':
            default:
                return self::sanitizarTexto($valor);
        }
    }

    /**
     * Valida múltiples campos a la vez
     * @param array $campos - Array de campos a validar
     * @return array - ['valido' => bool, 'errores' => array]
     */
    public static function validarCampos($campos)
    {
        $errores = [];

        foreach ($campos as $campo => $reglas) {
            foreach ($reglas as $regla => $params) {
                switch ($regla) {
                    case 'requerido':
                        $resultado = self::validarRequerido($reglas['valor'] ?? '', $campo);
                        if (!$resultado['valido']) {
                            $errores[$campo][] = $resultado['error'];
                        }
                        break;
                    case 'email':
                        if (!empty($reglas['valor']) && !self::validarEmail($reglas['valor'])) {
                            $errores[$campo][] = "El email no es válido";
                        }
                        break;
                }
            }
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }
}
