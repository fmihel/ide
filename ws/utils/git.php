<?php
/**
 * класс-библиотека для работы с информацией git
 */ 
class GIT{
    
    public static $path = ''; // значение папки git по умолчанию
    
    /**
     * проверка наличия папки git
     */
    public static function exist(string $path=''):bool{
        return file_exists(self::getPath($path).'.git');
    }
    /**
     * возвращает имя текущей ветки git
     */ 
    public static function branchName(string $path=''){
        try{
            if (self::exist($path)){
                
                $stringFromFile = file(self::getPath($path).'.git/HEAD', FILE_USE_INCLUDE_PATH);
                $firstLine = $stringFromFile[0]; //get the string from the array
                $explodedString = explode("/", $firstLine, 3); //seperate out by the "/" in the string
                $branchName = $explodedString[2]; //get the one that is always the branch name
                
                return $branchName;
            }else
                return '';
            
        }catch(Exception $e){
            return '';
        }
    } 
    /**
     *добавляет конечый слеш
     */  
    static private function slash(string $path):string{
        $path = trim($path);
        if ($path==='')
            return $path;
        
        $last = $path[mb_strlen($path)-1];
        if ($last!=='/' && $last !== "\\")
            return $path.'/';
            
        return $path;
    }
    /**
     * возвращает путь или путь по умолчанию
     */ 
    static private function getPath(string $path):string{
        return self::slash($path!==''?$path:self::$path);
    }
}


?>