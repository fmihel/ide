<?php
/*
https://ws-framework-fmihel.c9users.io/ide/ws/plugins/jgallery/example.php
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);


    
if(!isset($Application)){
    require_once '../../utils/application.php';
    
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
RESOURCE('plugins','common/local_storage.js');


RESOURCE('jgallery.dcss');
RESOURCE('jgallery.js');
RESOURCE('jgallery_navi.js');
RESOURCE('jgallery_navi.dcss');



//RESOURCE('plugins','jgallery/jgallery.js');
//RESOURCE('plugins','jgallery/jgallery.dcss');

/*
варианты вызова
{$}.jgallery(["","",""]);
{$}.jgallery([{data:object,url:""},"",""]);
*/
class TWS extends WS{
        

    public function plugin(){
        
        $own = FRAME('plugin')
        ->ALIGN('
            JX.arrange({$pluginFrame}.children(),{direct:"horiz",type:"stretch",align:"stretch",stretch:{$plugin},margin:5});
        ')
        //->ALIGN('JX.pos({$},{x:20,y:20,w:200,h:32})')
        
        ->INIT('
            {$}.jgallery({
                viewIsCurrent:true,
                onChangeView:function(o){
                    jconsole(o,{name:"onChangeView"});
                },
                onChangeCurrent:function(o){
                    jconsole(o,{name:"onChangeCurrent"});
                },
                onLoading:function(o){
                    jconsole(o,{name:"onLoading"})
                },
                onLoad:function(){
                    jconsole("load complete")
                }
                
            });

            {$}.jgallery("addPlugin",{
                name:"navi",
                obj:new jgallery_navi({
                    owner:{$add}
                
                })
            });

            
        '); // place code for plugin init

    }

    private function menu(){
        
        FRAME('left')
        ->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})')
        ->READY('
        ')
        ->SCRIPT('
        var images = [
            "http://windeco.su/pdfcatalog/JPG/Murano_klassica_foto_15/Murano_klassica_foto_15-000001.jpg",
            "http://windeco.su/pdfcatalog/JPG/Murano_klassica_foto_15/Murano_klassica_foto_15-000002.jpg",
            "http://windeco.su/pdfcatalog/JPG/Murano_klassica_foto_15/Murano_klassica_foto_15-000003.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000001.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000002ww.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000002.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000003.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000004.jpg",
            "http://windeco.su/pdfcatalog/JPG/Antik_instr/Antik_instr-000001.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000002.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000003.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000004.jpg",
            "http://windeco.su/pdfcatalog/JPG/Antik_instr/Antik_instr-000001.jpg", 
    "http://windeco.su/pdfcatalog/JPG/Murano_klassica_foto_15/Murano_klassica_foto_15-000001.jpg",
            "http://windeco.su/pdfcatalog/JPG/Murano_klassica_foto_15/Murano_klassica_foto_15-000002.jpg",
            "http://windeco.su/pdfcatalog/JPG/Murano_klassica_foto_15/Murano_klassica_foto_15-000003.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000001.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000002ww.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000002.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000003.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000004.jpg",
            "http://windeco.su/pdfcatalog/JPG/Antik_instr/Antik_instr-000001.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000002.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000003.jpg",
            "http://windeco.su/pdfcatalog/JPG/ANTIK_32169/ANTIK_32169-000004.jpg",
            "http://windeco.su/pdfcatalog/JPG/Antik_instr/Antik_instr-000001.jpg",          
            ];
        
        ');

        $this->item(
            'quickStart',                   //id
            'Quick start',                  //caption
            '$("#plugin").jgallery(images);
              ',//sample code
            '
            {$plugin}.jgallery(images);
            '    //onclick event
            
            
        );
        
        
        /*c:byWidth */
        $this->item(
            'byWidth',
            /*------------caption---------------*/
            'byWidth',                  
            /*------------code------------------*/
            '
            // вписывает страницу по ширине
            {$plugin}.jgallery({placeBy:"width"});
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery({placeBy:"width"});
            
            '
        );        

        /*c:byHeight */
        $this->item(
            'byHeight',
            /*------------caption---------------*/
            'byHeight',                  
            /*------------code------------------*/
            '
            // вписывает страницу полностью
            {$plugin}.jgallery({placeBy:"height"});
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery({placeBy:"height"});
            
            '
        );        
        /*c:Tile */
        $this->item(
            'Tile',
            /*------------caption---------------*/
            'Tile',                  
            /*------------code------------------*/
            '
            // вписывает страницу по ширине
            {$plugin}.jgallery({placeBy:"tile"});
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery({placeBy:"tile"});
            
            '
        );        

        /*c:byHeight */
        $this->item(
            'byCustomW',
            /*------------caption---------------*/
            'by custom W',                  
            /*------------code------------------*/
            '
            // вписывает страницу полностью
            {$plugin}.jgallery({placeBy:800});
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery({placeBy:800});
            
            '
        );        
        
        /*c:current0 */
        for($i=0;$i<8;$i++)
        $this->item(
            'current'.$i,
            /*------------caption---------------*/
            'current'.$i,                  
            /*------------code------------------*/
            '
            var current = {$plugin}.jgallery("current");
            {$plugin}.jgallery("current",'.$i.');
            
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery("current",'.$i.');
            jconsole({$plugin}.jgallery("current"));
            
            '
        );        
        
        
        /*c:onChangeView */
        $this->item(
            'onChangeView',
            /*------------caption---------------*/
            'onChangeView',                  
            /*------------code------------------*/
            '
            
            // вызывается при изменении видимого изображения (обычно при скролинге)
            {$plugin}.jgallery({
                onChangeView:function(o){
                    
                }
            })
            ',
            /*------------event-----------------*/
            '
            

            '
        );        
        /*c:onChangeView */
        $this->item(
            'onChangeCurrent',
            /*------------caption---------------*/
            'onChangeCurrent',                  
            /*------------code------------------*/
            '
            
            // вызывается при изменении текущего рисунка (курсор)
            {$plugin}.jgallery({
                onChangeCurrent:function(o){
                    
                }
            })
            ',
            /*------------event-----------------*/
            '
            

            '
        );        
        
        /*c:onLoading */
        $this->item(
            'onLoading',
            /*------------caption---------------*/
            'onLoading',                  
            /*------------code------------------*/
            '
            // процесс загрузки картинок, инициируется на каждую загрузку изображения
            {$plugin}.jgallery({
                onLoading:function(o){
                    
                }
            })
            
            ',
            /*------------event-----------------*/
            '

            '
        );        
        
        
        /*c:onLoad */
        $this->item(
            'onLoad',
            /*------------caption---------------*/
            'onLoad',                  
            /*------------code------------------*/
            '
            // выызвается по завершению последней загрузки изображений
            {$plugin}.jgallery({
                onLoad:function(){
                    
                }
            })            
            ',
            /*------------event-----------------*/
            '
            
            
            
            '
        );        

        
        /*c:story */
        $this->item(
            'story',
            /*------------caption---------------*/
            'story',                  
            /*------------code------------------*/
            '
            
            // сохраняет текущее состояние галлереи
            {$plugin}.jgallery("story","test");
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery("story","test");
            
            '
        );        


        
        /*c:reStory */
        $this->item(
            'reStory',
            /*------------caption---------------*/
            'reStory',                  
            /*------------code------------------*/
            '
            
            // загружает состояние галлереи
            {$plugin}.jgallery("story","test");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery("reStory","test");
            
            '
        );        

        /*c:prev */
        $this->item(
            'prev',
            /*------------caption---------------*/
            'prev',                  
            /*------------code------------------*/
            '
            // смещает курсор на предыдущую картинку
            {$plugin}.jgallery("prev");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery("prev");
            
            '
        );        

    
        
        /*c:next */
        $this->item(
            'next',
            /*------------caption---------------*/
            'next',                  
            /*------------code------------------*/
            '
            // смещает курсор на следующу картинку
            {$plugin}.jgallery("next");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.jgallery("next");
            
            '
        );        


        

        
        /*c:add */
        $this->item(
            'addPlugin',
            /*------------caption---------------*/
            'addPlugin',                  
            /*------------code------------------*/
            '
            
            // добавляем плагин
            {$plugin}.jgallery("addPlugin",{
                name:"navi",
                obj:new jgallery_navi({
                    owner:{$add}
                
                })
            });
            
            // плагин должен реализовать метод onGallery, для перехватывания событий от галереи
            // пример плагина
            function jgallery_navi(o){
                    var t = this;
                    t.param = $.extend(true,{
                        gallery:null,
                        owner:null,

                    },o);
            }


            jgallery_navi.prototype.onGallery=function(o){
                var t=this,p=t.param;
    
                if (o.event === "init")
                    p.gallery = o.sender;
    
                if (o.event === "create")
                    t.create(o.sender);
        
                if (o.event === "change")
                    t.change(o);
        
            };                
            
            ...
            
            ',
            /*------------event-----------------*/
            '

            
            '
        );        


        
        /*c:prop */
        $this->item(
            'prop',
            /*------------caption---------------*/
            'any prop',                  
            /*------------code------------------*/
            '
            /**отступ между изображениями */    
            gap:10 
        
            /** отступ при вписывании в видимую область */
            indent:20,
            
            /** скорость анимации */
            animation:500,
            
            /** изображение, которое грузится на в случае ошибки загрузки */
            imgOnError:"",
            
            /** включает режим при котором видимая картинка автоматически становится текущей */
            viewIsCurrent:false,            
            '
            ,
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
            /*{$right}.children().hide(0);*/    
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
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0,rate:2},{idx:2,rate:1}]});
            else    
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0}]});
            centerAlignFirst=false;
            
            JX.stretch(Qs.code,{margin:{left:10,top:30,bottom:10,right:10}});
            JX.stretch(Qs.console,{margin:{left:10,top:30,bottom:10,right:10}});
        ');
        
        $pluginFrame = FRAME('pluginFrame',$center)->CLASSES('layout');
        
        $plugin = FRAME('plugin',$pluginFrame)->STYLE('position:absolute')->CLASSES('layout');
        $add    = FRAME('add',$pluginFrame)->STYLE('position:absolute;width:100px')->CLASSES('layout');            
        
        FRAME('splitter_fl',$center)
            ->STYLE('position:absolute;height:20px;cursor:n-resize')
            ->CLASSES('splitter splitter_horiz')
            ->READY('{$}.splitter({horiz:false})');
        
        $codeFrame = FRAME('codeFrame',$center)->CLASSES('layout');
                FRAME('codeLabel',$codeFrame)->CLASSES('layout')->VALUE('CODE:')->STYLE("left:10px;top:5px;color:#99B8D8");
                FRAME('codeFrom',$codeFrame)->CLASSES('layout')->VALUE('...')->STYLE("left:80px;top:5px");
                FRAME('code',$codeFrame)->CLASSES('layout')->STYLE('overflow:auto;padding:2px')->VALUE('');



        //---------------------------------------------------------------------
        FRAME('splitter_cr',$middle)
            ->STYLE('position:absolute;width:10px;cursor:ew-resize')
            ->CLASSES('splitter splitter_vert')
            ->READY('{$}.splitter({horiz:true})');

        $consoleFrame = FRAME('consoleFrame',$middle)->CLASSES('layout')->STYLE('height:200px');
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
        $this->layout();
        $this->plugin();

        $this->css();
        $this->javascript();
        $this->menu();
        
        FRAME('top')->VALUE('jgallery');
        FRAME('left')->STYLE('width:220px');
        //FRAME('right')->STYLE('width:250px');
        FRAME('codeFrame')->STYLE('height:150px');
        FRAME('consoleFrame')->STYLE('width:260px');
        FRAME('plugin');        
        
        FRAME('console')->INIT('
        {$}.jconsole("put",{
            clearBefore:false,
            expandAfter:false,
            fastInfo:{enable:true},
            
        })');


    }
    
  
  
    public function AJAX(&$response){
        global $REQUEST;
      
        return false;
    }

}      

if($Application->is_main(__FILE__)){
    
    $app = new TWS(); 
    
    $app->title = 'jgallery';
    $app->RUN();
    

}

?>
    