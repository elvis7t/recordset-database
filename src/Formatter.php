<?php

namespace ElvisLeite\RecordSetDatabase;

//sujeira embaixo do tapete :(
error_reporting(E_ALL & E_NOTICE & E_WARNING);
// require_once("../model/recordset.php");
class Formatter
{
    /**
     * Method responsible for count days
     * @param string $startday
     * @param string $finalday
     * @return int
     */
    public function setCountDays($startday, $finalday)
    {
        $difference = strtotime($finalday) - strtotime($startday);
        $days = floor($difference / (60 * 60 * 24));

        //RETURN NUMBER OF DAYS
        return $days;
    }

    /**
     * Method responsible for treating the date in Brazilian format
     * @param string $date
     * @return string
     */
    public static function setDataBr($date)
    {
        $arraydate = explode("-", $date);
        $formattedDate  = $arraydate[2] . "/" . $arraydate[1] . "/" . $arraydate[0];

        //RETURN DATE FORMATED
        return $formattedDate;
    }

    /**
     * Method responsible for treating the date in American format
     * @param string $date
     * @return string
     */
    public static function setDataUsa($date)
    {
        $arraydate = explode("/", $date);
        $formattedDate = $arraydate[2] . "-" . $arraydate[1] . "-" . $arraydate[0];

        //RETURN DATE FORMATED
        return $formattedDate;
    }

    /**
     * Method responsible for treating the date with time
     *
     * @param string $timedate
     * @return string
     */
    public static function setTimeDate($timedate)
    {

        $arraydate = explode(" ", $timedate);
        $fd = explode("-", $arraydate[0]);
        $formattedDate = $fd[2] . "/" . $fd[1] . "/" . $fd[0] . " &agrave;s " . $arraydate[1];

        //RETURN DATE FORMATED
        return $formattedDate;
    }

    /**
     * Method responsible for treating the date return month string
     * @param string $date
     * @return string
     */
    public static function setMonthformat($date)
    {
        $months = array("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
        list($day, $month, $year) = explode("/", $date);

        //RETURN DATE FORMATED
        return $day . " de " . $months[$month - 1] . " de " . $year;
    }
    
    /**
     * Method responsible for treating the string modey
     *
     * @param string $value
     * @return void
     */
    public function setMoneyFormat($value)
    {
        return "R$" . number_format($value, 2, ",", ".");
    }
}
