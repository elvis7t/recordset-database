<?php
namespace WilliamCosta\DatabaseManager;
use \WilliamCosta\DatabaseManager\Recordset;
//sujeira embaixo do tapete :(
    error_reporting(E_ALL & E_NOTICE & E_WARNING);
    // require_once("../model/recordset.php");
    class functions extends recordset{
        var $link;
    
        function __construct(){
            $this->link = conecta();
            return $this->link;
        }
        function data_br($data){
            $arraydata = explode("-",$data);
            $novadata = $arraydata[2]."/".$arraydata[1]."/".$arraydata[0];
            return $novadata;
        }
        function data_usa($data){
            $arraydata = explode("/",$data);
            $novadata = $arraydata[2]."-".$arraydata[1]."-".$arraydata[0];
            return $novadata;
        }
        function data_hbr($data){
            $arraydata = explode(" ",$data);
            $dta = explode("-",$arraydata[0]);
            $novadata = $dta[2]."/".$dta[1]."/".$dta[0] ." &agrave;s ".$arraydata[1] ;
            return $novadata;
        }
        function is_set($variavel){//Verifica se está setado e se valor != null
            if( (isset($variavel)) AND ((!empty($variavel)) OR (is_null($variavel))) ) {
                return true;
            }
            else{ return false;}
        }
        function calc_dh($valor, $valor2 = 0){
            /* Parte 1 - Calcular mkTime da data passaa via parametro*/
            date_default_timezone_set('America/Sao_Paulo');
    
            $dt = date("Y-m-d" , strtotime($valor));
            $hr = date("H:i:s", strtotime($valor));
            list($y, $m, $d) = explode("-",$dt);
            list($h, $i, $s) = explode(":",$hr);
            $dpc = mktime($h,$i,$s,$m,$d,$y);
            /* Parte 2 - Calcular a data de agora */
            if($valor2<>0){
                $dt2 = date("Y-m-d" , strtotime($valor2));
                $hr2 = date("H:i:s", strtotime($valor2));
                list($y2, $m2, $d2) = explode("-",$dt2);
                list($h2, $i2, $s2) = explode(":",$hr2);
                $dpc2 = mktime($h2,$i2,$s2,$m2,$d2,$y2); 
                $agr = $dpc2;
            }
            else{	
                $agr = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
            }
            /* Parte 3 - Calcular a diferença */
            $difer = $agr - $dpc;
            /* Parte 4 - Verificar o tempo em minutos / horas / dias / data*/
            switch($difer){
                case ($difer<=60) :
                    if($valor2<>0){$msg = $difer." segundos";}
                    else{$msg = "- de 1 min";}
                    break;
                case ($difer>60 AND $difer<=120):
                    $msg = number_format(($difer/60),0) . " min";
                    break;
                    
                case ($difer>120 AND $difer<=3600):
                    $msg = number_format(($difer/60),0) . " min";
                    break;
                case ($difer>3600 AND $difer<=86400):
                    $msg = number_format(($difer/3600),0) . " hrs";
                    break;
                case ($difer >86400):
                    $msg = number_format(($difer/86400),0) ." dias";
                    break;
            }
            return $msg;
        }
        function mes_extenso($data){
            $meses = array("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
            list($dia, $mes,$ano) = explode("/",$data);
            return $dia." de ".$meses[$mes-1]." de ".$ano;
        }
        
        function valorPorExtenso( $valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false ){
     
            //$valor = self::removerFormatacaoNumero( $valor );
     
            $singular = null;
            $plural = null;
     
            if ( $bolExibirMoeda )
            {
                $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
                $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
            }
            else
            {
                $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
                $plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
            }
     
            $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
            $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
            $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
            $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
     
     
            if ( $bolPalavraFeminina )
            {
     
                if ($valor == 1) 
                {
                    $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
                }
                else 
                {
                    $u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
                }
     
     
                $c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");
     
     
            }
     
     
            $z = 0;
     
            $valor = number_format( $valor, 2, ".", "." );
            $inteiro = explode( ".", $valor );
     
            for ( $i = 0; $i < count( $inteiro ); $i++ ) 
            {
                for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ ) 
                {
                    $inteiro[$i] = "0" . $inteiro[$i];
                }
            }
     
            // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
            $rt = null;
            $fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
            for ( $i = 0; $i < count( $inteiro ); $i++ )
            {
                $valor = $inteiro[$i];
                $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
                $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
                $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
     
                $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
                $t = count( $inteiro ) - 1 - $i;
                $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
                if ( $valor == "000")
                    $z++;
                elseif ( $z > 0 )
                    $z--;
     
                if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
                    $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
     
                if ( $r )
                    $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
            }
     
            $rt = mb_substr( $rt, 1 );
     
            return($rt ? trim( $rt ) : "zero");
     
        }
        function sanitize($dado){
            $dado = preg_replace("/[^0-9]/","",$dado);
            return $dado;
        }
        function ultimo_dia_mes($ref){
            list($mes,$ano) = explode("/",$ref);
            $ultimo = date("Y-m-t", mktime(0, 0, 0, $mes, 1, $ano));
            $semana = date("w", strtotime($ultimo));
            list($a, $m, $d) = explode("-",$ultimo);
            if($semana==0){$semana =7;}
            while($semana > 5){
                $d--;
                $semana--;
            }
            return $d."/".$ref;		
        }
    
        function dia_util($dia, $tipo){
            $mes = date("m");
            $ano = date("Y");
            $ultimo = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano));
            $semana = date("w", strtotime($ultimo));
            list($a, $m, $d) = explode("-",$ultimo);
            if($semana==0){$semana = 7;}
            if($tipo == "dia_util"){
                while($semana > 5){
                    $d--;
                    $semana--;
                }
            }if($tipo == "postpone"){
                while($semana > 5 OR $semana < 1){
                    $d++;
                    $ultimo = date("Y-m-d", mktime(0, 0, 0, $mes, $d, $ano));
                    $semana = date("w", strtotime($ultimo));
                }
            }
            $ultimo = date("Y-m-d", mktime(0, 0, 0, $mes, $d, $ano));
            return $ultimo;		
        }
    
        function hora_decimal($hora){
            list($h, $m, $s) = explode(":",$hora);
            $dec = ($h*60)+$m+($s/60);
            return $dec;
        }
    
    
        function DiaDaSemana($date) {
            return date('w', strtotime($date));
        }
    
        function getFeed($feed_url) {
         
            $content = file_get_contents($feed_url);
            $x = new SimpleXmlElement($content);
             
            /*echo "<ul>";
             
            foreach($x->channel->item as $entry) {
                echo "<li><a href='$entry->link' title='$entry->title'>" . $entry->title . "</a></li>";
            }
            echo "</ul>";
            */
            echo htmlentities($x->channel->item->description);
        }
        function Audit($tabela, $param, $dados, $emp, $user){
    
            $this->Seleciona("*",$tabela,$param);
            $this->GeraDados();
            $log = "Alterações em $tabela: <br>";
    
            foreach($dados as $i=>$v){
                if($v <> $this->fld($i)){
                    $log.="<b>".$i." de </b>".$this->fld($i)."<b> para </b>".$v."<br>";
                }
            }
            $dlog = array();
            $dlog['log_cod'] = $emp;
            $dlog['log_altera'] = $log;
            $dlog['log_user'] = $user;
            $dlog['log_data'] = date("Y-m-d h:i:s");
            $this->Insere($dlog,"logs_altera");
        }
        private function eFeriado(DateTime $data,$feriados){
            foreach ($feriados as $dia){
                if($data->format('Ymd')==$dia['data']){return true;}
            }
            return false;
        }
        /**
         * Cria um objeto DateTime com a data informada no formato d/m/Y.
         * @param String $data Data no formato d/m/Y
         * @return DateTime Objeto com a data informada.
         */
        public static function converterData($data) {
            if (preg_match('/[0-9]*\/[0-9]*\/[0-9]*/', $data)) {
                $t = explode('/', $data);
            } else {
                $t = Array('00', '00', '00');
            }
            return new DateTime($t[2] . '-' . $t[1] . '-' . $t[0]);
        }
     
         function formata_din($num){
            return "R$".number_format($num,2,",",".");
        } 
        /**
         * Calcula a diferença em horas comerciais entre a primeira e a segunda data passadas como parâmetro
         * @param DateTime $data1 Data inicial
         * @param DateTime $data2 Data final
         * @param DateTime $inicio Hora de inicio do horário comercial, padrão = 8
         * @param DateTime $fim Hora de término do horário comercial, padrão = 18
         * @return Array Array com horas e minutos
         */
        public static function horasUteis(DateTime $start, DateTime $end, $feriados = Array(), $inicio = '7:30', $fim = '17:00') {
            $step = $start;
            $seguinte = $start;
            $horas = 0;
     
            $hora_inicio = strtotime($step->format('Y-m-d') . ' ' . $inicio);
            while ($step <= $end) {
                // Hora inicial e final no dia atual
                $hora_inicio = strtotime($step->format('Y-m-d') . ' ' . $inicio);
                $hora_fim = strtotime($step->format('Y-m-d') . ' ' . $fim);
     
                // Se a hora atual estiver dentro do horario comercial
                // E o dia não for domingo
                if (($step->format('U') < $hora_fim)&&($step->format("w")!=0) || ($step->format("w")!=6)) {
                    if ($step->format('U') >= $hora_inicio) {
                        $inicial = $step->format('U');
                        $step = new DateTime($step->format('Y-m-d'));
                    }else{
                        $inicial = $hora_inicio;
                    }
                    // Se a hora estiver abaixo do horário comercial
                    if ($step->format('U') < $hora_fim) {
                        if (strtotime($end->format('y-m-d')) == strtotime($step->format('Y-m-d'))) {
                            if($end->format('U') > $hora_inicio){
                            $final = $end->format('U');
                            }else{
                                $final = $hora_inicio;
                            }
                        } else {
                            $final = $hora_fim;
                        }
                    }
                    if ($final > $hora_fim) {
                        $horas += ( $hora_fim - $inicial);
                    } else {
                        $horas += ( $final - $inicial);
                    }
                }else{
                        $step = new DateTime($step->format('Y-m-d'));
                }
                $step->modify('+1 day');
            }
            $horas = $horas / 3600;
            $min = ($horas - (int) $horas) * 60;
            $horas = (int) $horas;
            $retorno = array('h' => $horas, 'm' => $min);
            return $retorno;
        }
        
    }