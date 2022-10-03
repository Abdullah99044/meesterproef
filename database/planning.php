<?php



include 'C:\Program Files\ammps2\Ampps\www\game_alpha\database\app.php';




class select extends App{

    public function select()
    {   



        if($this->connecting()){

            
            $query = "SELECT name FROM games";

            $results = mysqli_query($this->dataBase() , $query );

            if($results){

               while($row = mysqli_fetch_assoc($results)){

                $name = $row['name'];

                echo "<option value='$name'>$name  </option>" ;


               }

            }else{

                return die('Query failed!' . mysqli_error($this->dataBase()));
            }
     
               
        }
    }

}

class Plans extends App {

     
    public function show_plans($results , $type ){

            if($results){
                
                $game =  mysqli_fetch_assoc($results);
                $start_time = $game['startTime'];
                $name_of_the_game = $game['name'];
                $name_of_the_orgnaiser = $game['makerName'];

                $id =  $game['id'];

                $players = $game['players'];
                $players = explode("," ,  $players);


                $query_image_time = "SELECT image , play_minutes , explain_minutes  FROM games WHERE name='$name_of_the_game'";

                $result_image_time = mysqli_query($this->dataBase() , $query_image_time );

                if($result_image_time){

                    $image_time =  mysqli_fetch_assoc($result_image_time);
                    $image = $image_time['image'];
                    $time = $image_time['play_minutes'] + $image_time['explain_minutes'];

                }
                
                
                echo '<br>' . "<h2> Start time : $start_time </h2>" . '<br>';
                echo "<img src='/../../game_alpha/afbeeldingen/$image' alt='$name_of_the_game'>";
                echo '<br>' . "<p> Game : <a href='/../../game_alpha/pages/detailsPage.php?game=$name_of_the_game&id=$id'> $name_of_the_game </a>" . '<br>';
                echo  "<p> The orgnaiser : $name_of_the_orgnaiser  " . '<br>';
                echo "Time : $time " . '<br>';
                echo " Players : ";

                foreach($players as $player){
                    echo  '<br>' . $player ;
                }
                echo "</p>";

                if($type == "admin"){

                    
                    echo "<a href='/../../game_alpha/pages/update_plans.php?id=$id&type=update'> Update </a>";
                    echo '<form action="" method="POST">' ;
                    echo "<input type='hidden' name='id' value='$id'>" ;
                    echo '<input onclick="myFunction()" type="submit" name="submit" value="delete">';
                    echo '  </form>';
                }
                

                 
            
            }else{

                return die('Query failed!' . mysqli_error($this->dataBase()));

            }

    }

    public function readplans($user , $type)
    {  
        $username = $_SESSION["user_name"];

        if($user == "personal"){


            $query = "SELECT id FROM planning";

            $results = mysqli_query($this->dataBase() , $query );
        
            while($row = mysqli_fetch_assoc($results)){

                 

                $id = $row['id'];

                if($results){

                    $query_2 = "SELECT * FROM planning WHERE id='$id'";
                    
                    $results_2 = mysqli_query($this->dataBase() , $query_2 );

                    $row_name = mysqli_fetch_assoc($results_2);

                    $name = $row_name['userName'];

                    if( $results_2){

                        if($name == $username){

                            $query_3 = "SELECT * FROM planning WHERE id='$id' AND userName='$username'";

                            $results_3 = mysqli_query($this->dataBase() , $query_3 );
    
                            if($results_3){
    
                                $this->show_plans( $results_3 , $type );
    
                            }else{
    
                                echo die('Query failed!' . mysqli_error($this->dataBase()));
                            }

                        }
                       
                    }else{

                        echo  die('Query failed!' . mysqli_error($this->dataBase()));
                    } 

                    $id++;
    
                }


            }
            
        }else{

            $query1 = "SELECT id FROM planning ";
            $results1 = mysqli_query($this->dataBase() , $query1 );

            if($results1){

                while($row = mysqli_fetch_assoc($results1)){

                    $ids = $row['id'] ;

                    $query = "SELECT * FROM planning WHERE id='$ids'";
                    $results = mysqli_query($this->dataBase() , $query );
        
                    $this->show_plans($results ,  $ids);



                 }
        
       

             }

        }
    }


   

