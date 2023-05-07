<?php
/**
 * Objetivo dessa classe é fazer a leitura dos arquivos em formato String de múltiplas linhas e converter pra um formato de Matriz Array
 * updated to php 8.1
 * Author Anderson Arruda < andmarruda@gmail.com >
 */
class logReader{
    /**
     * Verifica se o sistema de log deve excluir a primeira linha
     * @var bool
     */
    private bool $excludeFirstRow = true;

    /**
     * Linhas tratadas
     * @var [[string => mixed]]
     */
    private array $data;

    /**
     * Inicia a leitura do arquivo de log e a conversão de dados
     * 
     */
    public function __construct(string $logPath)
    {
        if(!file_exists($logPath))
        {
            echo 'O arquivo '. $logPath. ' não existe!.';
            die;
        }

        if(!is_readable($logPath))
        {
            echo 'O arquivo '. $logPath. ' não está acessível devido a condições de permissão!.';
            die;
        }

        $this->translate($logPath);
    }

    /**
     * Filtra e traduz os dados
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   string $logPath
     * @return  void
     */
    private function translate(string $logPath) : void
    {
        $f = fopen($logPath, 'r');
        $this->data = [];
        while(($row=fgets($f))){
            if($this->excludeFirstRow && !preg_match('/^(\d{2}(:|\.)){3}\d{3}/', $row))
                continue;

            $cols = array_filter(explode(' ', $row), function($val){
                return $val !== '' && $val !== '-';
            });
            $this->data[] = $cols;
        }
        fclose($f);
    }

    /**
     * Retorna os dados traduzidos
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   string $name
     * @return  mixed
     */
    public function __get(string $name)
    {
        if(!property_exists(__CLASS__, $name))
            return null;

        return $this->$name;
    }
}
?>