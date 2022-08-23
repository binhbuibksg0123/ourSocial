<?php
include_once "mvc/core/session_help.php";
class users extends Controller{
    private $userModel;
    public function __construct(){
        $this->userModel = $this->model("userModel");
    }
    public function createUserSession($user){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        header("Location: home");
        exit();
    }
    public function mainpage(){
        if(isset($_POST['login'])){
            
            $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
            ];
            if(empty($data['username'])||empty($data['password'])){
                $this->view("loginLayout",[
                    "errorLogin" => "Please fill in all fields"
                ]);
            }
            if($this->userModel->findUserByEmailOrUsername($data['username'],$data['username'])){
                $logginUser = $this->userModel->loginUser($data);
                if($logginUser){
                    $this->createUserSession($logginUser);
                }
                else{
                    $this->view("loginLayout",[
                        "errorLogin" => "Invalid username or password"
                    ]);
                }
            }
            else{
                $this->view("loginLayout",[
                    "errorLogin" => "No user found"
                ]);
            }
        }else{
            echo "hi";
            $this->view("loginLayout");
        }
        
    }
    public function signup(){
        if(isset($_POST['username'])){
            $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
            $data = [
                "username"=>trim($_POST['username']),
                "email"=>trim($_POST['email']),
                "password"=>trim($_POST['pwd']),
                "confirm_password"=>trim($_POST['pwdRe']),
            ];
            if(empty($data['username'])||empty($data['email'])||empty($data['password'])||empty($data['confirm_password'])){
                // aloo("signup","Please fill out all inputs");
                $this->view("signupLayout",[
                    "errorRegiester"=>"Please fill out all inputs",
                ]); 
            }
            if(!preg_match("/^[a-zA-Z0-9]*$/",$data['username'])){
                // aloo("signup","Username must be alphanumeric");
                $this->view("signupLayout",[
                    "errorRegiester"=>"Username must be alphanumeric",
                ]); 
            }
            if($data['password']!=$data['confirm_password']){
                $this->view("signupLayout",[
                    "errorRegiester"=>"Password and confirm password do not match",
                ]); 
            }
            if(!filter_var($data['email'],FILTER_VALIDATE_EMAIL)){
                $this->view("signupLayout",[
                    "errorRegiester"=>"Invalid email",
                ]); 
            }
            if($this->userModel->findUserByEmailOrUsername($data['email'],$data['username'])){
                $this->view("signupLayout",[
                    "errorRegiester"=>"Username or email already exists",
                ]); 
            }else{
                $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);
                if($this->userModel->registerUser($data)){
                    header("Location: login");
                    exit();
                }
                else{
                    die("Something went wrong");
                }
            }
        }
        else {
            $this->view("signupLayout"); 
        }
    }
}
?>