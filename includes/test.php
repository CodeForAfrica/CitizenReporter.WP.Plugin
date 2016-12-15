<?php
/**
 * Created by PhpStorm.
 * User: Ahereza
 * Date: 12/10/16
 * Time: 00:40
 */
require_once 'FirebasePush.php';

$response = array();

if($_SERVER['REQUEST_METHOD']=='POST'){
    //hecking the required params
    if(isset($_POST['message'])){
        $fire = new FirebasePush($_POST['message']);
        $fire->sendPushNotification();
    }
}