<?php

namespace VitesseCms\Core\Utils;

/**
 * Class TimerUtil
 */
class TimerUtil
{
    /**
     * @var array
     */
    protected static $start = [];

    /**
     * @param string $key
     */
    public static function start(string $key = 'timer')
    {
        self::$start[$key] = microtime(true);
    }

    /**
     * @param string $key
     */
    public static function stop(string $key = 'timer')
    {
        self::$start[$key] = round((microtime(true) - self::$start[$key]), 4);
    }

    /**
     * @param bool $bSendmail
     *
     * @return string
     */
    public static function Results( bool $sendmail = true )
    {
        if(
            count(self::$start) > 0
            && $_SERVER['REMOTE_ADDR'] == '145.53.211.29'
        ) :
            arsort(self::$start);
            $r = '<table style="border-collapse: collapse;position:fixed;left:0px;bottom:0px;background:#fff;z-index:10000;color:#000" cellpadding="10" border="1">';
            $r .= '<tr>
                          <th>Description</th>
                          <th>Time (s)</th>
                    </tr>';
            $totaal = 0;
            foreach(self::$start as $k => $v) :
                $r .= '<tr><td>'.htmlspecialchars($k).'</td><td>'.$v.'</td></tr>';
                $totaal += $v;
            endforeach;
            //$r .=  '<tr><td>totaal</td><td>'.$totaal.'</td></tr>';
            $r .=  '</table>';
            if( $sendmail ) :
                if ( $totaal > 5 ) :
                    mail('jasper@biernavigatie.nl','Website application timer : '.$totaal,$_SERVER['REQUEST_URI']);
                endif;
            else :
                return $r;
            endif;
        endif;

        return '';
    }
}
