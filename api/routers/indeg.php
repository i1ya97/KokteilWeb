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
    
    if ($method === 'GET' && count($urlData) === 0) {

        $mysqli = bdconnect();
        $result = mysqli_query($mysqli,'SELECT Name FROM `Indigridient` GROUP BY `name` ORDER BY `name`')or die(mysqli_error($mysqli));
        $count= mysqli_num_rows($result);
        $arr = array();       
        for ($i=0;$i<$count;$i++){
            $indeg = mysqli_fetch_assoc($result);
               $tmp = array (
                   'name'=>$indeg["Name"],
                   );
                $arr[$i] = $tmp;
                unset($tmp); 
        }
        echo json_encode($arr);

        return;
    }

    // Возвращаем ошибку
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(array(
        'error' => 'Bad Request'
    ));

}
