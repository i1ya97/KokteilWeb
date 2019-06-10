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
    
    if ($method === 'GET' && count($urlData) === 1) {
        $type = $urlData[0];

        $mysqli = bdconnect();
        if($type == "alc"){
            $result = mysqli_query($mysqli,'SELECT * FROM koktail where type ="alc"  ORDER BY `koktail`.`count` DESC, `koktail`.`Name` ASC;')or die(mysqli_error($mysqli));
        }else {
            $result = mysqli_query($mysqli,'SELECT * FROM koktail where type ="bezalc"  ORDER BY `koktail`.`count` DESC, `koktail`.`Name` ASC;')or die(mysqli_error($mysqli));
        }
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
    
    if ($method === 'SER') {
        $param = "%".$urlData[0]."%";
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,"SELECT * FROM koktail WHERE Name LIKE '".$param."'  ORDER BY `koktail`.`count` DESC, `koktail`.`Name` ASC;")or die(mysqli_error($mysqli));
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
    if ($method === 'GET' && count($urlData) === 2) {

        $name = $urlData[1];
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,'SELECT * FROM koktail where Name ="'.$name.'";')or die(mysqli_error($mysqli));
        $koktail = mysqli_fetch_assoc($result);
        // Выводим ответ клиенту
        echo json_encode(array (
            'id'=> $koktail["id"],
            'name'=>$koktail["Name"],
            'consist' =>$koktail["consist"],
            'description' => $koktail["description"],
            'cooking' => $koktail["cooking"],
            'image' => $koktail["image"]
        ));
        
        return;
    }
    if ($method === 'ONE') {
        $count = $urlData[0];
        $arr = array();  
        for ($i=0;$i<$count;$i++){
            $a = $i+1;
            $id = $urlData[$a];
            $mysqli = bdconnect();
            $result = mysqli_query($mysqli,'SELECT * FROM koktail where id ="'.$id.'";')or die(mysqli_error($mysqli));
            $koktail = mysqli_fetch_assoc($result);
               $tmp = array (
                   'id'=> $koktail["id"],
                   'name'=>$koktail["Name"],
                   'consist' =>$koktail["consist"],
                   'description' => $koktail["description"],
                   'cooking' => $koktail["cooking"],
                   'image' => $koktail["image"]
                   );
                $arr[$i] = $tmp;
                unset($tmp); 
        }
        echo json_encode($arr);

        return;
    }
    if ($method === 'PUT' && count($urlData) === 2) {

        $name = $urlData[0];
        $id = $urlData[1];
        $mysqli = bdconnect();
        //$result = mysqli_query($mysqli,'SELECT * FROM koktail where Name ="'.$name.'";')or die(mysqli_error($mysqli));
        //$koktail = mysqli_fetch_assoc($result);
        $result = mysqli_query($mysqli,"SELECT * FROM zakladki where idUser =".$id." and idKok=".$name.";")or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        if($count == 0){
            $query = "INSERT INTO zakladki (idUser,idKok) VALUE ('".$id."', '".$name."')";
            mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
            $result = mysqli_query($mysqli,"SELECT * FROM koktail where id ='".$name."';")or die(mysqli_error($mysqli));
            $koktail = mysqli_fetch_assoc($result);
            $coun = $koktail["count"]+1;
            mysqli_query($mysqli,'UPDATE `koktail` SET `count`='.$coun.' WHERE `id`="'.$name.'";')or die(mysqli_error($mysqli));
            echo json_encode(array (
                'message'=>"Добавлено в закладки"
        ));
        }else {
            echo json_encode(array (
                'message'=>"Уже было добавлено"
        ));
        }
        
        return;
    }
    
    if ($method === 'PUT' && count($urlData) === 1) {
        $id = $urlData[0];

        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,'SELECT * FROM zakladki where idUser ="'.$id.'";')or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        $arr = array();       
        for ($i=0;$i<$count;$i++){
            $kok = mysqli_fetch_assoc($result);
            $resul = mysqli_query($mysqli,'SELECT * FROM koktail where id ="'.$kok['idKok'].'";')or die(mysqli_error($mysqli));
            $koktail = mysqli_fetch_assoc($resul);
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
    if ($method === 'DELETE' && count($urlData) === 2) {

        $name = $urlData[0];
        $id = $urlData[1];
        $mysqli = bdconnect();
       // $result = mysqli_query($mysqli,'SELECT * FROM koktail where id ="'.$name.'";')or die(mysqli_error($mysqli));
      //  $koktail = mysqli_fetch_assoc($result);
        mysqli_query($mysqli,"DELETE FROM zakladki where idUser =".$id." and idKok=".$name.";")or die(mysqli_error($mysqli));
        echo json_encode(array (
            'message'=>"Удалено"
        ));
        
        return;
    }
    if ($method === 'POST'&& count($formData)===6) {
        $name = $formData["name"];
        $consist = $formData["consist"];        
        $descrip = $formData["descrip"];
        $cook = $formData["cook"];
        $type = $formData["type"];
        $image = $formData["image"];
        
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,"SELECT * FROM koktail where Name ='".$name."';")or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        if($count == 0){
            
            $pieces = explode(",", $consist);
            for($i=0 ; $i<count($pieces); $i++){
                
                $con =  $con.$pieces[$i]." ---------------- ".$pieces[++$i]."<br>";
            }

            $query = "INSERT INTO `koktail`( `Name`, `consist`, `description`, `cooking`, `image`, `type`) VALUES ('".$name."', '".$con."', '".$descrip."', '".$cook."', '".$image."', '".$type."')";
            mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
            $result = mysqli_query($mysqli,"SELECT * FROM koktail where Name ='".$name."';")or die(mysqli_error($mysqli));
            $koktail = mysqli_fetch_assoc($result);
            for($i=0 ; $i<count($pieces); $i++){
                $query = "INSERT INTO `Indigridient`(`IdKok`, `name`, `count`) VALUES ('".$koktail['id']."','".$pieces[$i]."','".$pieces[++$i]."')";            
                mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
            }
            echo json_encode(array (
                'message'=> "Добавлено"
            ));
            
        }else {
            echo json_encode(array (
                'message'=> "Уже существует",
            ));
        }
        return;
    }
    if ($method === 'TOP') {
        $count = count($urlData);
        $indig;
        for($i=0;$i<$count;$i++){
            $a = $count-1;
            if($i==$a){
                $indig = $indig."'".$urlData[$i]."'";
            }else {
                $indig = $indig."'".$urlData[$i]."',";
            }
        }
        $query ="SELECT `IdKok`,COUNT(*) FROM `Indigridient` WHERE `name` in (".$indig.") GROUP BY `IdKok`";
        //SELECT `IdKok`,COUNT(*) FROM `Indigridient` WHERE `name` in ('Тоник','Лайм','Лед') GROUP BY `IdKok`
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        if($count<3){
            $mass = array (
            'count'=> $count
            );
            for($i=1;$i<=$count;$i++){
                $koktail = mysqli_fetch_assoc($result);
                $mass[$i] = array('id'=> $koktail["IdKok"]);
            }
        }else {
            $mass = array (
            'count'=> 3
            );
            for($i=1;$i<=3;$i++){
                $koktail = mysqli_fetch_assoc($result);
                $mass[$i] = array('id'=> $koktail["IdKok"]);
            }
        }
        echo json_encode($mass);
        
        return;
    }

    // Возвращаем ошибку
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));

}
