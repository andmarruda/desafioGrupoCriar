<?php
/**
 * Objetivo dessa classe é solucionar o problema proposto para o teste da vaga de desenvolvedor do Grupo criar
 * A partir de um input de um arquivo log no formato do exemplo ../sampledata.log, montar o resultado com as seguintes informações: Posição de chegada, Código piloto,
 * Nome piloto, Qtde de voltas completadas e tempo
 * updated to php 8.1
 * Author Anderson Arruda < andmarruda@gmail.com >
 */

 namespace challenge;
 use \DateTime;

 class analytics{
    /**
     * Armazena informação da volta mais rápida da corrida
     * @var array
     */
    private array $bestLap = [];

    /**
     * Horário de término da corrida
     * @instanceof DateTime
     */
    private ?DateTime $raceEnd = NULL;

    /**
     * Horário de início da corrida
     * @instanceof DateTime
     */
    private ?DateTime $raceStart = NULL;

    /**
     * Separa e analisa dados dos corredores
     * @var array
     */
    private array $pilotoData = [];

    /**
     * Indexa os pilotos pelo id e finalizacao da prova
     * @var array
     */
    private array $endRacePilot = [];

    /**
     * Essa função tem o objetivo de receber os dados extraídos pelo logReader e iniciar o processo de busca e otimização dos dados
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   private array $data
     * @return  void
     */
    public function __construct(private array $data)
    {
        $this->prepare();
    }

    /**
     * Analisa qual foi a melhor volta
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   array $row
     * @return  void
     */
    public function checkBestLap(array $row) : void
    {
        try{
            if(count($this->bestLap) == 0 || $row[4]->format('Uv') < $this->bestLap[4]->format('Uv'))
                $this->bestLap = $row;
        } catch(\Exception $err){
            writeLog($err->getCode(), $err->getMessage(), $err->getFile(), $err->getLine());
            echo 'Erro no tratamento de formato de data e hora.'; die;
        }
    }
    
    /**
     * Verifica o início da corrida
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   array $row
     * @return  void
     */
    public function raceStartedAt(array $row) : void
    {
        if($row[3]!='1')
            return;
        
        try{
            if(is_null($this->raceStart) || $row[0]->getTimestamp() < $this->raceStart->getTimestamp())
                $this->raceStart = $row[0];
        } catch(\Exception $err){
            writeLog($err->getCode(), $err->getMessage(), $err->getFile(), $err->getLine());
            echo 'Erro no tratamento de formato de data e hora.'; die;
        }                
    }

    /**
     * Verifica o término da corrida
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   array $row
     * @return  void
     */
    public function raceEndedAt(array $row) : void
    {
        if($row[3]!=4)
            return;

        try{
            if(is_null($this->raceEnd) || $row[0]->getTimestamp() < $this->raceEnd->getTimestamp())
                $this->raceEnd = $row[0];

            $this->endRacePilot[$row[1]] = $row[0]->getTimestamp();
        } catch(\Exception $err){
            writeLog($err->getCode(), $err->getMessage(), $err->getFile(), $err->getLine());
            echo 'Erro no tratamento de formato de data e hora.'; die;
        }
    }

    /**
     * Extrai e separada dados dos pilotos
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @param   array $lap
     * @return  void
     */
    public function pilotoDataAnalyzer(array $lap) : void
    {
        $pilotData = &$this->pilotoData;
        try{
            if(!isset($pilotData[$lap[1]]))
            {
                $pilotData[$lap[1]] = [
                    'codigo'            => $lap[1],
                    'hora'              => [],
                    'nome'              => $lap[2],
                    'total_volta'       => $lap[3],
                    'tempo_voltas'      => [],
                    'velocidade_media'  => [],
                    'velocidade_soma'   => 0
                ];
            }
            $pilotData[$lap[1]]['hora'][$lap[3]] = $lap[0];
            $pilotData[$lap[1]]['total_volta'] = $pilotData[$lap[1]]['total_volta'] < $lap[3] ? $lap[3] : $pilotData[$lap[1]]['total_volta'];
            $pilotData[$lap[1]]['tempo_voltas'][$lap[3]] = $lap[4];
            $pilotData[$lap[1]]['velocidade_media'][$lap[3]] = $lap[5];
            $pilotData[$lap[1]]['velocidade_soma'] += $lap[5];
        } catch(\Exception $err){
            writeLog($err->getCode(), $err->getMessage(), $err->getFile(), $err->getLine());
            echo 'Erro no tratamento de formato de data e hora.'; die;
        } 
    }

    /**
     * Converte e organiza os dados no formato que é mais interessante para o tratamento e cálculo dos mesmos
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param
     * @return  void
     */
    private function prepare()
    {
        $obj = $this;
        $this->data = array_map(function($val) use($obj){
            $val = array_values($val);
            $val[0] = DateTime::createFromFormat('H:i:s.v', $val[0]);
            $val[3] = (int) $val[3];
            $val[4] = DateTime::createFromFormat('H:i:s.v', '21:'.str_pad($val[4], 9, '0', STR_PAD_LEFT));
            $val[5] = (float) str_replace(',', '.', $val[5]);

            $obj->checkBestLap($val); //checa melhor volta
            $obj->raceStartedAt($val); //checa início da corrida
            $obj->raceEndedAt($val); //checa o fim da corrida
            $obj->pilotoDataAnalyzer($val); //dados separados por piloto

            return $val;
        }, $this->data);
    }

    /**
     * Pega a melhor volta do piloto
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   string $code
     * @return  array
     */
    public function bestPilotLap(string $code) : array
    {
        if(!isset($this->pilotoData[$code])){
            echo 'Piloto com o código '. $code. ' não foi encontrado!'; die;
        }
        $bestTime=null;
        $bestLap = null;
        foreach($this->pilotoData[$code]['tempo_voltas'] as $lap => $time)
        {
            if(is_null($bestTime)){
                $bestTime = $time;
                $bestLap = $lap;
                continue;
            }

            if((int)$time->format('Uv') < (int)$bestTime->format('Uv')){
                $bestTime = $time;
                $bestLap = $lap;
            }
        }
        return ['lap'=> $bestLap, 'time' => $bestTime->format('i:s.v')];
    }

    /**
     * Pega a velocidade média da prova do piloto
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   string $code
     * @return  float
     */
    public function avgPilotSpeed(string $code) : float
    {
        if(!isset($this->pilotoData[$code])){
            echo 'Piloto com o código '. $code. ' não foi encontrado!';
            die;
        }

        return $this->pilotoData[$code]['velocidade_soma'] / $this->pilotoData[$code]['total_volta'];
    }

    /**
     * Retorna dados da melhor volta da corrida
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   
     * @return  array
     */
    public function getBestRaceLap() : array
    {
        return $this->bestLap;
    }

    /**
     * Retorna o tempo da prova até a chegada do primeiro colocado
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   
     * @return  string
     */
    public function getRaceFirstEnd()
    {
        $time=$this->raceEnd->diff($this->raceStart);
        return $time->format('%d dia(s) %H hora(s) %i minuto(s) %s segundo(s)');
    }

    /**
     * Retorna o tempo da prova do piloto
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   
     * @return  string
     */
    public function getRacePilotEnd(string $code)
    {
        $raceEnd = end($this->pilotoData[$code]['hora']);
        $raceStart = reset($this->pilotoData[$code]['hora']);
        $time=$raceEnd->diff($raceStart);
        return $time->format('%d dia(s) %H hora(s) %i minuto(s) %s segundo(s)');
    }

    /**
     * Retorna os dados dos pilotos  ordenados pela ordem de chegada
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param   
     * @return  array
     */
    function getOrderedData() : array
    {
        $pilotOrder = $this->endRacePilot;
        asort($pilotOrder);
        $ret = [];
        foreach($pilotOrder as $code => $tm)
        {
            $ret[$code] = $this->pilotoData[$code];
        }

        $indexedPilot = array_keys($this->endRacePilot);
        $existingPilot = array_keys($this->pilotoData);
        $missingPilot = array_diff($existingPilot, $indexedPilot);
        unset($indexedPilot, $existingPilot);
        foreach($missingPilot as $mp)
        {
            $ret[$mp] = $this->pilotoData[$mp];
        }

        return $ret;
    }

    /**
     * Calcula a distância entre os colocados
     * @author  Anderson Arruda < andmarruda@gmail.com >
     * @version 1.0.0
     * @param
     * @return  string
     */
    public function getDiff(DateTime $first, DateTime $actual) : string
    {
        $diff = $actual->diff($first);
        return $diff->format('%h:%i:%s');
    }
 }
?>