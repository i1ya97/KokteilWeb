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
    
    // Получение всей информации о пользователе
    if ($method === 'POST' && count($urlData) === 3) {
        
        $login = $urlData[0];
        $password = $urlData[1];
        $email = $urlData[2];
        
        $email = stripslashes($email);
        $email = htmlspecialchars($email);
        $email = trim($email);
        $password =md5($password);
        
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,'SELECT * FROM User Where Email="'.$email.'";')or die(mysqli_error($mysqli));
        
        $count= mysqli_num_rows($result);
        if($count==0){
            // Вытаскиваем общие данные о пользователе из базы...
            $query = "INSERT INTO User (Email,login,password,rol) VALUE ('".$email."', '".$login."', '".$password."',1)";
            
            mysqli_query($mysqli,$query) or die(mysqli_error($mysqli));
            
            $result = mysqli_query($mysqli,'SELECT * FROM User Where Email="'.$email.'";')or die(mysqli_error($mysqli));
            $user = mysqli_fetch_assoc($result);
            // Выводим ответ клиенту
            echo json_encode(array(
                'method' => 'POST',
                'id' => $user["id"],
                'rol'=> $user["rol"],
                'info' => array(
                    'email' => $email,
                    'name' => $login
                )
            ));
        }else {
            echo json_encode(array(
                'error' => 'Пользователь существует'
            ));
        }

        return;
    }


    if ($method === 'PUT' && count($urlData) === 2)  {
        // Получаем id товара
        $pas = $urlData[0];
        $email = $urlData[1];
        $password =md5($pas);
        
        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,'SELECT * FROM User Where Email="'.$email.'" and password= "'.$password.'";')or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        
        if($count==1){
            $user = mysqli_fetch_assoc($result);
            echo json_encode(array(
                'message'=> 'Вы успешно вошли',
                'method' => 'PUT',
                'id' => $user['id'],
                'rol' => $user['rol'],
                'login' => $user['login'],
                'email' => $email
            ));
        }else {
            echo json_encode(array(
                'error' => 'Вход не выполнен!
Повторите попытку.'
            ));
        }
        // Вытаскиваем общие данные о пользователе из базы...
        return;
    }

    // Возвращаем ошибку
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));

}
