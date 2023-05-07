<?php
/**
 * Objetivo dessa classe é solucionar o problema proposto para o teste da vaga de desenvolvedor do Grupo criar
 * A partir de um input de um arquivo log no formato do exemplo ../sampledata.log, montar o resultado com as seguintes informações: Posição de chegada, Código piloto,
 * Nome piloto, Qtde de voltas completadas e tempo
 * updated to php 8.1
 * Author Anderson Arruda < andmarruda@gmail.com >
 */

 namespace challenge;

use logReader;

 class race{
    /**
     * Instância do leitor de log
     * @var instanceof \challenge\logReader
     */
    private logReader $reader;

    /**
     * Instância do sistema de análise e organização dos dados
     * @var instanceof \challenge\logReader
     */
    private analytics $analytics;

    /**
     * Essa função tem o objetivo de receber o caminho para o arquivo e encaminhar para o sistema de tratamento de dados
     * que por sua vez irá retornar uma array com os dados, em seguida passará por análises analíticas para então a apresentação do
     * resultado final
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   string $logPath
     * @return  void
     */
    public function __construct(string $logPath)
    {
        $this->reader = new logReader($logPath);
        $this->analytics = new analytics($this->reader->data);
    }

    /**
     * Retorna dados da função analytics
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   
     * @return  instanceof analytics
     */
    public function getAnalytics() : analytics
    {
        return $this->analytics;
    }
 }


        //calcular a diferença para achar o tempo que o piloto esteve na pista
        //achar o piloto campeão
        //melhor tempo do piloto
        //ordenar piloto pela ordem de chegada
?>