<?php
class Database
{
    private $host = 'localhost';
    private $db = 'projetfins4';
    private $user = 'Talal123';
    private $pass = 'Talal123';
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db}", $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