    public function reserve()
    {
 
        if($_SERVER["REQUEST_METHOD"] == "POST"){

            if(!empty($_POST["gameName"])){
                

                $select = $_POST['gameName'];
                $_SESSION["game"] =  $select;
            

                if($this->connecting()){

                    $this->show_insert_boxes($select , "" , "" , "" , "" );

                    }else{

                        die('Query faild!' . mysqli_error($this->dataBase()) );

                    }
            

                }
            }
        }
     


    public function show_insert_boxes($select , $id , $userName , $type , $game_update ){

        $maker = $time =  '';
        $game_players = ['' , '' , ' ' , '' , '' , '' , ' ' , '' , '' , '' , ' ' , ''];
     

        if($type == "update"){

            $game_id = $id;
            $_SESSION["game_id"] =  $game_id;

            $username = $userName;
             
            $game = $game_update;

            $maker = $game['makerName'];
            $time = $game['startTime'];
            $game_players = $game['players'];
            $game_players = explode("," ,  $game_players);

            $_SESSION["game"] =  $select;

        }

  
        $query = "SELECT * FROM games where name='$select'" ;    
        $result = mysqli_query( $this->dataBase() , $query );
        $row = mysqli_fetch_assoc($result);

        
        $maxPlayers = $row['max_players'];


        $players = (string) $maxPlayers;
        $_SESSION["numPlayers"] = $players;

        $img = $row['image'];

        $playTime = $row['play_minutes'] + $row['explain_minutes'];

        $num = 1;
        $player_num = 0;
        
        echo "<input type='hidden' name='id'  value=$game_id >";
        echo "<label>Name of the explain player : </label>"  . "<input type='text' name='maker' value='$maker' required>" . '<br>';

        echo  "<label>Start time : </label>" .  "<input type='time'  value='$time' name='time' required>" . '<br>' ;


        for($i = 0 ; $i < $maxPlayers ; $i++){

            $numString =  (string) $num;

            
            ?> 

            <br> <label>Player $num : </label>  <input required  type='text' value='<?php echo $game_players[$player_num]; ?>' <?php echo "name='player$numString'" ?> >"   
            
            <?php

            $num++;
            $player_num++;
            
        }

        echo   "<br>" . $game_id . " = " . " id ";
        
      
        

       
        
    
    }

    public function insert($type){

        if($_SERVER["REQUEST_METHOD"] == "POST"){


            if(!empty($_POST["maker"]) && !empty($_POST["time"])){

               
                $username = $_SESSION["user_name"];;

                $userName = mysqli_real_escape_string($this->dataBase() ,$username);
                 #Het naam van de speler die zal uitlig geven over het spel

                $person_who_explains_game = $_POST["maker"];
                $person_who_explains_game = mysqli_real_escape_string($this->dataBase() , $person_who_explains_game);

                #De start tijd van het spell

                $time = $_POST["time"];  
                
                $startTime = (string) $time;

                $startTime =  mysqli_real_escape_string($this->dataBase() , $startTime);

                
                #Deze 'session' is gemaakt om ons te vertellen over het aantal spelers

                 

                $players = []; #in dit array bewaren we de namen van de spelers

                $number_of_players = $_SESSION["numPlayers"] ; #Deze variable bewaart de aantal van de spelers van class inputData

                $game_Name =  $_SESSION["game"] ;

                $number_of_players = (int)  $number_of_players; 


                #Player + number ( zoals 'player1' of 'player2') zijn de namen van de input forms in reservePage.php

                #We gaan zorgen om deze nammen in een autmatch maneer te maken door een nummer tevogen aan 'player'

                #Daarna we gaan dit nammen elke keer in $players array toevogen zodat kunnen we alle players een keer toevogen in de database


                $num = 1; 

                for($i = 0 ; $i <  $number_of_players ; $i++){

                    $numString = (string) $num; 

                    $playerName = 'player' . $numString; 

                    $player = $_POST[$playerName];   #Hier verzamel we de naam van elke player die in de input forms heeft geschreven

                    array_push($players ,   $player);

                    $num++;


                }

                $players = implode("," , $players );
                $players = mysqli_real_escape_string($this->dataBase() ,  $players );
                

                $id = $_POST["id"];

                if($type == "update"){
                    $id = $id;
                    

                    $data = [ $game_Name , $person_who_explains_game , $startTime , $players , $userName ];
                    $coloumn = [ 'name' , 'makerName' , 'startTime' , 'players' , 'userName' ];
    
                    $num = 0;    
    
                    foreach($data as $value){
    
                        $nas =  $coloumn[$num];
                        
    
                    
                        $query = "UPDATE planning SET  $nas='$value' WHERE id='$id'";
                        $result = mysqli_query($this->dataBase() ,  $query);
    
                        if(!$result){
                            echo die('Query faild' .  mysqli_error($this->dataBase()));
                        }
    
                        $num++;
                    } 
                    
                    if($result){
                        echo "Goood! we recived your plan.";
                        header('Location: /../../game_alpha/pages/feedback_page.php');
                    }
                         
                }else{

                    $query = "INSERT INTO planning(name , makerName , startTime , players , userName) VALUES('$game_Name' , '$person_who_explains_game' , '$startTime' , '$players' , '$userName')";
                    $result = mysqli_query($this->dataBase() ,  $query);

                    if(!$result){
                        echo  die('Query faild' .  mysqli_error($this->dataBase()));
                    }else{

                        header('Location: /../../game_alpha/pages/feedback_page.php');
                    }
                }
            

            }
        }
    }

