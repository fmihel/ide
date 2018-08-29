<?php
define('CODING_UTF8','utf-8');
define('CODING_WIN1251','windows-1251');

$CODING = CODING_UTF8;//кодировка, в которой будет формироваться ответ 

header('Content-Type: text/html; charset='.$CODING);// заголовок ответа

define('cENCODING',"ENCODING");  

/* ENCODING -  
   Вывод в установленной кодировке   
   Использование ф-ции класса внутри         
   self::FunName();        
   Использование вне класса        
   ENCODING::FuncName();        

*/
class ENCODING // static class
{  

    public static function APPLAY($to)//:string
    {        
        // ENCODING - преобразуетс строку в нужную кодировку для вывода        
        global $CODING;
        $result = $to;
        //if ($CODING === CODING_UTF8)    
            //$result = mb_convert_encoding($to, 'utf-8', 'windows-1251');
            //$result = mb_convert_encoding($to, 'utf-8');        
        return $result;
    }    

    public static function OUT($str)
    {
        echo self::APPLAY($str);        
    }
    public static function FROM_PAGE($str)
    {
        // данная функция используется при текстовых данных на руссом  из страницы броузера
        // в исполняемый скрипт
        // в данном случае на страние используется кодировка UTF-8 в скрипте WIN1251
        //return mb_convert_encoding($str,'windows-1251','utf-8');
    }
    
    public static function simple_codepage($text = '') {
         /* упрощенная ф-ция определени кодировки (работает не на все 100% но вернее чем mb_detect_encoding*/
        if (!empty($text)) {
        $utflower  = 7;
        $utfupper  = 5;
        $lowercase = 3;
        $uppercase = 1;
        $last_simb = 0;
        $charsets = array(
            'UTF-8'       => 0,
            'CP1251'      => 0,
            'KOI8-R'      => 0,
            'IBM866'      => 0,
            'ISO-8859-5'  => 0,
            'MAC'         => 0,
        );
        for ($a = 0; $a < strlen($text); $a++) {
            $char = ord($text[$a]);

            // non-russian characters
            if ($char<128 || $char>256)
                continue;

            // UTF-8
            if (($last_simb==208) && (($char>143 && $char<176) || $char==129))
                $charsets['UTF-8'] += ($utfupper * 2);
            if ((($last_simb==208) && (($char>175 && $char<192) || $char==145))
                || ($last_simb==209 && $char>127 && $char<144))
                $charsets['UTF-8'] += ($utflower * 2);

            // CP1251
            if (($char>223 && $char<256) || $char==184)
                $charsets['CP1251'] += $lowercase;
            if (($char>191 && $char<224) || $char==168)
                $charsets['CP1251'] += $uppercase;

            // KOI8-R
            if (($char>191 && $char<224) || $char==163)
                $charsets['KOI8-R'] += $lowercase;
            if (($char>222 && $char<256) || $char==179)
                $charsets['KOI8-R'] += $uppercase;

            // IBM866
            if (($char>159 && $char<176) || ($char>223 && $char<241))
                $charsets['IBM866'] += $lowercase;
            if (($char>127 && $char<160) || $char==241)
                $charsets['IBM866'] += $uppercase;

            // ISO-8859-5
            if (($char>207 && $char<240) || $char==161)
                $charsets['ISO-8859-5'] += $lowercase;
            if (($char>175 && $char<208) || $char==241)
                $charsets['ISO-8859-5'] += $uppercase;

            // MAC
            if ($char>221 && $char<255)
                $charsets['MAC'] += $lowercase;
            if ($char>127 && $char<160)
                $charsets['MAC'] += $uppercase;

            $last_simb = $char;
        }
        arsort($charsets);
        return key($charsets);
        }
        return 'UTF-8';
        
    }
    
};

?>
