<?php

/**
 * Created by PhpStorm.
 * User: Ahereza
 * Date: 12/9/16
 * Time: 22:59
 */
class FirebasePush
{
    private $title;
    private $message;
    private $url;

    function __construct($message)
    {
        $this->message = $message;
        $this->title = "New Assignment";
        $this->url = 'https://fcm.googleapis.com/fcm/send';
    }

    private function getPush(){
        $res = array(
            'title' => $this->title,
            'body' => $this->message
        );
        return $res;

    }

    public function sendPushNotification()
    {
        $fields = array
        (
            'to' 	=> '/topics/assginment',
            'notification'			=> $this->getPush()
        );

        $headers = array
        (
            'Authorization: key=' . "AIzaSyA_WcsHwGxnKO7mThNuYaoXHhVoqBb4kiA",
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $this->url );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        echo $result;
    }



}