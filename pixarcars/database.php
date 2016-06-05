<?php

/**************************************
 * 
 *  File: database.php
 *  Created by: jsimpson
 *  Date: May 18, 2016 7:01:44 PM
 *  Description: Database Access and functions
 * 
 ****************************************/


/**************************************
 * 
 *  Function:  openDB()
 *  Descript:   Generical Function to open DB local or otherwise
 * 
 ****************************************/

function openDB($dbname)
{

    $openShiftVar = getenv('OPENSHIFT_MYSQL_DB_HOST');

    if ($openShiftVar === null || $openShiftVar == "")
    {
        // Not in the openshift environment
        $servername = "localhost";
        $username = "php";
        $password = "php-login";
      
    }
    else 
    { 
        // In the openshift environment
        $servername = getenv('OPENSHIFT_MYSQL_DB_HOST');
        $username = getenv('OPENSHIFT_MYSQL_DB_USERNAME');
        $password = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');        
          
    } 

// Create connection
    try 
    {          
        $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
    } catch (Exception $ex) 
    {
        echo "Connection failed: " . $ex->getMessage();
        die();
    }

return $db;

}

function dbRead($db,$sqlQuery)
{

    //Build PDO statement and fetch it from DB
    $statement = $db->query($sqlQuery);         
    $dbInfo =  $statement->fetchAll(PDO::FETCH_ASSOC);
  
    return $dbInfo;
}

function updateDbRecord()
{
    $db = openDB("pixar_cars");
    
    $name =$_POST["name"];
    $description = $_POST["description"];
    $bought = $_POST['purchase'];
    $primary = $_POST['purchase'];
    $racecar = $_POST['race_car'];
    $racenum = $_POST['race_num'];
    $racesponsor = $_POST['race_sponsor'];
    
    $id = $_SESSION['currRecord'];
    
    
    if($primary === 'yes')
        $primary = 1;
    else
        $primary = 0;
    
    
     if($racecar === 'yes')
        $racecar = 1;
    else
        $racecar = 0;
     
    
    
    $stmt = $db->prepare("UPDATE `cars` SET `name`=:name, `description`=:descr,  `date_aquired`=:buy, 
        `primary_version`=:prim,  `race_car`=:rcar, `race_number`=:rnum, `race_sponsor`=:spon where id=:id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':descr', $description);
    $stmt->bindParam(':buy', $bought);
    $stmt->bindParam(':prim', $primary);
    $stmt->bindParam(':rcar', $racecar);
    $stmt->bindParam(':rnum', $racenum);
    $stmt->bindParam(':spon', $racesponsor);
    $stmt->bindParam(':id', $id);
   
    
    $flag = $stmt->execute();
    

    if ($flag)
    {
        $last_id = $db->lastInsertId();
    }
    else
    {
            print_r($db->errorInfo());
    }

  //take care of primary id
    
  //take care of friends
    
  //take care of locations
    
  //take care of no image  
}

function addDbRecord()
{
    $db = openDB("pixar_cars");
    
    $name =$_POST["name"];
    $description = $_POST["description"];
    $bought = $_POST['purchase'];
    $primary = $_POST['purchase'];
    $racecar = $_POST['race_car'];
    $racenum = $_POST['race_num'];
    $racesponsor = $_POST['race_sponsor'];
    
    $own = 1;
    
    if($primary === 'yes')
        $primary = 1;
    else
        $primary = 0;
    
    
     if($racecar === 'yes')
        $racecar = 1;
    else
        $racecar = 0;
     
    
    
    $stmt = $db->prepare("INSERT INTO `cars` (`name`, `description`, `owned`, `date_aquired`, `primary_version`,  `race_car`, `race_number`, `race_sponsor`)
    VALUES (:name, :descr, :own, :buy, :prim, :rcar, :rnum, :spon)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':descr', $description);
    $stmt->bindParam(':own', $own);
    $stmt->bindParam(':buy', $bought);
    $stmt->bindParam(':prim', $primary);
    $stmt->bindParam(':rcar', $racecar);
    $stmt->bindParam(':rnum', $racenum);
    $stmt->bindParam(':spon', $racesponsor);
   
    
    $flag = $stmt->execute();
    
    

//    if ($flag)
//    {
//        $last_id = $stmt->lastInsertId();
//    }
//    else
//    {
//            print_r($db->errorInfo());
//    }

  //take care of primary id
    if($primary == 1)
    {
         $stmt = "UPDATE `cars` SET primary_car_id = $last_id where id=$last_id";
         $stmt = $db->prepare($sql);
         $flag= $stmt->execute();
    }
//    
//    if ($flag)
//    {
//        
//        $last_id = $stmt->lastInsertId();
//    }
//    else
//    {
//       // print_r($db->errorInfo());
//    }
    
  //take care of friends
    
  //take care of locations
    
  //header("Location:cheat.php");
    
}


function deleteDbRecord()
{ 
    $db = openDB("pixar_cars");
    $carIndex = $_SESSION["currRecord"];  
      
    if(isset($carIndex))
    {
       $imageInfo = dbRead($db,"SELECT folderpath, name FROM image WHERE car_id=$carIndex"); 
       $imageFile = $imageInfo[0]["folderpath"] ."/" .$imageInfo[0]["name"];

       //delete image file
       if(file_exists ($imageFile ))
            unlink($imageFile);            
   
 
            //delete DB record 
            $sql = "DELETE FROM cars WHERE id=$carIndex";
            $db->exec($sql);

        
    }
    else
        echo "Error: No Record to get";
    
    //close db
    $db = null;
    
}


function getAllDbFriends()
{
    $db = openDB("pixar_cars");
    $friends = dbRead($db,"select id,name from cars where primary_version=1");
    
    $db = null;
    
    return $friends;
}


function getAllDbLocations()
{
    $db = openDB("pixar_cars");
    $items = dbRead($db,"select * from location");
    
    $db = null;
    
    return $items;
}

