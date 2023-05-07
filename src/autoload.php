<?php
/**
 * Objetivo desse arquivo é fazer o carregamento automático das classes do php usando spl autoload
 * updated to php 8.1
 * Author Anderson Arruda < andmarruda@gmail.com >
 */
spl_autoload_register(function($className){
    $path = str_replace(['challenge\\', '\\'], ['', '\/'], $className);
    require_once $path.'.php';
});

/**
 * Função para escrever novas mensagens no log
 * @author  Anderson Arruda < andmarruda@gmail.com >
 * @version 1.0.0
 * @param
 * @return  void
 */
function writeLog(string $id, string $msg, string $file, int $line) : void
{
    $line = date('Y-m-dTH:i:s.v').'|File:'. $file.'|Line:'. $line. '|Error:'. $msg;
    file_put_contents(__DIR__.'/../storage/error.log', $line, FILE_APPEND);
}

/**
 * Adicionado tratamento de erros global para erros desconhecidos e inesperados, gerando um log num arquivo na pasta storage
 */
set_error_handler(function(string $id, string $msg, string $file, int $line){
    writeLog(...func_get_args());
    echo 'Um erro inesperado ocorreu!'; die;
});
?>