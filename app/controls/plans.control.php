<?php


require 'C:\Program Files\ammps2\Ampps\www\meesterproef\app\controls\joinPlan.control.php';

class PlansControl {


    #Deze functies gaan plans tonen aan de gebruker

    public static function readplans($user )
    {   

        if($user == "personal"){

            if(isset($_SESSION["user_name"])){

                return PlansModel::get_personal_plans();
                 
            }

        }else{

            return PlansModel::get_all_plans();
            
        }
    }


 
    

    public static function game_details($game_name , $display_type ,  $name_of_the_orgnaiser , $plan_id , $start_time , $type_user ){


        $game_details     =  PlansModel::gameData( $game_name );
        $name_of_the_game =  $game_details['name'];
        $image            =  $game_details['image'];
        $play_time        =  $game_details['play_minutes'];
        $explain          =  $game_details['explain_minutes'];
        $max_players      =  $game_details['max_players'];

         
        $is_full = JoinPlan_model::join_bolean($plan_id , $max_players); 

        $html_code = " ";

        

        if($display_type == "read_plans"){

            $html_code .= "<br>  <h1> Game : <a href='/../../meesterproef/app/views/detailsPage.php?game=$name_of_the_game&id=$plan_id'> $name_of_the_game  </a> ($is_full) </h2> <br> ";  
            $html_code .= "<a  href='/../../meesterproef/app/views/detailsPage.php?game=$name_of_the_game&id=$plan_id'> <img   src='/../../meesterproef/afbeeldingen/$image'   alt='$name_of_the_game'  class='plansBoxImage' >   </a>  ";
            
        }

        $html_code .= "<p>  Start tijd                         :  $start_time                 <br> ";
        $html_code .= "     Speler die zal het spel  uitleggen :  $name_of_the_orgnaiser      <br> ";
        $html_code .= "     Uitliggen tijd                     :  $play_time                  <br> ";
        $html_code .= "     Spel tijd                          :  $explain               <br> </p> ";

        if($type_user == "admin"){  
                
            $html_code     .=   "<div class='adminPlanBox' > ";
            $html_code     .=   "<h2>Bewerk uw plan </h2> ";

            if($is_full    ==   "Niet vol"){
                $html_code .=   "<a href='/../../meesterproef/app/views/add_updatePlayer.php?name=$game_name&id=$plan_id&type=add'>  Speler toevoegen  </a>  <br>";
            }

            $html_code .= self::update_delete_plan($plan_id);

            $html_code .=   " </div> ";
            
        }

        

        return $html_code;

    }






