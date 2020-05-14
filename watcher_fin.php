<?php
$q  = "piece1 check_query piece2 piece3 piece4 piece5 piece6 UNION OR 1=1";
$aa = "SELECT id, login FROM users WHERE pass='XCDS' OR 1=1";

function check_query($q, &$pars = []): bool
{
    $ret = true;
    $q_a = explode(" ", $q);
    $pars_a = is_array($pars) ? $pars : array($pars);
    $query_for_check = array_merge($q_a, $pars_a);
    $double_check = true;
    foreach ($query_for_check as $ch) {
        $check = stripos($ch, '=');
        if ($check != false) {
            $ch_a = explode("=", $ch);
            $check_double = array_unique($ch_a);
            if ($check_double !== $ch_a)
            {
                $double_check = false;
                //echo 'вхождение = есть' . PHP_EOL;
            }
        }
    }
    $stop_words = ['statistic:getViews', 'statistic:Tops'];
    $danger_words = array(
        "DROP", "ALTER", "SLEEP", "UNION",
        "LOAD_FILE", "--"
    );
    $report = array();
    foreach ($query_for_check as $word_check) {
        if (in_array($word_check, $danger_words) or $double_check == false) {
            $danger_events = debug_backtrace();
            foreach ($danger_events as $d_event) {
                foreach ($stop_words as $st_w)
                    $event = isset($d_event['class']) ? $d_event['class'] . ':' . $d_event['function'] : $d_event['function'];
                if ($event != $st_w and $ret!=false) {
                    if (isset($d_event['class'])) {
                    array_push($report, date("H-i") . ' ' . $d_event['file'] . ' ' . $d_event['line'] . ' '
                        . $d_event['class'] . ' ' . $d_event['function'] . ' ' . print_r($d_event['args'], 1));
                    } else {
                        array_push($report, date("H-i") . ' ' . $d_event['file'] . ' ' . $d_event['line'] .
                            ' ' . $d_event['function'] . ' ' . print_r($d_event['args'], 1));
                    }
                    $ret = false;
                    // toLog($report);
                } else {
                    unset($report);
                }
            }
        }
    }
    return $ret;
}
function toLog($report)
{
    //echo 'log' . PHP_EOL;
}
check_query($q, $q2);
