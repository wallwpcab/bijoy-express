<?php

function getEnvData($key){
    //LOAD ENV
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
    $dotenv->load();
    return getenv($key);
}

function app_name(){
    return getEnvData('APP_NAME');
}

function app_url(){
    return getEnvData('APP_URL');
}

