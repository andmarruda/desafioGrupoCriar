<?php
/**
 * Objetivo dessa classe é recepcionar o arquivo enviado verificar sua segurança e informar onde o mesmo foi armazenado para o intermediário
 * updated to php 8.1
 * Author Anderson Arruda < andmarruda@gmail.com >
 */

 class upload{
    /**
     * Extensão aceita por esse projeto
     * @var array
     */
    private array $accept = ['log', 'txt'];

    /**
     * Armazena o último erro ao enviar o upload
     * @var string
     */
    private string $lastError;

    /**
     * Arquivo enviado
     * @var array
     */
    private array $file;

    /**
     * o novo nome do arquivo
     * @var string
     */
    private string $filename;

    /**
     * Aqui armazeno os erros possíveis no upload e suas respectivas informações
     * @var array
     */
    private array $errors = [
        UPLOAD_ERR_INI_SIZE => 'O arquivo é muito grande.',
        UPLOAD_ERR_FORM_SIZE => 'O arquivo é muito grande.',
        UPLOAD_ERR_PARTIAL => 'O arquivo foi corrompido no upload, por favor tente novamente.',
        UPLOAD_ERR_NO_FILE => 'Por favor envie um arquivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Configuração do servidor errada. Pasta temporária não existe',
        UPLOAD_ERR_CANT_WRITE => 'Não foi possível armazenar o arquivo. Falha na escrita no disco',
    ];

    /**
     * Recebe o arquivo para o inicio de seu tratamento e verificações
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   array $file
     * @return  bool
     */
    public function verify(array $file) : bool
    {
        if($file['error'] != 0){
            $this->lastError = $this->errors[$file['error']];
            return false;
        }

        $extension = preg_replace('/.*\.(?=[a-zA-Z0-9]{2,})/', '', $file['name']);
        if(!in_array($extension, $this->accept)){
            $this->lastError = 'A extensão do arquivo não é válida.';
            return false;
        }

        $this->file = $file;
        $this->filename = microtime(true). '.'. $extension;
        return true;
    }

    /**
     * Retorna o último erro que aconteceu
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   
     * @return  string
     */
    public function getLastError() : string
    {
        return $this->lastError ?? '';
    }

    /**
     * Retorna o último erro que aconteceu
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   
     * @return  string
     */
    public function getFilename() : string
    {
        return $this->filename ?? '';
    }

    /**
     * Armazena o arquivo enviado no caminho escolhido
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   string $path
     * @return  bool
     */
    public function store(string $path) : bool
    {
        if(!is_dir($path) || !is_readable($path)){
            $this->lastError = 'O caminho '. $path. ' não é um diretório ou não é possível lê-lo.';
            return false;
        }

        if(!is_writable($path)){
            $this->lastError = 'O caminho '. $path. ' não têm permissão de escrita, não será possível armazenar o arquivo.';
            return false;
        }

        if(!move_uploaded_file($this->file['tmp_name'], $path. '/'. $this->filename)){
            $this->lastError = 'Erro inesperado ao armazenar o arquivo!';
            return false;
        }
        return true;
    }
 }
?>