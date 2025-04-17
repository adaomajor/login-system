<?php
    require_once './db/config.php';
    require_once './auth/auth.php';
    use App\Auth\auth;

     header('Content-Type: application/json; charset=UTF-8');
     header('X-Powered-By: Adam Major');

    function get($arr, $key){
        return (isset($arr[$key])) ? $arr[$key] : false;
    }

    function getInputs(){
        return json_decode(file_get_contents('php://input'), true);
    }

    $routes = [
        "uri=/api/user/login",
        "uri=/api/user/auth",
        "uri=/api/user/logout",
        "uri=/api/user/register",
        "uri=/api/user/verify",
        "?help=help"
    ];

    if(isset($_GET['help'])){
        echo json_encode([ "routes" => $routes]);
        exit(0);
    }

    if(isset($_GET['uri']) && $_GET['uri'] != ""){
        switch($_GET['uri']){
            case "/api/user/login":
                if($_SERVER['REQUEST_METHOD'] != "POST"){
                    http_response_code(400);
                    echo json_encode(["Error" => ["message" => "method not allowd"]]);
                    exit(0);
                }
                $inputs = getInputs();
                if($inputs == NULL){
                    echo json_encode(["Error" => [ "message" => "invalid json body"]]);
                    http_response_code(400);
                    exit(0);
                }
                $defaults = [
                    "email" => '',
                    "password" => ''
                ];
                $_ = array_merge($defaults, $inputs);
                extract($_);
                if(empty($email) || empty($password)){
                    http_response_code(400);
                    echo json_encode(["Error" => ["message" => "email or password missing"]]);
                    exit(0);
                }
                $user = auth::login($email, $password);
                if($user){
                    echo json_encode($user);
                    exit(0);
                }else{
                    echo json_encode(["Error" => ["message" => "email or password invalid"]]);
                    exit(0);
                }
            case "/api/user/verify":
                // headers
                //      x-auth-token
                if($_SERVER['REQUEST_METHOD'] != "POST"){
                    http_response_code(400);
                    echo json_encode(["Error" => ["message" => "method not allowd"]]);
                    exit(0);
                }
                if(isset($_SESSION['auth_token']) && $_SESSION['auth_token'] == $_SERVER['HTTP_X_AUTH_TOKEN']){
                    $user = json_decode(preg_replace("/\.[a-zA-Z0-9]+/","",base64_decode($_SERVER['HTTP_X_AUTH_TOKEN']))); 
                    echo json_encode([$user, ["logged" => true]]);
                    exit(0);
                }else{
                    echo json_encode(["logged" => false]);
                    http_response_code(401);
                    exit(0);
                }
                exit(0);
            case "/api/user/register":
                if($_SERVER['REQUEST_METHOD'] != "POST"){
                    http_response_code(400);
                    echo json_encode(["Error" => ["message" => "method not allowd"]]);
                    exit(0);
                }
                $inputs = getInputs();
                if($inputs == NULL){
                    echo json_encode(["Error" => [ "message" => "invalid json body"]]);
                    http_response_code(400);
                    exit(0);
                }
                $defaults = [
                    "name" => '',
                    "surname" => '',
                    "gender" => '',
                    "phone" => '',
                    "email" => '',
                    "password" => ''
                ];
                $_ = array_merge($defaults, $inputs);
                extract($_);
                if(
                    empty($name) || 
                    empty($surname) ||
                    empty($gender) ||
                    empty($phone) ||
                    empty($email) ||
                    empty($password) ){
                    http_response_code(400);
                    echo json_encode([
                        "Error" => ["message" => "bad fields",
                                    "fields" => ["name", "surname","gender" => ["male","female"],"phone","email","password"]]]);
                    exit(0);
                }

                $user = auth::register($name, $surname, $gender, $phone, $email, $password);

                if($user){
                    http_response_code(201);
                    echo json_encode($user);
                    exit(0);
                }else{
                    echo json_encode(["Error" => ["message" => "something went wrong", "tip" => "trye with different infos"]]);
                    exit(0);
                }
            case "/api/user/logout":
                session_destroy();
                exit(0);
            default:
                http_response_code(404);
                exit(0);
            }

    }else{
        http_response_code(400);
        echo json_encode(["Error" => ["message" => "the uri must be set on the url", "HELP" =>"take a look on http://host/?help=help"]]);
        exit(0);
    }

?>