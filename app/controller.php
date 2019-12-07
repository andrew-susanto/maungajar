<?php 

class App{
    public function __construct()
    {
        $url=$this->parseURL();
        
        //controller
        if(file_exists('view/'.$url[0].'.php')){
            require_once 'view/'.$url[0].'.php';
            unset($url[0]);
        }
        else{
            require_once 'view/index.php';
        }

    }

    public function parseURL()
    {
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'],'/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/',$url);
            return $url;
        }
    }
}