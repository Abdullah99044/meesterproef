<?php 

require 'C:\Program Files\ammps2\Ampps\www\meesterproef\classes\plansControl.class.php';

class PlansInsert extends App {

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

                if($this->check_connection()){
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
                                return die('Query faild' .  mysqli_error($this->dataBase()));
                            }
        
                            $num++;
                        } 
                        
                        if($result){
                            
                            return header('Location: /../../meesterproef/pages/feedback_page.php?type=update');

                        }else{

                            return  die('Query faild' .  mysqli_error($this->dataBase()));

                        }
                            
                    }else{

                        $query = "INSERT INTO planning(name , makerName , startTime , players , userName) VALUES('$game_Name' , '$person_who_explains_game' , '$startTime' , '$players' , '$userName')";
                        $result = mysqli_query($this->dataBase() ,  $query);

                        if(!$result){

                            return  die('Query faild' .  mysqli_error($this->dataBase()));

                        }else{

                            return header('Location: /../../meesterproef/pages/feedback_page.php?type=update');

                        }
                    }
                }else{

                    return $this->check_connection();
                }
            }
        }
    }
}















?>