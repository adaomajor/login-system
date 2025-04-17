<?php
    namespace App\Auth;

    require_once './db/db.php';
    use Exception;
    use App\DB\DB;
    use PDO;

    session_start();

    class auth{
        public static function login($email, $password){
            $db = DB::getConnection();
            $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$user){
                return false;
            }
            if($user['password'] == md5($password)){
                unset($user['password']);
                $user["auth_token"] = base64_encode(json_encode($user).".".bin2hex(random_bytes(32)));
                $_SESSION['auth_token'] = $user["auth_token"];
                return $user;
            }
        }

        public static function register($name, $surname, $gender, $phone, $email, $password){
           $db = DB::getConnection();
           $_ = $db->query("SELECT email FROM users WHERE email='".$email."'");
           if($_ && $_->rowCount() > 0){
                return false;
            }
            $stmt = $db->prepare("INSERT INTO users(name, surname, gender, phone, email, password) VALUES(:name, :surname, :gender, :phone, :email, :password)");
            $stmt->execute([
                ":name" => $name, 
                ":surname" => $surname,
                ":gender" => $gender,
                ":phone" => $phone,
                ":email" => $email,
                ":password" => md5($password)
            ]);

            $LastID = $db->lastInsertId();
            $_ = $db->prepare("SELECT name, surname, gender, phone, email FROM users WHERE id = :id");
            $_->execute([":id" => $LastID]);
            $user = $_->fetch(PDO::FETCH_ASSOC);
            $user["auth_token"] = base64_encode(json_encode($user).".".bin2hex(random_bytes(32)));
            $_SESSION['auth_token'] = $user["auth_token"];
            return $user;
        }


        //function logout(){}
    }



?>