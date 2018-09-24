<?php

/**
 * Module:BASE_MOD
 * for include to ws application use
 * require_once UNIT('trial/baseTest/base_mod.php');
 * 
 * Модуль является расширением для класса base и _table
 * Добавляет в javascript код объект tb содержащего описание
 * ф-ционал для доступа к именам полей таблиц передваваемых генератором _table или base::select
 * tb ={
 *  TABLE1_NAME:function(field,[data]) 
 *  TABLE1_NAME:ALIAS - алиас (префикс)
 *  TABLE1_NAME:INDEX - имя индексное поле
 *} 
 * 
 * Ex: получить имя с префиксом
 *      
 * tb.CLIENTS("NAME")   //  return "C_NAME"
 * возвращает имя поля с префиксом (если он есть,все поля которые не используют префикс описываются в _table::$noAlias=[field,field])
 * 
 * Ex: полчить значение из данных
 * tb.CLIENTS("NAME",{ID:CLIENT:1,C_NAME:"Mike"})  - Mike
 * 
 * Ex: преобразует данные в данные с префиксом
 * tb.CLIENTS(data)
 * 
 * Ex: получить выборку данных из строки
 *
 * var data = {ID_CLIENT:1,C_NAME:"Mike",C_DATE:'01/02/1987'};
 * tb.CLIENTS(['ID_CLIENT','NAME'],data) // return {ID:CLIENT:1,C_NAME:'Mike'};
 * 
 * Ex: получить выборку из массива строк
 * var data = [
 *      {ID_CLIENT:1,C_NAME:"Mike",C_DATE:'01/02/1987'},
 *      {ID_CLIENT:2,C_NAME:"Soma",C_DATE:'01/02/1987'},
 *      {ID_CLIENT:3,C_NAME:"Dute",C_DATE:'01/02/1987'},
 *      ...
 * ];
 * tb.CLIENTS(['ID_CLIENT','NAME'],data) // return [{ID_CLIENT:1,C_NAME:'Mike'},{ID:CLIENT:2,C_NAME:'Soma'},{ID:CLIENT:3,C_NAME:'Dute'}];
 *
 * 
*/


require_once UNIT('plugins','base/base.php');

MODULES::ADD('BASE_MOD');
class BASE_MOD extends WS_MODULE{

    function __construct($owner){
        parent::__construct($owner);
    }

    public function CONTENT(){
        $classes = get_declared_classes();
        $tables = array();
        for($i=0;$i<count($classes);$i++){
            if (get_parent_class($classes[$i])==='_table')
                $tables[]=$classes[$i]; 
        }    
        
        //$CR = "\n";
        $CR = "";
        $c='';
        $outer = '';
        for($i=0;$i<count($tables);$i++){
            $tab = $tables[$i];
            
            $c.=$tab::NAME.'(f,d){'.$CR. 
                'var n = "'.$tab::NAME.'",a= "'.$tab::ALIAS.'";'.$CR.
                'return (typeof f==="string")?(typeof d ==="object"?tb._u(f,d,n,a):tb._f(f,n,a)):(Array.isArray(f)?tb._a(f,d,n,a):tb._c(f,d,n));},
                ';
            /*return (typeof f==="string"?tb._f(f,n,a):(Array.isArray(f)?tb._a(f,d,n,a):tb._c(f,n,a)));*/
            $outer.='tb.'.$tab::NAME.'.ALIAS="'.$tab::ALIAS.'";'.$CR;
            $outer.='tb.'.$tab::NAME.'.INDEX="'.$tab::INDEX.'";'.$CR;
            $outer.='tb.'.$tab::NAME.'.noAlias='.(property_exists($tab,'noAlias')&&count($tab::$noAlias)>0? ARR::to_json($tab::$noAlias):'[]').';'.$CR;
        }
        
        $add = '
        _u(f,d,n,a){
            return d[tb._f(f,n,a)];
        },
        _f(f,n,a){
            return (tb[n].noAlias.indexOf(f)>-1?"":(a+"_"))+f;
        },
        _c(d,n,a){
            var o = [],
                t = Array.isArray(d),
                v,j,
                b=t?d:[d];
                
            for(j=0;j<b.length;j++){
                var r = {};
                for(var key in b[j]){
                    v=tb._f(key,n,a);
                    r[v]=b[j][key];
                }    
                if (!t)
                    return r;
                else
                    o.push(r);                    
            }
            return o;
        },
        _a(f,d,n,a){
            var o = [],
                t = Array.isArray(d),
                v,i,j,
                b=t?d:[d];
            
            for(j=0;j<b.length;j++){
                var r = {};
                for(i=0;i<f.length;i++){
                    v=tb._f(f[i],n,a);
                    if (v in b[j])
                        r[v]=b[j][v];
                }
                
                if (!t)
                    return r;
                else
                    o.push(r);
                
                
            }    
            return o;
        },
        '.$CR;
        FRAME()->SCRIPT('var tb={'.$add.$CR.$c.'};'.$outer.$CR);                
    }
};


?>