    public function read_games_details()
    {
        $game = $_GET['game']; 

        

        $query_game_details = "SELECT * FROM games WHERE name='$game'";
        $result_game_details = mysqli_query($this->dataBase() , $query_game_details );


        

        if($result_game_details){

            $data_game_details = mysqli_fetch_assoc($result_game_details);

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


          

            echo "<h2> $name_of_the_game </h2>" . '<br>';
            echo "<img src='/../../game/afbeeldingen/$image' alt='$name_of_the_game'>";
            echo "<br>" . $youtube . "<p> $description " . '<br>' . "<a href='$url'> to the game </a>" .  '<br>';
            echo "Expansions : $expansions" . '<br>' . "Skills : $skills " . '<br>' . "Min players : $min_players";
            echo  '<br>' . "Max players : $max_players" . '<br>' . " Explain minutes : '$explain_minutes'";
            echo '<br>' . "Play minutes : $play_minutes" . "</p>";

        }else{

            return die("Query faield!" . mysqli_error($this->dataBase()));
        }
    }

    public function read_game_plan(){

        $id = $_GET['id'] - 1;

        
        $query_planning_details = "SELECT * FROM planning WHERE id-'$id'";
        $result_planning_details = mysqli_query($this->dataBase() , $query_planning_details);

        if($result_planning_details){

            $data_game_planning = mysqli_fetch_assoc($result_planning_details);

            $explain_player = $data_game_planning['makerName'];
            $start_time = $data_game_planning['startTime'];
    
    
            $players = $data_game_planning['players'];
            $players = explode("," ,  $players);



           echo "<p> Game start at : $start_time " . "<br>"   . "Maker : $explain_player" . "<br>" ;
           echo "Players are : " ;
           foreach($players as $player){
                echo "<br>" . $player ;
           }
           echo "</p>";
            
        
    



        }else{

            return die("Query faield!" . mysqli_error($this->dataBase()));
        }

       



    }


    public function feedback_insert(){


        if($_SERVER["REQUEST_METHOD"] == "POST"){

            $userName = $_SESSION["user_name"] ;
            $type =  $_SESSION['type_for_feedback'] ;

            $feedBack = $_POST['feedback'];

            $feedBack = mysqli_real_escape_string($this->dataBase() ,   $feedBack  );


            $query =  "INSERT INTO feedback( username , feedback , type ) VALUES('$userName' , '$feedBack' , '$type')";
            
            $result = mysqli_query($this->dataBase() , $query);

            if($result){

                header('Location: /../../game_alpha/pages/personalPage.php');
            }else{

                echo die("Query failed! " . mysqli_error($this->dataBase()));
            }

        }
       



    }
}


    



?>