    public static function show_plans($plan_details_query , $user_type , $type_display ){



        if($plan_details_query){

            $plan_details =   $plan_details_query;
            
            $game_name              =  $plan_details['name'];
            $start_time             =  $plan_details['startTime'];
            $name_of_the_orgnaiser  =  $plan_details['makerName'];

            $plan_id                =  $plan_details['id'];

            $html_code = "";

            

            $html_code .= self:: game_details($game_name  , $type_display ,  $name_of_the_orgnaiser , $plan_id   , $start_time , $user_type  );
            

            $players_num = App::read_how_many_players_in_plan($plan_id);
            
          
            
            if($players_num != 0){

                $html_code .=   "<div class='playersTabel'> ";
                $html_code .=   "<h2> Spelers : ( $players_num ) </h2> ";

                $players_list = PlansModel::players($plan_id);

                foreach($players_list as $value){

                    $player_details =   PlansModel::read_player_data($value);

                    $player_name    =   $player_details['name'];
                    $player_id      =   $player_details['id'];
                    $html_code     .=   " <br>  $player_name ";

                    if($user_type == "admin"){  

                        $html_code .= "<div class='adminButtonsBox'> ";
                        $html_code .= "     <a class='adminButtonUpdate' href='/../../meesterproef/app/views/add_updatePlayer.php?name=$player_name&id=$plan_id&type=update'>  Updaten </a> ";

                        

                        $html_code .= "     <form  action='' method='POST'> ";  
                        $html_code .= "         <input type='hidden' name='plan_id'     value='$plan_id'> ";  
                        $html_code .= "         <input type='hidden' name='player_name' value='$player_name'> ";  
                        $html_code .= "         <input type='hidden' name='player_id'   value='$player_id'> ";  
                        $html_code .= "         <input type='hidden' name='delete_type' value='player'> ";  
                        $html_code .= "         <input class='adminButtonDelete' onclick='myFunction()' type='submit' name='submit' value='verwijdren'> " ;
                        $html_code .= "     </form>  ";

                        $html_code .= "</div> ";
                    }

                    
 
                }

                $html_code .=   "</div> ";
 
            }else{

                $html_code .=   "<div class='playersTabel'> ";
                $html_code .=   "   <br> ";
                $html_code .=   "   <h2> Spelers : ( $players_num ) </h2> ";

                $html_code .=   "   <p> Geen spelers tot nu </p> " ;
                $html_code .=   "</div> ";

            }
            
            $html_code .= "</h1>  ";
            

            if($user_type == "joinedGames"){


                $html_code .= " <div class='deletBox' > ";
                $html_code .= "     <form action='' method='POST'> ";  
                $html_code .= "     <input type='hidden' name='plan_id' value='$plan_id'> ";    
                $html_code .= "     <input onclick='myFunction()' type='submit' name='submit' value='Verwijderen'> " ;
                $html_code .= "     </form>  ";
                $html_code .= " </div> ";

                
            }

          

            return  $html_code;
            
        }else{

            return die('Query failed plans control' . mysqli_error(App::dataBase()));

        }

    }





    #Deze functies gaan data van de gebruiker halen en tovoegt dit data aan de deatabase

    public  function select_game()
    {   
  
        $games_list = []; 
        $result = PlansModel::selectGame();

        while($game_details =  mysqli_fetch_assoc($result )){

            
            $game_name    = $game_details['name'];
            $game_id      = $game_details['id'];
            $play_time    = $game_details['play_minutes'];
            $max_players  = $game_details['max_players'];
    

            $value = "<option value='$game_id'> Name : $game_name | Time : $play_time | Max players : $max_players  </option>" ;

            array_push( $games_list ,  $value );

        }

        return $games_list;
       
    } 





    public static function show_insert_boxes($selected_game , $plan_id , $user_name , $opretaion_type , $update_plan ){

        $maker = $time =  '';
     
        $html_code = " ";

        if(isset($selected_game , $plan_id , $user_name , $opretaion_type , $update_plan )){
            
            if($opretaion_type == "update"){

                $user_name             =    $user_name;
                $plan_details          =    $update_plan;

                $plan_id               =    $plan_details['id'];
                $maker                 =    $plan_details['makerName'];
                $time                  =    $plan_details['startTime'];
                

                $_SESSION["game"]      =    $selected_game;

                $html_code .= "<input type='hidden' name='plan_id'  value='$plan_id'  > ";
            
            }

            $game_details = PlansModel::show_insert_boxes($selected_game , "not details");

            echo App::gameDetails($game_details);
            
            $max_players =    $game_details['max_players'];
            $play_time   =    $game_details['play_minutes'];
            $game_name  =    $game_details['name'];
            


            $html_code .= "<div class='reservernFormsBox' > ";
            

            if($opretaion_type == "update" ){

                $html_code .= "     <h1> Bewerk uw plan </h1> <br> "  ;

            }else{

                $html_code .= "     <h1> Maak een plan </h1> <br> "  ;

            }

          

            $html_code .= "         <div class='inputStyle'> ";

            $html_code .= "             <label> Name of the explain player : </label> "  ;

            $html_code .= "             <input   type='text' name='maker' value='$maker' required>  <br> ";

            $html_code .= "         </div> ";

            $html_code .= "         <input type='hidden' name='game_name' value='$game_name' >  ";

            $html_code .= "         <input type='hidden' name='play_time' value='$play_time' >  <br> ";

            $html_code .= "         <input type='hidden' name='game_id' value='$selected_game' >  <br> ";

            $html_code .= "         <div class='inputStyle'> ";

            $html_code .= "             <label>Start time : </label> "; 
            
            $html_code .= "             <input   type='time'  value='$time' name='time' required>   " ;

            $html_code .= "         </div> ";




            


            $players_number = 0;


            if($opretaion_type != "update" ){

                while($players_number !=  $max_players ){

                    $number       =   strval($players_number);
                    $label_number =   $players_number + 1;

                    $html_code   .=   " <div class='inputStyle'> ";
                    $html_code   .=   "     <label> Player  $label_number :  </label> ";
                    $html_code   .=   "     <input  type='text' name='player$number'   > " ;
                    $html_code   .=   " </div> ";

                    $players_number++;

                }
            
            }   

            $_SESSION["numPlayers"] =   $players_number;

            $html_code .= "     </div> ";
                     
            if($opretaion_type == "update"){

                $html_code .= "     <input class='reserveButton'  onclick='myFunction()'  type='submit' name='submit' value='update'> ";

            }else{
                $html_code .= "     <input class='reserveButton' onclick='myFunction()''  type='submit' name='submit' value='reserve'> ";

            }
            
            $html_code .= "</div> ";
            return  $html_code;

        }else{

            return "Something wrong";
        }


    }






