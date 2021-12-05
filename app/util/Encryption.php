<?php

namespace app\util;

use think\facade\Env;

class Encryption
{
    static function encrypt($string, $encode = false) : string
    {
        $src = array("/","+","=");
        $dist = array("_a","_b","_c");
        if(!$encode){$string = str_replace($dist,$src,$string);}
        $key=md5(Env::get('jwt.secret'));
        $key_length=strlen($key);
        $string = !$encode ? base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rnd=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rnd[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rnd[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if(!$encode)
        {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
            {
                return substr($result,8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            $r = str_replace('=','',base64_encode($result));
            return str_replace($src,$dist,$r);
        }
    }
}