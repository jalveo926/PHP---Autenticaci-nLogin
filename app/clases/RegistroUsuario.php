<?php

class RegistroUsuario
{
    private $db;
    private $passwordHasher;

    public function __construct($db, $passwordHasher)
    {
        $this->db = $db;
        $this->passwordHasher = $passwordHasher;
    }

    public function validarNombre($nombre)
    {
        return Sanitizer::validarNombre($nombre);
    }

    public function validarUsuario($usuario)
    {
        return Sanitizer::validarUsuario($usuario);
    }

    public function validarEmail($email)
    {
        return Sanitizer::validarEmail($email);
    }

    public function validarPassword($password)
    {
        return Sanitizer::validarPassword($password, 4);
    }

    public function validarCoincidenciaPassword($password, $password_confirm)
    {
        return Sanitizer::validarPasswordCoinciden(
            $password,
            $password_confirm
        );
    }

    public function usuarioExiste($usuario, $email)
    {
        return $this->db->existe(
            'usuarios',
            [
                ':usuario' => $usuario,
                ':email' => $email
            ]
        );
    }

    public function generarHash($password)
    {
        return $this->passwordHasher->hash($password);
    }

    public function registrar($nombre, $email, $usuario, $passwordHash)
    {
        return $this->db->insertar(
            'usuarios',
            [
                ':nombre' => $nombre,
                ':email' => $email,
                ':usuario' => $usuario,
                ':password' => $passwordHash
            ]
        );
    }
}