<?php

require 'C:\Program Files\ammps2\Ampps\www\meesterproef\app\controls\plans.control.php';



class Update_Delete_Model {
    

    public static function update($id , $user_id){

        $mysql    =   App::dataBase();

        $query    =   $mysql->prepare("SELECT * FROM planning WHERE id=? AND userID=?");
        App::prepare_method(2 , $query , "ii" , $id , $user_id , "" );
        $results  =   $query->get_result();

        $query->close();
        $mysql->close();

        return  $results->fetch_assoc();

    }






    public static function add_update_Player($name  , $plan_id , $player  , $type){
        
        $id     =   App::select_player_id( $name , $plan_id);

        if( $type == "update"){

            $mysqli    =    App::dataBase();
            $query     =    $mysqli->prepare("UPDATE players SET name= ? WHERE plan_id= ? AND id= ? ");
            $result    =    App::prepare_method(3 ,  $query, "sii" , $player , $plan_id  , $id );

            if($result){

                
                $query->close();
                $mysqli->close();

                return header('Location: /../../meesterproef/app/views/feedback_page.php?type=delete');

            }else{

                return die('Query faild!' . mysqli_error(App::dataBase()) );

            }     

        }else{

            $game_id          =    App::select_game_id($plan_id);
            $maxPlayers       =    App::max_players($game_id);

            $full_or_not_full =    JoinPlan_model::join_bolean( $plan_id , $maxPlayers);

            if($full_or_not_full == "Niet vol"){

                $mysqli = App::dataBase();
                $query = $mysqli->prepare("INSERT INTO players(name , plan_id ) VALUES( ? , ? )");
                $result = App::prepare_method(2 ,  $query, "si" , $player , $plan_id  , "" );

                if($result){

                     
                    $query->close();
                    $mysqli->close();
                     

                    return header('Location: /../../meesterproef/app/views/feedback_page.php?type=delete');

                }else{

                    return die('Query faild!' . mysqli_error(App::dataBase()) );

                }  

            }else{

                return " U kunt niet meer spelers toevogen! want uw  plan is vol";

            }

        }       
        
    }





    


    public static function delete($plan_id , $user_id ,  $delete_type , $player_id){


        $mysql          =    App::dataBase();
        $delete_type    =    $delete_type;


        if($delete_type == "player"){

            $query      =    $mysql->prepare("DELETE FROM players WHERE  id= ?  ");

            App::prepare_method(1 , $query , "i" , $player_id , "" , "");

            
            $query->close();
            $mysql->close();

 
        }else{

            $query      =    $mysql->prepare("DELETE FROM planning WHERE  id=? AND userID=? ");

            App::prepare_method(2 , $query , "ii" , $plan_id  , $user_id , "");
    
             
            $query->close();
            $mysql->close();
    
            return header('Location: /../../meesterproef/app/views/feedback_page.php?type=delete');

        }
    }
}

 
?>