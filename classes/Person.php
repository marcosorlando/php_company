<?php
    
    class Person
    {
        private static $conn;
        
        public static function getConnection()
        {
            if (empty(self::$conn)) {
                $connection = parse_ini_file('config/books.ini');
                self::$conn = new PDO(
                    "mysql:host={$connection['host']};port={$connection['port']};dbname={$connection['name']}",
                    "{$connection['user']}",
                    "{$connection['pass']}",
                    [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8']
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
            return self::$conn;
        }
        
        public static function save($person)
        {
            $conn = self::getConnection();
            if (empty($person['id'])) {
                $result = $conn->query("SELECT max(id) as next FROM person");
                $row = $result->fetch();
                $person['id'] = (int)$row['next'] + 1;
                $sql = "INSERT INTO person
                                    (id, name, cep, address, district, phone, mail, city, state) VALUES
                                    (:id, :name, :cep, :address, :district, :phone, :mail, :city, :state)";
            } else {
                $sql = "UPDATE person SET name = :name, cep=:cep, address = :address, district = :district, phone = :phone, mail = :mail, city = :city, state= :state WHERE id = :id ";
            }
            
            $result = $conn->prepare($sql);
            
            return $result->execute(
                [
                    ':id' => $person['id'],
                    ':name' => $person['name'],
                    ':cep' => $person['cep'],
                    ':address' => $person['address'],
                    ':district' => $person['district'],
                    ':phone' => $person['phone'],
                    ':mail' => $person['mail'],
                    ':city' => $person['city'],
                    ':state' => $person['state']
                ]
            );
        }
    
        /**
         * Busca uma Pessoa pelo seu $id
         * @param $id
         *
         * @return mixed
         */
        public static function find($id)
        {
            $conn = self::getConnection();
            $result = $conn->query("SELECT * FROM person WHERE id='{$id}'");
            
            return $result->fetch();
        }
        
        public static function delete($id)
        {
            $conn = self::getConnection();
            $result = $conn->query("DELETE FROM person WHERE id='{$id}'");
            
            return $result;
        }
        
        public static function all()
        {
            $conn = self::getConnection();
            $result = $conn->query("SELECT * FROM person");
            
            return $result;
        }
    }
