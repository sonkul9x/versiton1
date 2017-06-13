<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Added 12/05/14 Dungnm
 */
if(!function_exists('get_online'))
{
    function get_online()
    {
        set_online();
        $result = array(
            'online_now'        => check_online(),
            'online_today'      => check_online_today(),
            'online_yesterday'  => check_online_yesterday(),
            'online_total'      => check_online_total(),
            'online_avg'        => check_online_avg(),
        );
        return $result;
    }
}
if(!function_exists('set_online'))
{
    function set_online()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $file_ip = fopen('./cache/ip.txt', 'rb');
        while (!feof($file_ip)) $line[]=fgets($file_ip,1024);
        for ($i=0; $i<(count($line)); $i++) {
            list($ip_x) = split("\n",$line[$i]);
            if ($ip == $ip_x) {$found = 1;}
        }
        fclose($file_ip);

        if (!($found==1)) {
            $file_ip2 = fopen('./cache/ip.txt', 'ab');
            $line = "$ip\n";
            fwrite($file_ip2, $line, strlen($line));
            $file_count = fopen('./cache/count.txt', 'rb');
            $data = '';
            while (!feof($file_count)) $data .= fread($file_count, 4096);
            fclose($file_count);
            list($today, $yesterday, $total, $date, $days) = split("%", $data);
            if ($date == date("Y m d")) $today++;
            else {
                $yesterday = $today;
                $today = 1;
                $days++;
                $date = date("Y m d");
            }
            $total++;
            $line = "$today%$yesterday%$total%$date%$days";

            $file_count2 = fopen('./cache/count.txt', 'wb');
            fwrite($file_count2, $line, strlen($line));
            fclose($file_count2);
            fclose($file_ip2);
          }
          return TRUE;
    }
}
if(!function_exists('check_online'))
{
    function check_online()
    {
        $rip = $_SERVER['REMOTE_ADDR'];
        $sd = time();
        $count = 1;
        $maxu = 1;

        $file1 = "./cache/online.log";
        $lines = file($file1);
        $line2 = "";

        foreach ($lines as $line_num => $line)
        {
            if($line_num == 0)
            {
                $maxu = $line;
            }
            else
            {
                $fp = strpos($line,'****');
                $nam = substr($line,0,$fp);
                $sp = strpos($line,'++++');
                $val = substr($line,$fp+4,$sp-($fp+4));
                $diff = $sd-$val;

                if($diff < 300 && $nam != $rip)
                {
                    $count = $count+1;
                    $line2 = $line2.$line;
                }
            }
        }

        $my = $rip."****".$sd."++++\n";
        if($count > $maxu)
        $maxu = $count;

        $open1 = fopen($file1, "w");
        fwrite($open1,"$maxu\n");
        fwrite($open1,"$line2");
        fwrite($open1,"$my");
        fclose($open1);
        $count=$count;
        $maxu=$maxu+200;

        return $count;
    }
}
if(!function_exists('check_online_today'))
{
    function check_online_today()
    {
        $file_count = fopen('./cache/count.txt', 'rb');
        $data = '';
        while (!feof($file_count)) $data .= fread($file_count, 4096);
        fclose($file_count);
        list($today, $yesterday, $total, $date, $days) = split("%", $data);
        return $today;
    }
}
if(!function_exists('check_online_yesterday'))
{
    function check_online_yesterday()
    {
        $file_count = fopen('./cache/count.txt', 'rb');
        $data = '';
        while (!feof($file_count)) $data .= fread($file_count, 4096);
        fclose($file_count);
        list($today, $yesterday, $total, $date, $days) = split("%", $data);
        return $yesterday;
    }
}
if(!function_exists('check_online_total'))
{
    function check_online_total()
    {
        $file_count = fopen('./cache/count.txt', 'rb');
        $data = '';
        while (!feof($file_count)) $data .= fread($file_count, 4096);
        fclose($file_count);
        list($today, $yesterday, $total, $date, $days) = split("%", $data);
        return $total;
    }
}
if(!function_exists('check_online_avg'))
{
    function check_online_avg()
    {
        $file_count = fopen('./cache/count.txt', 'rb');
        $data = '';
        while (!feof($file_count)) $data .= fread($file_count, 4096);
        fclose($file_count);
        list($today, $yesterday, $total, $date, $days) = split("%", $data);
        return ceil($total/$days);
    }
}    
