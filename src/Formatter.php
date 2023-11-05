<?php

namespace ElvisLeite\RecordSetDatabase;

class Formatter
{
    /**
     * Method responsible for count days
     * @param string $startday
     * @param string $finalday
     * @return int
     */
    public static function setCountDays(string $startday, string $finalday): int
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
    public static function setDataBr(string $date): string
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
    public static function setDataUsa(string $date): string
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
    public static function setTimeDate(string $timedate): string
    {
        $arraydate = explode(" ", $timedate);
        $fd = explode("-", $arraydate[0]);
        $formattedDate = $fd[2] . "/" . $fd[1] . "/" . $fd[0] . " às " . $arraydate[1];

        // Retorna a data formatada
        return $formattedDate;
    }

    /**
     * Method responsible for treating the date return month string
     * @param string $date
     * @return string
     */
    public static function setMonthformat(string $date): string
    {
        $months = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        // Parse a data e hora no formato "Y-m-d H:i:s" (Ano-Mês-Dia Hora:Minuto:Segundo)
        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return "Data e hora inválidas";
        }

        $day = date("d", $timestamp);
        $month = date("n", $timestamp);
        $year = date("Y", $timestamp);
        $hour = date("H", $timestamp);
        $minute = date("i", $timestamp);

        return $day . " de " . $months[$month - 1] . " de " . $year;
    }



    /**
     * Method responsible for treating the string modey
     *
     * @param string $value
     * @return string
     */
    public static function setMoneyFormat($value): string
    {
        return "R$" . number_format($value, 2, ",", ".");
    }

    public static function setDateTimeFormat(string $dateTime): string
    {
        $months = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        // Parse a data e hora no formato "Y-m-d H:i:s" (Ano-Mês-Dia Hora:Minuto:Segundo)
        $timestamp = strtotime($dateTime);

        if ($timestamp === false) {
            return "Data e hora inválidas";
        }

        $day = date("d", $timestamp);
        $month = date("n", $timestamp);
        $year = date("Y", $timestamp);
        $hour = date("H", $timestamp);
        $minute = date("i", $timestamp);

        return $day . " de " . $months[$month - 1] . " de " . $year . " às " . $hour . ":" . $minute;
    }
}
