<?php

function bdconnect () {
        $hostname = "localhost"; 
        $username = "u0578759_default"; 
        $password = "b!QJy9fN"; 
        $dbName = "u0578759_koktail"; 
         
        $mysqli = new mysqli($hostname, $username, $password, $dbName);
        mysqli_query($mysqli,'SET NAMES utf8');
        return $mysqli;
}

// Роутер
function route($method, $urlData, $formData) {
    
    if ($method === 'ADD'&& count($formData)===6) {
        $name = $formData["name"];
        $consist = $formData["consist"];        
        $descrip = $formData["descrip"];
        $cook = $formData["cook"];
        $type = $formData["type"];
        $image = $formData["image"];
        
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,"SELECT * FROM koktail where Name ='".$name."';")or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        $result = mysqli_query($mysqli,"SELECT * FROM addKoktail where Name ='".$name."';")or die(mysqli_error($mysqli));
        $count1= mysqli_num_rows($result);
        if($count == 0&& $count1 == 0){
            
            $pieces = explode(",", $consist);
            for($i=0 ; $i<count($pieces); $i++){
                
                $con =  $con.$pieces[$i]." ---------------- ".$pieces[++$i]."<br>";
            }

            $query = "INSERT INTO `addKoktail`( `Name`, `consist`,`consistOrig`, `description`, `cooking`, `image`, `type`) VALUES ('".$name."', '".$con."', '".$consist."', '".$descrip."', '".$cook."', '".$image."', '".$type."')";
            mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
            echo json_encode(array (
                'message'=> "Отправлено на рассмотрение"
            ));
            
        }else {
            echo json_encode(array (
                'message'=> "Уже существует c таким именем",
            ));
        }
        return;
    }
    
    if ($method === 'GET') {
        $mysqli = bdconnect();
        
        $result = mysqli_query($mysqli,'SELECT * FROM addKoktail ORDER BY `addKoktail`.`Name` ASC;')or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        
        $arr = array();       
        for ($i=0;$i<$count;$i++){
            $koktail = mysqli_fetch_assoc($result);
               $tmp = array (
                   'name'=>$koktail["Name"],
                   'consist' =>$koktail["consist"],
                   'description' => $koktail["description"],
                   'cooking' => $koktail["cooking"],
                   'image' => $koktail["image"],
                   'id' => $koktail["id"]
                   );
                $arr[$i] = $tmp;
                unset($tmp); 
        }
        echo json_encode($arr);

        return;
    }
    
    if ($method === 'PUT'&& count($urlData)===1) {
        $id = $urlData[0];
        
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,"SELECT * FROM addKoktail where id ='".$id."';")or die(mysqli_error($mysqli));
        $koktail = mysqli_fetch_assoc($result);
        
        
        $query = "INSERT INTO `koktail`( `Name`, `consist`, `description`, `cooking`, `image`, `type`) VALUES ('".$koktail['Name']."', '".$koktail["consist"]."', '".$koktail["description"]."', '".$koktail["cooking"]."', '".$koktail["image"]."', '".$koktail["type"]."')";
        mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
        $pieces = explode(",", $koktail["consistOrig"]);
        $result = mysqli_query($mysqli,"SELECT * FROM koktail where Name ='".$koktail['Name']."';")or die(mysqli_error($mysqli));
        $koktail = mysqli_fetch_assoc($result);
        
        for($i=0 ; $i<count($pieces); $i++){
            $query = "INSERT INTO `Indigridient`(`IdKok`, `name`, `count`) VALUES ('".$koktail['id']."','".$pieces[$i]."','".$pieces[++$i]."')";            
            mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
        }
        
        mysqli_query($mysqli,"DELETE FROM addKoktail where id =".$id.";")or die(mysqli_error($mysqli));
        echo json_encode(array (
            'message'=> "Коктейль добавлен",
        ));
        return;
    }
    if ($method === 'DEL' && count($urlData) === 1) {
        $id = $urlData[0];
        $mysqli = bdconnect();

        mysqli_query($mysqli,"DELETE FROM addKoktail where id =".$id.";")or die(mysqli_error($mysqli));
        
        echo json_encode(array (
            'message'=>"Удалено"
        ));
        
        return;
    }
    

    // Возвращаем ошибку
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));

}
