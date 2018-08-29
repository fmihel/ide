<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

    
if(!isset($Application)){
    require_once '../../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false;
    
    require_once UNIT('ws','ws.php');
};

RESOURCE('plugins','splitter/splitter.js');
RESOURCE('plugins','mbtn/mbtn.js');
RESOURCE('plugins','mbtn/mbtn.dcss');

RESOURCE('plugins','jconsole/jconsole.dcss');
RESOURCE('plugins','jconsole/jconsole.js');

RESOURCE('plugins','common/jlock.js');
RESOURCE('plugins','common/jaction.js');
RESOURCE('plugins','common/jhandler.js');

require_once UNIT(WARD(__DIR__,'../session_mod.php'));
require_once UNIT(WARD(__DIR__,'autorize.php'));



session::init(array(
        'autorize'=>new Autorize(),
        'tokenName'=>'_token_',
        'sessionType'=>'session',
        'start'=>true
));


//RESOURCE('plugins','session/session.js');
//RESOURCE('plugins','session/session.dcss');

class TWS extends WS{
        

    public function plugin(){
        
        $own = FRAME('plugin')
        //->ALIGN('JX.stretch(Qs.plugin,{margin:10});')
        ->ALIGN('JX.pos({$},{x:20,y:20,w:200,h:32})')
        ->VALUE('work with session');
        //->INIT('{$}.session({})'); // place code for plugin init

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})')
        ->READY('
        
        session.onStart=function(){
            jconsole("start session");
            jconsole(session,"session");
            jaction.do();
        }
        session.onStop=function(){
            jconsole("stop session");
            jconsole(session,"session");
            jaction.do();
        }
        
        jaction.add(function(){
            
            {$startSession}.mbtn("disable",session.enable);
            {$stopSession}.mbtn("disable",!session.enable);
            
        });
        jaction.do();
        ');
        /*c:theory */
        $this->item(
            'fast',
            /*------------caption---------------*/
            'Быстрый старт',                  
            /*------------code------------------*/
            '
            Быстрый старт:
            1. Подключаем session_mod.php (он автоматически подключит session.php и session.js)
                Подкючить как можно раньше!!! ( к примеру сразу после подключения к базе)
            2. Описываем классс Autorize
                class Autorize{
                    /** авторизация по токену*/
                    public function fromToken($token,$dev){
                        ...
                        return array("res"=>1,"token"=>TOKEN);
                        or 
                        return array("res"=>0);
                    }
                    /** авторизация по данным пользователя */
                    public function fromData($data,$dev){
                        ...
                        return array("res"=>1,"token"=>TOKEN);
                        or 
                        return array("res"=>0);
                    }

                    /** проверка актуальности сессии */
                    public function checkToken($token,$dev){
                        return array("res"=>1);
                        or 
                        return array("res"=>0);
                    }
                    /** проверка данных пользователя на возможность создания сессии */
                    public function checkData($data,$dev){
                        return array("res"=>1);
                        or 
                        return array("res"=>0);
                    }

                    /** действия по остановке сессии */
                    public function disable($token,$dev){
                        return array("res"=>1);
                        or 
                        return array("res"=>0);
                    }
    
                };
            3. В приложении ( к примеру в index.php) перед основным кодом запускаем сессию
                    session::init(array(
                    //подключаем обработчик
                    "autorize"=>new Autorize(),
                    //имя переменной где будет хранится токен ( желательно переименовыватья для приложения)
                    "tokenName"=>"_token_",           
                    //где будет хранится token ( session - токен будет удален после завершения работы приложения, local - противоположно )
                    "sessionType"=>"session",
                    //запустить сессию если возможно (или выполниь session::start())
                    "start"=>true                     
                ));            
            4. Проверка сессии на стороне клиента
                session:$enable : boolean 
            
            5. Проверка сессии на стороне броузера
                session.enable : boolean 
                
            
            ',
            /*------------event-----------------*/
            '
            

            '
        );        
        
        /*c:theory */
        $this->item(
            'theory',
            /*------------caption---------------*/
            'theory',                  
            /*------------code------------------*/
            '
            Механизм сессий.
            Сессия - работа (форирование контента и обмен по ajax) привязанная к определенному клиенту посредством
            уникально идентификатора сессии token 
            token - случайно сгенерированная строка гарантирующая уникальность сессии.
            Каждая передача информации должна осуществлятся с передачей token, для этого 
            после инициализации сессии  его прописывают в разделяемые данные Ws.share.token 
            Для работы с ссеией на стороне клиента описан объект session
            var session={
                // методы
                auto:function()   - автоматическая инициализация сессии
                start:function()  - запуск сессии
                stop:function()   - остановка сессии
                check:function()  - проверка данных пользователя перед отправкой 
                // свойства
                enable - запущена или нет сессия
                ... подробнее см в session.js
            }
            
            На стороне сервера описаны следующие объекты:
            1. session - статический класс ( интерфейс между данными пользователя )
            2. SESSION_MOD (  в session_mod.php ) - модуль ( автоматически подключает session.js 
               и выполняет всю работу по начальной инициализации сессии)
            3. class Autorize см. autorize.php - класс обработчик . Связывает данные проекта с токеном 
            
            Принцип работы (приложение)
            1. Подключаем модуль  session_mod.php (должен подключаться как можно раньше!!!!)
            2. Описываем класс Autorize (см autorize.php) и подключаем
            3  Сразу после подключения инициируем сессию
            
            
                session::init(array(
                    "autorize"=>new Autorize(),       - подключаем обработчик
                    "tokenName"=>"_token_",           - имя переменной где будет хранится токен ( желательно переименовыватья для приложения)
                    "sessionType"=>"session",         - где будет хранится token ( session - токен будет удален после завершения работы приложения, local - противоположно )
                    "start"=>true                     - запустить сессию если возможно
            ));
            
            4. Всё
            
            Для проверки 
                session::$enable -  bool 
            
            
            ',
            /*------------event-----------------*/
            '
            

            '
        );        
        
        
        /*c:startSession */
        $this->item(
            'startSession',
            /*------------caption---------------*/
            'start session',                  
            /*------------code------------------*/
            '
            session.start({login:"login",pass:"pass"});
            
            
            ',
            /*------------event-----------------*/
            '
            
            session.start({login:1,pass:1});

            '
        );        
        
        
        /*c:stopSession */
        $this->item(
            'stopSession',
            /*------------caption---------------*/
            'stop session',                  
            /*------------code------------------*/
            '
            
            session.stop();
            
            ',
            /*------------event-----------------*/
            '
            
            session.stop();
            
            '
        );        

        
        /*c:open page  */
        $this->item(
            'openPage',
            /*------------caption---------------*/
            'open page ',                  
            /*------------code------------------*/
            '// открытие сраницы с отсылкой в нее token и последующей авторизацией страницы
             // запуск из броузера:
             
             window.open(ut.hrefPath()+"simple.php?token="+session.token);
             
             
             // код в запускаемом скрипте php:
             <?php

                require_once "session.php";
                require_once "autorize.php";

                session::init(array(
                    "autorize"=>new Autorize(),
                    "tokenName"=>"_token_",
                    "start"=>false
                ));

                session::start($_REQUEST);
            ?>
            
            ',
            /*------------event-----------------*/
            '
                window.open(ut.hrefPath()+"simple.php?token="+session.token);
            '
        );        

        
        /*c:runApp */
        $this->item(
            'runApp',
            /*------------caption---------------*/
            'run app',                  
            /*------------code------------------*/
            '
            // запуск приложения 
            // авторизация приложения произойдет автоматически ( при условии запуска в том же домене)
            window.open(ut.hrefPath()+"app.php");
            
            ',
            /*------------event-----------------*/
            '
            window.open(ut.hrefPath()+"app.php");            

            '
        );        

        /*c:navigate */
        $this->item(
            'navigate',
            /*------------caption---------------*/
            'navigate',                  
            /*------------code------------------*/
            '
            // запуск приложения 
            // авторизация приложения произойдет автоматически ( при условии запуска в том же домене)
            Ws.navigate({module:"MOD2"});
            
            //or from FRAME
            {NAVIGATE}({module:"MOD2"})
            
            ',
            /*------------event-----------------*/
            '
            /*
                {NAVIGATE}({
                    module:"MOD2"
                })
            */

            '
        );        


    
        /*c:autorizeHandler */
        $this->item(
            'autorizeHandler',
            /*------------caption---------------*/
            'autorize.php',                  
            /*------------code------------------*/
            '
            // данный класс является обработчиком запросов от клиента к серверу
            // переписывается в зависимости от конкретной идеологии авторизации
            // должен реализовать 5 методов, каждый из которых возвращает массив
            // array ("res" => 0| 1,...) как результат (res = 0  - ошибка или невозможность)
            // описанный таким образом класс включается посредством 
            // session::init(array("autorise"=>new Autorize()));
            
            class Autorize{
                /** выполняет действия по авторизации через переданный token и идентификатор устройства dev*/
                public function fromToken($token,$dev){}

                /** действия при авторизации по пользовательским данным ( к примеру логин, пароль) и (или) dev */
                public function fromData($data,$dev){}

                /** проверка актуальности сессии */
                public function checkToken($token,$dev){}
    
                /** проверка данных на возможность создания сессии 
                  * (используется для проверки введенных данных от пользователя, 
                  * не должен создавать сессию,а только проверяет возможность создания) 
                */
                public function checkData($data,$dev){}

                /** действия по остановке сессии */
                public function disable($token,$dev){}
    
            };

            ',
            /*------------event-----------------*/
            '
            '
        );   
        
        


    


    
    
    

    

    }
    private function css(){
        FRAME()->CSS('
            /*c:body */
            body{
                overflow-x:hidden;
                overflow-y:hidden;
                -webkit-font-smoothing:antialiased;
                
                text-overflow: ellipsis;
                margin:0px;
                padding:0px;
                font-family:arial, sans-serif;
                font-size:13px;
                
            }
            
            /*c:layout */
            .layout{
                position:absolute;
                margin:0px;
                padding:0px;
                
            }
            
            /*c:splitter */
            .splitter{
                border:1px solid #C0C0C0;
            }
            
            /*c:splitter_vert */
            .splitter_vert{
                border-top:1px solid white;
                border-bottom:1px solid white;
                z-index:1;
                
            }
            
            /*c:splitter_horiz */
            .splitter_horiz{
                border-left:1px solid white;
                border-right:1px solid white;
                z-index:2;
                 
            }
            
            /*c:menu */
            .menu{
                height:24px;
                line-height:24px;
                width:140px;
                text-align:center;
                border:1px solid silver;
            }
            
            /*c:item_panel */
            .item_panel{
                overflow:auto;
            }
        ');
    }
    private function javascript(){
        FRAME()->SCRIPT('
        function codeNorm(code){
            return  code.replaceAll("<#enter#>","\n").replaceAll("<#quot#>",String.fromCharCode(34)).replaceAll("<#apos#>",String.fromCharCode(39));
        }
        
        function jconsole(o,param){
            if ((o==="clear")&&(param===undefined)){
                 if (!{$btnConsoleClear}.mbtn("active"))
                    {$console}.jconsole("clear");
                return;
            }
            {$console}.jconsole(o,param);
        }
        
        
        function activatePanel(panel){
            {$right}.children().hide(0);    
            panel.show();
        }
        ');
    }
    private function layout(){
        $wp = FRAME('workplace',FRAME())->CLASSES('layout');
            
        FRAME('modal',FRAME())->CLASSES('layout')
            ->STYLE('left:0px;top:0px;width:0px;height:0px;z-index:1000');
        
        $page = FRAME('page',$wp)->CLASSES('layout')
            ->ALIGN('JX.workplace(Qs.workplace,Qs.page);');        
        
        $own = $page
            ->ALIGN('
                JX.arrange([Qs.top,Qs.middle,Qs.bottom],{direct:"vert",stretch:Qs.middle});
                JX.arrange(Qs.middle.children(),{direct:"horiz",stretch:Qs.center});
            ');
                
        FRAME('top',$own)->CLASSES('layout')
            ->STYLE('height:32px;line-height:32px;text-indent:5px;font-weight:bold;border-bottom:1px solid #C0C0C0');
        
        $middle = FRAME('middle',$own)->CLASSES('layout');
            
        FRAME('bottom',$own)->CLASSES('layout')
            ->STYLE('height:32px;border-top:1px solid #C0C0C0');

        FRAME('left',$middle)->CLASSES('layout');

        FRAME('splitter_lc',$middle)
            ->STYLE('position:absolute;width:10px;cursor:ew-resize')
            ->CLASSES('splitter splitter_vert')
            ->READY('{$}.splitter({horiz:true})');
            
        $center = FRAME('center',$middle)->CLASSES('layout')
        ->SCRIPT('var centerAlignFirst = true')
        ->ALIGN('
            if (centerAlignFirst)
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0,rate:1},{idx:2,rate:4},{idx:4,rate:5}]});
            else    
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0}]});
            centerAlignFirst=false;
            
            JX.stretch(Qs.code,{margin:{left:10,top:30,bottom:10,right:10}});
            JX.stretch(Qs.console,{margin:{left:10,top:30,bottom:10,right:10}});
        ');
        
        $pluginFrame = FRAME('pluginFrame',$center)->CLASSES('layout');
        
        $plugin = FRAME('plugin',$pluginFrame)->STYLE('position:absolute')->CLASSES('layout');
            
        FRAME('splitter_fl',$center)
            ->STYLE('position:absolute;height:10px;cursor:n-resize')
            ->CLASSES('splitter splitter_horiz')
            ->READY('{$}.splitter({horiz:false})');
        
        $codeFrame = FRAME('codeFrame',$center)->CLASSES('layout');
                FRAME('codeLabel',$codeFrame)->CLASSES('layout')->VALUE('CODE:')->STYLE("left:10px;top:5px;color:#99B8D8");
                FRAME('codeFrom',$codeFrame)->CLASSES('layout')->VALUE('...')->STYLE("left:80px;top:5px");
                FRAME('code',$codeFrame)->CLASSES('layout')->STYLE('overflow:auto;padding:2px')->VALUE('');

        FRAME('splitter_cc',$center)
            ->STYLE('position:absolute;height:10px;cursor:n-resize')
            ->CLASSES('splitter splitter_horiz')
            ->READY('{$}.splitter({horiz:false})');
        
        $consoleFrame = FRAME('consoleFrame',$center)->CLASSES('layout')->STYLE('height:200px');
            FRAME('consoleLabel',$consoleFrame)->CLASSES('layout')->VALUE('CONSOLE:')->STYLE("left:10px;top:5px;color:#99B8D8");
            FRAME('btnConsoleClear',$consoleFrame)->CLASSES('layout')->VALUE('not clear')->STYLE("left:100px;top:5px;width:60px;text-align:center;cursor:default")
            ->INIT('{$}.mbtn({click:function(){
                    {$}.mbtn("active",!{$}.mbtn("active"));
                            
            }})');
            FRAME('btnConsoleExpanded',$consoleFrame)->CLASSES('layout')->VALUE('expanded')->STYLE("left:190px;top:5px;width:60px;text-align:center;cursor:default")
            ->INIT('{$}.mbtn({click:function(){
                    {$}.mbtn("active",!{$}.mbtn("active"));
                    {$console}.jconsole("put",{expandAfter:{$}.mbtn("active")});
            }})');
            
        FRAME('console',$consoleFrame)->CLASSES('layout')->STYLE('overflow:auto')->VALUE('');

        //---------------------------------------------------------------------
        FRAME('splitter_cr',$middle)
            ->STYLE('position:absolute;width:10px;cursor:ew-resize')
            ->CLASSES('splitter splitter_vert')
            ->READY('{$}.splitter({horiz:true})');

        FRAME('right',$middle)->CLASSES('layout')
            ->ALIGN('
                $.each({$}.children(),function(i,o){
                    if (JX.visible(o)){
                        JX.stretch(o,{margin:5});
                        return true;
                    }
                });');
    }
    private function refactoring($code){
        $code = str_replace(array("\t"),array('    '),$code);
        $codes = explode("\n",$code);
        // подсчитываем мин кол - во пробелов вначале
        $min = -1;
        for($i=0;$i<count($codes);$i++){
            
            $len = strlen(ltrim($codes[$i]));
            $val = strlen($codes[$i])-$len;
            if (($len>0) && ($val>0)){
                if ($min==-1)
                    $min = $val;
                else
                    $min = min($val,$min);
            }
        };
        
    
        // удаляем из начала минимальное кол-во пробелов
        if ($min>0){
            $code = '';
            for($i=0;$i<count($codes);$i++){
                $val = strlen($codes[$i])-strlen(ltrim($codes[$i]));
            
                if ($val>=$min)
                    $codes[$i]= substr($codes[$i],$min);
            
                if (($code!=='')||(strlen(trim($codes[$i]))>0))
                    $code.=$codes[$i]."\n";    
            };
        }    
        return $code;        
    }
    private function item($id,$caption,$code='',$click=''){
        $own = FRAME('left');
        $code = $this->refactoring($code);

        $code = str_replace(array("\n",'"',"'"),array('<#enter#>','<#quot#>','<#apos#>'),$code);

        $item = FRAME($id,$own)
            ->CLASSES('menu')
            ->STYLE('position:absolute')
            ->VALUE($caption)
            ->INIT('{$}.mbtn({
                click:function(){
                    var code = "'.$code.'";
                    activatePanel(Qs.'.$id.'_panel);
                    jconsole("clear");
                    Qs.codeFrom.text({$}.text());

                    Qs.code.html("<xmp>"+codeNorm(code)+"</xmp>");
                    '.$click.';
                    
                    Ws.align();
                }
            });');
            
        $panel = FRAME($id.'_panel',FRAME('right'))    
        ->CLASSES('item_panel')
        ->STYLE('position:absolute;display:none');
        
        return array('item'=>$item,'panel'=>$panel);
    }
    

    public function CONTENT(){
        global $REQUEST;
        if ( $REQUEST->MODULE!=='MOD2'){
            $this->layout();
            $this->plugin();

            $this->css();
            $this->javascript();
            $this->menu();
        
            FRAME('top')->VALUE('session');
            FRAME('left')->STYLE('width:220px');
            FRAME('right')->STYLE('width:250px');
            FRAME('codeFrame')->STYLE('height:150px');
            FRAME('consoleFrame')->STYLE('height:600px');
            FRAME('plugin');        
        
            FRAME('console')->INIT('
            {$}.jconsole("put",{
                clearBefore:false,
                expandAfter:false,
                fastInfo:{enable:true},
            
            })');
        }else{
            $own = FRAME('out',FRAME())
            ->READY('
                jaction.add(function(){
                
                    var text = session.enable?"session start..":"..session stop";
                    if (text != {$}.text())
                        {$}.text(text);
                
                });
            ');            
        }    


    }
    
  
  
    public function AJAX(&$response){
        global $REQUEST;
      
        return false;
    }

}      

if($Application->is_main(__FILE__)){
    
    $app = new TWS(); 
    
    $app->title = 'session';
    $app->RUN();
    

}

?>
    