    public static function reserveren()
    {
 
        if($_SERVER["REQUEST_METHOD"] == "POST"){

            if(!empty($_POST["game_name"])){

                $selected_game = $_POST['game_name'];
                $selected_game = mysqli_real_escape_string(App::dataBase() ,  $selected_game);
               
                return self::show_insert_boxes($selected_game , "" , "" , "" , "" );
 
            }
        }
    }





    public static function insert($opreation_type){

        if($_SERVER["REQUEST_METHOD"] == "POST"){

            if(!empty($_POST["maker"]) && !empty($_POST["play_time"])){

               
                $user_id =  App::select_user_id();
                $plan_id = $_POST["plan_id"];
                 
 
                $person_who_explains_game = $_POST["maker"];
                $person_who_explains_game = App::mysql_escape($person_who_explains_game);

 
                $game_time  =  $_POST["time"];  
                
                $start_time =  (string) $game_time;

                $start_time =  App::mysql_escape($start_time);

                $play_time  =  $_POST['play_time'];
                
  

                $game_Name  =  $_POST['game_name'];
                $game_Name  =  App::mysql_escape($game_Name);

                $game_id    =  $_POST['game_id'];
                $game_id    =  App::mysql_escape($game_id);

                
                $players = [];

                $max_players        =   $_SESSION["numPlayers"];
                $num                =   0;

                while($num != $max_players){

                    $players_number =   strval($num);
                    $player         =   $_POST["player$players_number"];

                    if(!empty($player)){
                        array_push($players ,  $player );
                    }

                    $num++;

                }   
 
                return PlansModel::insert($opreation_type , $plan_id , $game_Name , $person_who_explains_game , $start_time , $play_time , $user_id , $game_id , $players );
                 
            }
        }
    }

    public static function update_delete_plan($id){

       

        $html_code = " ";
        
        $html_code .= "     <a href='/../../meesterproef/app/views/update_plans.php?id=$id&type=update'>Plan updaten </a> "; 

        $html_code .= "     <form action='' method='POST'> ";  
        $html_code .= "     <input type='hidden' name='plan_id' value='$id'> ";  
        $html_code .= "     <input type='hidden' name='delete_type' value='plan'> ";
        $html_code .= "     <input onclick='myFunction()' type='submit' name='submit' value='Plan verwijderen'> " ;
        $html_code .= "     </form>  ";
        

        return $html_code;
            
    }


 
     
}













?>