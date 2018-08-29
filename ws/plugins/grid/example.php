<?php
/*
https://ws-framework-fmihel.c9users.io/ide/ws/plugins/grid/example.php
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
RESOURCE('plugins','common/dvc.js');
RESOURCE('plugins','common/timer.js');

//RESOURCE('grid.js');
//RESOURCE('grid.dcss');

RESOURCE('plugins','grid/grid.js');
RESOURCE('plugins','grid/grid.dcss');

class TWS extends WS{
        

    public function plugin(){
        
        $own = FRAME('plugin')
        ->STYLE('overflow:hidden')
        ->CLASSES('plugin')
        
        ->ALIGN('JX.stretch(Qs.plugin,{margin:10});')
        ->INIT('{$}.grid({
            indexField:"ID",            
            
            
            /*c:fields*/
            
            fields :[
                {name:"ID",caption:"id",width:50},
                {name:"NAME",caption:"name wekf kwjehrf kj hkwerkjhf kjhw kwjhf kjwherf khkhwkerhf ",width:100},
                {name:"HIDDEN",caption:"hidden ",visible:false},
                {name:"DATE",caption:"date"},
                {name:"PANEL",caption:"panel",width:200},
            ],

            selected:true,
            /*c:onChange*/
            onChange:function(o){
                
                o.sender.each(function(a){
                    if (!a.sender.group(a.tr)){
                        if (a.i%2===0)
                            a.tr.removeClass("grid_parity");
                        else
                            a.tr.addClass("grid_parity");
                    }else{
                        a.tr.css("border","1px solid rgba(0,0,0,0)");
                        a.tr.find("td").css("border","1px solid rgba(0,0,0,0)");
                    }
                    
                    
                },"visible");
                
                
                var inputs = o.sender.param.jq.cells.find("input");
                inputs.css("border","1px solid rgba(0,0,0,0)");
                inputs.css("background","rgba(0,0,0,0)");
                
                inputs.off("keyup").on("keyup",function(){
                    var input = {$}.grid("closest",this);
                    input.data[input.name] = input.source.val();
                });
                
            },
            /*c:onCreate*/
            onCreate:function(o){
                
                if (o.event ==="panel_group"){
                    
                    o.style= "text-indent:10px";
                }
                return;
                if (o.event === "td"){
                    if (o.name==="NAME"){
                        o.value = ut.tag({tag:"input",value:o.value,style:"width:80%"});
                    
                        
                    }else if ((o.name==="DATE")&&(o.id==2)){
                        o.style = "color:red";
                    }else if (o.name==="PANEL"){
                        o.value = ut.tag({value:" [+] [-] [$]",style:"height:12px;border:1px solid gray;display:none;line-height:12px"});
                    }
                    
                    
                }
            },
            /*c:onSetValue*/
            onSetValue:function(o){
                if (o.name==="NAMES"){
                    o.td.find("input").val(o.value);
                    return true;
                }
                
            },
            /*c:onHover*/
            onHover:function(o){
                var td;
                if (o.tr)
                    o.tr.find("#PANEL").children().fadeIn(0);
                if (o.prev)
                    o.prev.find("#PANEL").children().fadeOut(100);
                
            },
            onHoverGroup:function(o){
                console.info("hoverGroup",o);    
            },
            onSelect:function(o){
                jconsole("clear");
                jconsole(o,"onSelect");
            },
            /*c:onClick*/
            onClick:function(o){
                
            }
            
        })'); // place code for plugin init

    }

    private function menu(){
        
        FRAME('left')->ALIGN('JX.arrange({$}.children(),{direct:"vert",type:"top",align:"center",gap:2,margin:{top:10}})')
        ->SCRIPT('
        var max_id = 0,max_group=0;
        function getData(count){
        
            var i,data = [];
            
            for(i=0;i<count;i++){
                max_id++;
                data.push({
                    ID:max_id,
                    NAME:ut.random_str(5),
                    DATE:ut.random(10,99)+"/"+ut.random(10,99)+"/"+ut.random(10,99)
                })
            }
            return data;
        }
        ');
        
        
        /*c:clear */
        $this->item(
            'clear',
            /*------------caption---------------*/
            'clear',                  
            /*------------code------------------*/
            '
            
            {$plugin}.grid("clear");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.grid("clear");
            
            '
        );        
        
        /*c:free */
        $this->item(
            'free',
            /*------------caption---------------*/
            'free',                  
            /*------------code------------------*/
            '
            // полная очистка таблицы, вместе со структурой
            {$plugin}.grid("free");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.grid("free");
            
            '
        );        


    
        
        
        /*c:delete */
        $this->item(
            'delete',
            /*------------caption---------------*/
            'delete',                  
            /*------------code------------------*/
            '
            
            var s = {$plugin}.grid("selected");
            {$plugin}.grid("delete",s);
            
            ',
            /*------------event-----------------*/
            '
            
            var s = {$plugin}.grid("selected");
            {$plugin}.grid("delete",s);
            
            '
        );        

    
        /*c:add10 */
        $this->item(
            'add5',                   //id
            'add5',                  //caption
            '$("#plugin").grid({
                data:[...]
            });',//sample code
            '
            {$plugin}.grid("begin");
            
            var s = {$plugin}.grid("selected");
            if (s.length>0)
                {$plugin}.grid({data:getData(5),insertTo:{after:s[0]}});
            else    
                {$plugin}.grid({data:getData(5)});


            {$plugin}.grid("end");    
        
            '    //onclick event
            
        );
        
        /*c:add_group_5 */
     $this->item(
            'group5',                   //id
            'group5',                  //caption
            'group',//sample code
            '
                var s = {$plugin}.grid("selected");

            max_group++;
            if (s.length>0)
                {$plugin}.grid("data",{
                    data:getData(5),
                    group:{id:"g"+max_group},
                    insertTo:{after:s[0]}
                
                });
            else                
                {$plugin}.grid("data",{
                    data:getData(5),
                    group:{id:"g"+max_group},
                
                });'    //onclick event
        );            
        /*c:add_group_5 */
     $this->item(
            'group100',                   //id
            'group100',                  //caption
            'group',//sample code
            '
                var s = {$plugin}.grid("selected");
            {$plugin}.grid("begin");    
            for(i=0;i<5;i++){
            
                max_group++;
                if (s.length>0)
                    {$plugin}.grid("data",{
                        data:getData(100),
                        group:{id:"g"+max_group},
                        insertTo:{after:s[0]}
                
                    });
                else                
                    {$plugin}.grid("data",{
                        data:getData(100),
                        group:{id:"g"+max_group},
                
                    });
                    
            }
            {$plugin}.grid("end");
            
                    '    //onclick event
        );            
        
        
        /*c:getData */
        $this->item(
            'getData',
            /*------------caption---------------*/
            'getData',                  
            /*------------code------------------*/
            '
            
            var data = {$plugin}.grid("data");

            var data = {$plugin}.grid("data","rows");

            var data = {$plugin}.grid("data","selected");

            var data = {$plugin}.grid("data","hide");

            ',
            /*------------event-----------------*/
            '
            jconsole("clear");
            
            var data = {$plugin}.grid("data");
            jconsole(data,"all data");
            
            var data = {$plugin}.grid("data","rows");
            jconsole(data,"rows");

            var data = {$plugin}.grid("data","selected");
            jconsole(data,"selected");
            
            var data = {$plugin}.grid("data","hide");
            jconsole(data,"hide");
            '
        );        
        
        
        /*c:setData */
        $this->item(
            'setData',
            /*------------caption---------------*/
            'setData',                  
            /*------------code------------------*/
            '
            // modif current selection data
            
            var selected = {$plugin}.grid("selected");
            if (selected.length){
                var data = $D(selected[0]);
                    data.NAME = "modif";
                {$plugin}.grid("data",{data:[data]});
            }    
            
            ',
            /*------------event-----------------*/
            '
            var sel = {$plugin}.grid("selected");
            if (sel.length){
                var d = $D(sel[0]);
                    d.NAME = "modif";
                {$plugin}.grid("data",{data:[d]});
            }    
            
            '
        );        


    
        /*c:hide */
        $this->item(
            'hide',
            /*------------caption---------------*/
            'hide',                  
            /*------------code------------------*/
            '
            var s={$plugin}.grid("selected");
            {$plugin}.grid("hide",s,true);
            
            ',
            /*------------event-----------------*/
            '
            var s={$plugin}.grid("selected");
            {$plugin}.grid("hide",s,true);
            
            '
        );        
        
        
        /*c:show */
        $this->item(
            'show',
            /*------------caption---------------*/
            'show',                  
            /*------------code------------------*/
            '
            
            var hide = {$plugin}.grid("map","hide");
            {$plugin}.grid("hide",hide,false);
            
            ',
            /*------------event-----------------*/
            '
            var hide = {$plugin}.grid("map","hide");
            {$plugin}.grid("hide",hide,false);
            
            '
        );        
        
        /*c:hideGroups */
        $this->item(
            'hideGroups',
            /*------------caption---------------*/
            'hideGroups',                  
            /*------------code------------------*/
            '
            
            {$plugin}.grid("hideGroup",true);
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.grid("hideGroup",true);
            
            '
        );        
        
        /*c:showGroup */
        $this->item(
            'showGroup',
            /*------------caption---------------*/
            'showGroup',                  
            /*------------code------------------*/
            '
            {$plugin}.grid("hideGroup",false);
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.grid("hideGroup",false);
            
            '
        );        


    

    

        
        /*c:mark */
        $this->item(
            'mark',
            /*------------caption---------------*/
            'mark',                  
            /*------------code------------------*/
            '
            var tr = {$plugin}.grid("selected");

            {$plugin}.grid("mark","deleted",tr,true);
            
            var mark = {$plugin}.grid("map",{filter:"mark",mark:"deleted"});
            
            ',
            /*------------event-----------------*/
            '
            var tr = {$plugin}.grid("selected");
            {$plugin}.grid("mark","deleted",tr,true);
            
            var mark = {$plugin}.grid("map",{filter:"mark",mark:"deleted"});
            
            jconsole("clear");
            jconsole(mark,"mark");
            
            '
        );        

        /*c:unmark */
        $this->item(
            'unmark',
            /*------------caption---------------*/
            'unmark',                  
            /*------------code------------------*/
            '

            var mark = {$plugin}.grid("map",{filter:"mark",mark:"deleted"});

            {$plugin}.grid("mark","deleted",mark,false);
            
            ',
            /*------------event-----------------*/
            '
            var mark = {$plugin}.grid("map",{filter:"mark",mark:"deleted"});

            {$plugin}.grid("mark","deleted",mark,false);
            
            
            mark = {$plugin}.grid("map",{filter:"mark",mark:"deleted"});
            
            jconsole("clear");
            jconsole(mark,"mark");
            
            '
        );        

        
        /*c:find */
        $this->item(
            'find',
            /*------------caption---------------*/
            'find',                  
            /*------------code------------------*/
            '
            
             {$plugin}.grid("find",2);//find by pos
             {$plugin}.grid("find","3");//find by id
             {$plugin}.grid("find",function(o){
                 return $D(o.tr).ID===5
             });//find by id
             
            
            ',
            /*------------event-----------------*/
            '
            
            var tr = {$plugin}.grid("find",function(o){
                return $D(o.tr).ID==3;
            });
            jconsole(tr);
            
            '
        );        


    
        /*c:select */
        $this->item(
            'select',
            /*------------caption---------------*/
            'select',                  
            /*------------code------------------*/
            '
            var tr=false;
            {$plugin}.grid("each",function(o){
                var data = $.data(o.tr,"data");
                if (data.ID == 2){
                    tr = o.tr;
                    return true;
                }
            },"rows");
            
            if (tr)
                {$plugin}.grid("select",tr);
            ',
            /*------------event-----------------*/
            '
            var tr=false;
            {$plugin}.grid("each",function(o){
                var data = $.data(o.tr[0],"data");
                if (data.ID == 2){
                    tr = o.tr;
                    return true;
                }
            },"rows");
            
            if (tr)
                {$plugin}.grid("select",tr);
            
            '
        );        

        
        /*c:unselect */
        $this->item(
            'unselect',
            /*------------caption---------------*/
            'unselect',                  
            /*------------code------------------*/
            '
            
            {$plugin}.grid("unselect");
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.grid("unselect");
            
            '
        );        


        
        /*c:hideField */
        $this->item(
            'hideField',
            /*------------caption---------------*/
            'hideField',                  
            /*------------code------------------*/
            '
            
                {$plugin}.grid("field","ID",{visible:false});
            
            ',
            /*------------event-----------------*/
            '
            {$plugin}.grid("field","ID",{visible:false});
            
            '
        );        

        /*c:hideField */
        $this->item(
            'showField',
            /*------------caption---------------*/
            'showField',                  
            /*------------code------------------*/
            '
            
                {$plugin}.grid("field","ID",{visible:true});
            
            ',
            /*------------event-----------------*/
            '
            {$plugin}.grid("field","ID",{visible:true,width:200});
            
            '
        );        
        
        
        /*c:moveField */
        $this->item(
            'moveColumn',
            /*------------caption---------------*/
            'moveColumn',                  
            /*------------code------------------*/
            '
            
            {$plugin}.grid("moveColumn",{
                field:"NAME",
                to:3,
                place:"after"
            });
            
            ',
            /*------------event-----------------*/
            '
            
            {$plugin}.grid("moveColumn",{
                field:"NAME",
                to:3,
                place:"after"
            });
            
            '
        );        

        
        /*c:scroll */
        $this->item(
            'scroll',
            /*------------caption---------------*/
            'scroll',                  
            /*------------code------------------*/
            '
            
            var s = {$plugin}.grid("selected");
            
            {$plugin}.grid("scroll",s);
            
            ',
            /*------------event-----------------*/
            '
            
            var s = {$plugin}.grid("selected");
            
            if (s.length)
                {$plugin}.grid("scroll",s[0]);
            
            '
        );        

        
        /*c:scrollToGroup */
        $this->item(
            'scrollToGroup',
            /*------------caption---------------*/
            'scrollToGroup',                  
            /*------------code------------------*/
            '
            
            var g = {$plugin}.grid("group",1);
            {$plugin}.grid("scroll",g);
            
            ',
            /*------------event-----------------*/
            '
            var g = {$plugin}.grid("group",1);
            {$plugin}.grid("scroll",g);
            
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
        var tt={};
        function timer(ev,name){
            if (name===undefined)
                name = "delta";
            if (ev==="start")
                tt[name]=TIMER.timesec();
            if (ev==="stop"){
                var t = TIMER.timesec()-tt[name];
                jconsole(t.toFixed(3),name);
            }
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
                JX.arrange({$}.children(),{direct:"vert",type:"stretch",align:"stretch",stretch:[{idx:0},{idx:2}]});
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


        //---------------------------------------------------------------------
        FRAME('splitter_cr',$middle)
            ->STYLE('position:absolute;width:10px;cursor:ew-resize')
            ->CLASSES('splitter splitter_vert')
            ->READY('{$}.splitter({horiz:true})');

        $right = FRAME('right',$middle)->CLASSES('layout')
            
            ->ALIGN('
                $.each({$}.children(),function(i,o){
                    if (JX.visible(o)){
                        JX.stretch(o,{margin:5});
                        return true;
                    }
                });');
        $consoleFrame = FRAME('consoleFrame',$right)->CLASSES('layout')->STYLE('height:200px');
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
                    /*activatePanel(Qs.'.$id.'_panel);*/
                    jconsole("clear");
                    Qs.codeFrom.text({$}.text());

                    Qs.code.html("<xmp>"+codeNorm(code)+"</xmp>");
                    '.$click.';
                    
                    Ws.align();
                }
            });');
        /*    
        $panel = FRAME($id.'_panel',FRAME('right'))    
        ->CLASSES('item_panel')
        ->STYLE('position:absolute;display:none');
        */
        
        return array('item'=>$item,'panel'=>$panel);
    }
    

    public function CONTENT(){
        $this->layout();
        $this->plugin();

        $this->css();
        $this->javascript();
        $this->menu();
        
        FRAME('top')->VALUE('grid');
        FRAME('left')->STYLE('width:220px');
        FRAME('right')->STYLE('width:270px');
        FRAME('codeFrame')->STYLE('height:150px');
        FRAME('consoleFrame')->STYLE('height:600px');
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
    
    $app->title = 'grid';
    $app->RUN();
    //echo $Application->debug_info();
    

}

?>
    