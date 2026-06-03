<?php

class Database
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Inserta un registro en una tabla
     * @param string $tabla - Nombre de la tabla
     * @param array $datos - Array asociativo con los datos a insertar
     * @return int - ID del último registro insertado
     */
    public function insertar($tabla, $datos)
    {
        if (empty($datos)) {
            throw new Exception("Los datos a insertar están vacíos");
        }

        $datosLimpios = [];
        $columnas = [];
        $placeholders = [];

        foreach ($datos as $columna => $valor) {
            $columnaLimpia = str_replace(':', '', $columna);
            $columnas[] = $columnaLimpia;
            $placeholders[] = ":$columnaLimpia";
            $datosLimpios[":$columnaLimpia"] = $valor;
        }

        $sql = "INSERT INTO $tabla (" . implode(", ", $columnas) . ") VALUES (" . implode(", ", $placeholders) . ")";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datosLimpios);

        return $this->pdo->lastInsertId();
    }

    /**
     * Verifica si existe un registro basado en condiciones
     * @param string $tabla - Nombre de la tabla
     * @param array $condiciones - Array asociativo con las condiciones de búsqueda
     * @return bool - true si existe, false si no
     */
    public function existe($tabla, $condiciones)
    {
        if (empty($condiciones)) {
            throw new Exception("Las condiciones de búsqueda están vacías");
        }

        $where = [];
        $datosLimpios = [];
        
        foreach ($condiciones as $columna => $valor) {
            // Limpiar la clave si tiene ':'
            $columnaLimpia = str_replace(':', '', $columna);
            $where[] = "$columnaLimpia = :$columnaLimpia";
            $datosLimpios[":$columnaLimpia"] = $valor;
        }

        $sql = "SELECT id FROM $tabla WHERE " . implode(" OR ", $where);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datosLimpios);

        return $stmt->fetch() !== false;
    }

    /**
     * Obtiene un registro de la base de datos
     * @param string $tabla - Nombre de la tabla
     * @param array $condiciones - Array asociativo con las condiciones de búsqueda
     * @return array|false - Registro encontrado o false
     */
    public function obtener($tabla, $condiciones)
    {
        if (empty($condiciones)) {
            throw new Exception("Las condiciones de búsqueda están vacías");
        }

        $where = [];
        $datosLimpios = [];
        
        foreach ($condiciones as $columna => $valor) {
            // Limpiar la clave si tiene ':'
            $columnaLimpia = str_replace(':', '', $columna);
            $where[] = "$columnaLimpia = :$columnaLimpia";
            $datosLimpios[":$columnaLimpia"] = $valor;
        }

        $sql = "SELECT * FROM $tabla WHERE " . implode(" AND ", $where) . " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datosLimpios);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene múltiples registros de la base de datos
     * @param string $tabla - Nombre de la tabla
     * @param array $condiciones - Array asociativo con las condiciones de búsqueda (opcional)
     * @return array - Array de registros
     */
    public function obtenerTodos($tabla, $condiciones = [])
    {
        $sql = "SELECT * FROM $tabla";
        $datosLimpios = [];

        if (!empty($condiciones)) {
            $where = [];
            foreach ($condiciones as $columna => $valor) {
                $columnaLimpia = str_replace(':', '', $columna);
                $where[] = "$columnaLimpia = :$columnaLimpia";
                $datosLimpios[":$columnaLimpia"] = $valor;
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datosLimpios);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza un registro en la base de datos
     * @param string $tabla - Nombre de la tabla
     * @param array $datos - Array asociativo con los datos a actualizar
     * @param array $condiciones - Array asociativo con las condiciones WHERE
     * @return int - Número de filas afectadas
     */
    public function actualizar($tabla, $datos, $condiciones)
    {
        if (empty($datos) || empty($condiciones)) {
            throw new Exception("Los datos o condiciones están vacíos");
        }

        $datosLimpios = [];
        $set = [];
        foreach ($datos as $columna => $valor) {
            $columnaLimpia = str_replace(':', '', $columna);
            $set[] = "$columnaLimpia = :$columnaLimpia";
            $datosLimpios[":$columnaLimpia"] = $valor;
        }

        $where = [];
        foreach ($condiciones as $columna => $valor) {
            $columnaLimpia = str_replace(':', '', $columna);
            $where[] = "$columnaLimpia = :cond_$columnaLimpia";
            $datosLimpios[":cond_$columnaLimpia"] = $valor;
        }

        $sql = "UPDATE $tabla SET " . implode(", ", $set) . " WHERE " . implode(" AND ", $where);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datosLimpios);

        return $stmt->rowCount();
    }

    /**
     * Elimina un registro de la base de datos
     * @param string $tabla - Nombre de la tabla
     * @param array $condiciones - Array asociativo con las condiciones WHERE
     * @return int - Número de filas eliminadas
     */
    public function eliminar($tabla, $condiciones)
    {
        if (empty($condiciones)) {
            throw new Exception("Las condiciones de búsqueda están vacías");
        }

        $where = [];
        $datosLimpios = [];
        foreach ($condiciones as $columna => $valor) {
            $columnaLimpia = str_replace(':', '', $columna);
            $where[] = "$columnaLimpia = :$columnaLimpia";
            $datosLimpios[":$columnaLimpia"] = $valor;
        }

        $sql = "DELETE FROM $tabla WHERE " . implode(" AND ", $where);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($datosLimpios);

        return $stmt->rowCount();
    }

    public function guardarSecret2FA($idUsuario, $secret)
    {
        $sql = "UPDATE usuarios SET secret_2fa = :secret WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(":secret", $secret, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
