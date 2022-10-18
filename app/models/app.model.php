<?php

session_start();


$_SESSION["login"] = false ;



class App {

    private static $name =  'localhost' ;
    private static $user = 'root';
    private static $passWord =  'mysql';
    private static $dataBase =  'games';


    public  static function dataBase(){
        return new mysqli(self::$name , self::$user , self::$passWord , self::$dataBase) ;
    } 


    public static function check_connection(){

        if(self::dataBase()){
            return true;

        }else{
            return die("Database connection failed");
        }

    }
     

    public function personal_nav(){
        $html_code = " ";

 
        if(isset($_SESSION['user_name'])){
            $html_code .= "<h2><a href='/../../meesterproef/app/views/personalPage.php'>Personal page</a></h2";
        }

        return $html_code;
    }
    

    public function logout($logout){

        if($logout == "true"){

            unset($_SESSION['user_name'] , $_SESSION["login"]);
            session_destroy();

            return header('Location: /../../meesterproef/index.php');
        }
    }

    public static function check_login(){

        if(!isset($_SESSION["user_name"]) && $_SESSION["login"] != true){

            return header('Location: /../../meesterproef/index.php');
        
        }else{

            return true;
        }
    }

    public static function check_set_data($data){

        if(!isset($data)){

            return header('Location: /../../meesterproef/app/views/personalPage.php');
        
        }else{

            return true;
        }
    }


    public static function select_user_id(){

        
        $username = $_SESSION["user_name"];
        
        $mysqli = App::dataBase();
        $query = $mysqli->prepare("SELECT id FROM user WHERE username = ? ");
        App::prepare_method(1 , $query , "s" , $username , "" , "");

        $data =  $query->get_result();
         
        $row =  $data->fetch_assoc();

        $row_id = $row['id'];


        $query->close();
        $mysqli->close();

        return $row_id ;
        
    }

    public static function select_players($id){

        $mysqli = App::dataBase();
        $query = $mysqli->prepare("SELECT * FROM players WHERE plan_id= ? ");
        App::prepare_method(1 , $query , "i" , $id , "" , "");
        $data =   $query->get_result();


        $mysqli->close();
        $query->close();
         
        return $data->fetch_assoc();
         

    }

    public static function select_player_id( $name , $plan_id){

       

        $mysqli = App::dataBase();
        $query = $mysqli->prepare("SELECT * FROM players WHERE name= ? AND plan_id= ? ");
        App::prepare_method(2 ,  $query , "si" , $name , $plan_id , "" );
        $data = $query->get_result();
        $row = $data->fetch_assoc();

        $id = $row['id'];

        $query->close();
        $mysqli->close();

        return  $id;

         
        
    }

    public static function prepare_method( $num , $query , $dataType , $col1 , $col2 ,  $col3){

        if($num == 2){

            $query->bind_param($dataType  , $col1 , $col2);
       
        }elseif($num == 1){
            $query->bind_param($dataType  , $col1);
        }else{
            $query->bind_param($dataType  , $col1 , $col2 , $col3);
        }
       
        

        return  $query->execute();;

    }

    public static function read_how_many_players_in_plan($plan_id){


        $mysqli = App::dataBase();
        $query = $mysqli->prepare("SELECT  COUNT(*) FROM players WHERE plan_id= ? ");
        $result = App::prepare_method(1 ,  $query , "i" , $plan_id ,  "" , "" );
        $data = $query->get_result();

        $query->close();
        $mysqli->close();

        if($result){

            $row = $data->fetch_assoc();
            return $row["COUNT(*)"];

        }else{
            return  die('Query faild!' . mysqli_error(App::dataBase()) );
        }
    }

    public static function max_players($Game_id){
        

        $mysqli = App::dataBase();
        $query = $mysqli->prepare("SELECT * FROM games WHERE id= ? ");
        $result = App::prepare_method(1 , $query , "i" , $Game_id , "" , "");
        $data= $query->get_result();

        $row = $data->fetch_assoc();
        $max_players = $row['max_players'];

        $query->close();
        $mysqli->close();

        if($result){
           return $max_players ;

        }else{
            return  die('Query faild!' . mysqli_error(App::dataBase()) );
       }

    }


    public static function select_game_id($Plan_id){
        

        $mysqli = App::dataBase();
        $query = $mysqli->prepare("SELECT * FROM planning WHERE id= ? ");
        $result = App::prepare_method(1 , $query , "i" , $Plan_id , "" , "");
        $data = $query->get_result();


        $query->close();
        $mysqli->close();
        
        if($result){

           $row =  $data->fetch_assoc();
           $id = $row['Game_ID'];
           return $id ;

        }else{
            return  die('Query faild!' . mysqli_error(App::dataBase()) );
       }

    }

    public static function select_plan_user_id($Plan_id){
        
        $mysqli = App::dataBase();
        $query =  $mysqli->prepare("SELECT * FROM planning WHERE id= ? ");
        $result = App::prepare_method(1 , $query , "i" , $Plan_id , "" , "");
        $data =  $query->get_result();

        $query->close();
        $mysqli->close();

        if($result){

           $row =  $data->fetch_assoc();
           $id = $row['userID'];
           return $id ;

        }else{
            return  die('Query faild!' . mysqli_error(App::dataBase()) );
       }

    }

    public static function mysql_escape($data){
    
        return mysqli_real_escape_string(App::dataBase() ,  $data); 

    }


    public static function gameDetails($result){
        
        $data_game_details =  $result;

                
        $image =  $data_game_details['image'];

        $name_of_the_game =   $data_game_details['name'];

        $description =   $data_game_details['description'];

        $expansions =   $data_game_details['expansions'];

        $skills =   $data_game_details['skills'];

        $min_players =   $data_game_details['min_players'];

        $max_players =   $data_game_details['max_players'];

        $explain_minutes =   $data_game_details['explain_minutes'];

        $play_minutes =   $data_game_details['play_minutes'];

        $url =   $data_game_details['url'];

        $youtube =   $data_game_details['youtube'];


    
        $details = "";
        $details .= "<h2> $name_of_the_game </h2> <br> ";
        $details .=  "<img src='/../../game/afbeeldingen/$image' alt=$name_of_the_game  width='200' > ";
        $details .=  "<br>  $youtube  <p> $description  <br> <a href='$url'> to the game </a>  <br>  ";
        $details .=  "Expansions : $expansions Skills : $skills <br> Min players : $min_players ";
        $details .=  " <br> Max players : $max_players <br>  Explain minutes :  $explain_minutes  ";
        $details .= "<br> Play minutes : $play_minutes </p> ";

        return $details;
    }


   

}

























?>