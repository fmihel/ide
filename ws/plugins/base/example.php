<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL & ~E_NOTICE);
    
if(!isset($Application)){
    require_once '../../../ws/utils/application.php';
    
    $Application->LOG_ENABLE        = true;
    $Application->LOG_TO_ERROR_LOG  = false; 
    
    require_once UNIT('ws','ws.php');
};

require_once UNIT('plugins','base/base_mod.php');
require_once UNIT('utils','framet.php');


class K_TOVAR_TABLE extends _table {
    const NAME  ="K_TOVAR";
    const ALIAS ="KT";
    const INDEX ="ID_K_TOVAR";
    /*c:types*/
    public static $types=array(
        "ID_K_TOVAR"    =>"int",
        "ID_K_WIN_KIND" =>"int",
        "IS_PRODUCT"    =>"int",
        "NAME"          =>"string",
        "ID_ED_IZM"     =>"int",
        "IMAGE_TOV"     =>"blob",
        "NOTE"          =>"string",
        "ARCH"          =>"int",
        "LAST_MODIFY"   =>"date",
        "DESCR_BEFORE"  =>"blob",
        "DESCR_AFTER"   =>"blob",
        "NOM_PP"        =>"int",
        "CAN_CUT"       =>"int",
        "PRICELIST_KIND"=>"int",
    );
    /*c:update*/
    public static $update=array(
        "ID_K_WIN_KIND",
        "IS_PRODUCT",
        "NAME",
        "ID_ED_IZM",
        "IMAGE_TOV",
        "NOTE",
        "ARCH",
        "LAST_MODIFY",
        "DESCR_BEFORE",
        "DESCR_AFTER",
        "NOM_PP",
        "CAN_CUT",
        "PRICELIST_KIND",
    );
    public static $select=array(
        /*c:default*/
        "default"=>array(
            "ID_K_TOVAR",
            "ID_K_WIN_KIND",
            "IS_PRODUCT",
            "NAME",
            "ID_ED_IZM",
            "NOTE",
            "ARCH",
            "LAST_MODIFY",
            "NOM_PP",
            "CAN_CUT",
            "PRICELIST_KIND",
        ),
        /*c:myData*/
        "myData"=>array(
            "NAME",
            "IMAGE_TOV",
            "NOTE",
            "ARCH",
        ),
        
    );
    /*c:noAlias*/
    public static $noAlias=array(
        "ID_K_TOVAR",
        "ID_K_WIN_KIND",
        "ID_ED_IZM",
    );
};

class K_TOVAR_DETAIL_TABLE extends _table {
    const NAME  ="K_TOVAR_DETAIL";
    const ALIAS ="KTD";
    const INDEX ="ID_K_TOVAR_DETAIL";
    /*c:types*/
    public static $types=array(
        "ID_K_TOVAR_DETAIL"=>"int",
        "ID_K_TOVAR"       =>"int",
        "ID_K_COLOR"       =>"int",
        "ART"              =>"string",
        "SIZE_BORDER"      =>"float",
        "QUANT"            =>"float",
        "NOTE"             =>"string",
        "ARCH"             =>"int",
        "LAST_MODIFY"      =>"date",
        "REPLACE_INFO"     =>"string",
    );
    /*c:update*/
    public static $update=array(
        "ID_K_TOVAR",
        "ID_K_COLOR",
        "ART",
        "SIZE_BORDER",
        "QUANT",
        "NOTE",
        "ARCH",
        "LAST_MODIFY",
        "REPLACE_INFO",
    );
    public static $select=array(
        /*c:default*/
        "default"=>array(
            "ID_K_TOVAR_DETAIL",
            "ID_K_TOVAR",
            "ID_K_COLOR",
            "ART",
            "SIZE_BORDER",
            "QUANT",
            "NOTE",
            "ARCH",
            "LAST_MODIFY",
            "REPLACE_INFO",
        ),
    );
    /*c:noAlias*/
    public static $noAlias=array(
        "ID_K_TOVAR_DETAIL",
        "ID_K_TOVAR",
        "ID_K_COLOR",
    );
};

class K_COLOR_TABLE extends _table {
    const NAME  ="K_COLOR";
    const ALIAS ="KCO";
    const INDEX ="ID_K_COLOR";
    /*c:types*/
    public static $types=array(
        "ID_K_COLOR" =>"int",
        "NAME_SHORT" =>"string",
        "NAME_FULL"  =>"string",
        "SAMPLE"     =>"blob",
        "NOTE"       =>"string",
        "LAST_MODIFY"=>"date",
        "ARCH"       =>"int",
    );
    /*c:update*/
    public static $update=array(
        "NAME_SHORT",
        "NAME_FULL",
        "SAMPLE",
        "NOTE",
        "LAST_MODIFY",
        "ARCH",
    );
    public static $select=array(
        /*c:default*/
        "default"=>array(
            "ID_K_COLOR",
            "NAME_SHORT",
            "NAME_FULL",
            "NOTE",
            "LAST_MODIFY",
            "ARCH",
        ),
    );
    /*c:noAlias*/
    public static $noAlias=array(
        "ID_K_COLOR",
    );
};

class USER_TABLE extends _table {
    const NAME  ="USER";
    const ALIAS ="U";
    const INDEX ="ID_USER";
    /*c:types*/
    public static $types=array(
        "ID_USER"       =>"int",
        "ID_DEALER"     =>"int",
        "NAME"          =>"string",
        "EMAIL_LOGIN"   =>"string",
        "PASS"          =>"string",
        "DATE_CREATE"   =>"date",
        "ENABLE"        =>"int",
        "LAST_MODIFY"   =>"date",
        "UUID"          =>"string",
        "REL_EMAIL"     =>"string",
        "IS_MAIN"       =>"int",
        "BY_USER_MODIFY"=>"int",
    );
    /*c:update*/
    public static $update=array(
        "ID_DEALER",
        "NAME",
        "EMAIL_LOGIN",
        "PASS",
        "DATE_CREATE",
        "ENABLE",
        "LAST_MODIFY",
        "UUID",
        "REL_EMAIL",
        "IS_MAIN",
        "BY_USER_MODIFY",
    );
    public static $select=array(
        /*c:default*/
        "default"=>array(
            "ID_USER",
            "ID_DEALER",
            "NAME",
            "EMAIL_LOGIN",
            "PASS",
            "DATE_CREATE",
            "ENABLE",
            "LAST_MODIFY",
            "UUID",
            "REL_EMAIL",
            "IS_MAIN",
            "BY_USER_MODIFY",
        ),
    );
    /*c:noAlias*/
    public static $noAlias=array(
        "ID_USER",
        "ID_DEALER",
    );
};


class TWS extends WS{
    public $id = 0;
    
    public function  out($title,$code=false,$result=false){
        
        $own = FRAME('out');
        $frame = FRAME('ex'.($this->id++),$own)->CLASSES('example');
        
        FRAME('title'.($this->id++),$frame)->CLASSES('title')
        ->VALUE($title);

        if ($code)
        FRAME('out'.($this->id++),$frame)->TAG_NAME('xmp')->CLASSES('code')
        ->VALUE($code);
        
        if ($result)
        FRAME('result'.($this->id++),$frame)->TAG_NAME('pre')->CLASSES('result')
        ->VALUE($result);
        
    }
    
    public function CONTENT(){
        $own = FRAMET('
        <out {border:0px dashed silver;padding:10px;overflow:auto;position:static}>
            
        ',FRAME())
        ->READY("
            console.info(tb);
        ")->CSS('
        body{
            font-size:12px;
            margin:0px;
            padding:0px;
            border:0px;
            font-family: Roboto, sans-serif;
            
        }
        .title{
            font-size:1.2em;
            border:1px solid silver;
            padding:10px;
            background-color:#E6ECF2;
            //background: linear-gradient(to bottom, rgba(88,131,183,1) 0%,rgba(69,122,187,1) 100%);
            
            cursor:pointer;
        }
        .title:hover{
        background:#D1DDE9;
        }
        .code{
            border-left:1px solid silver;
            border-right:1px solid silver;
            border-bottom:1px solid silver;
            margin:0px;
            padding:10px;
            overflow:auto;
        }
        .result{
            border-left:1px solid silver;
            border-right:1px solid silver;
            border-bottom:1px solid silver;
            margin:0px;
            background-color:#FFFBE6;
            padding:10px;
            overflow:auto;
            
        }
        .keyWord{
            color:red;
            font-size:1.15em;
            font-weight:bold;
        }
        ')->READY('
        
        $(".title").on("click",function(o){
            var t = $(o.target);
            var c = t.next();
            var r = c.next();
            if (JX.visible(c)){
                c.slideUp(300);
                r.slideUp(300);
            }else{
                c.slideDown(300);
                r.slideDown(300);
            }
        });
        
        $(".code").slideUp(0);
        $(".result").slideUp(0);

        var r = $(".result");
        var rep = ["select","from","join","on","left outer","update","set","insert","where","values","into"];
        $.each(r,function(i,_t){
            var o=$(_t);
            var str = o.text();
            for(var i=0;i<rep.length;i++){
                str = str.replaceAll(rep[i],"<span class=`keyWord`>"+rep[i]+"</span>");
            }    
            o.html(str);
        });
        
        ');
        
        
        baseSetup::$refactoring = true;
        
        /*c:select -------------------------------------------------*/
        $result = K_TOVAR_TABLE::select("where ID_K_TOVAR = 10");
        $this->out('<b>select</b>: Простая выборка',
        '
            $q = K_TOVAR_TABLE::select("where ID_K_TOVAR = 10");
        ',$result);

        /*c:select -------------------------------------------------*/
        $result = K_TOVAR_TABLE::select("where ID_K_TOVAR = 10",'myData');
        $this->out('<b>select</b>: Выборка из нестандартного набора',
        '
            $q = K_TOVAR_TABLE::select("where ID_K_TOVAR = 10","myData");
        ',$result);
            
        /*c:select_macro -------------------------------------------------*/
        
        $result = K_TOVAR_TABLE::select("where {INDEX} = 10",'myData');
        $this->out('<b>select</b>: макро команды в select',
        '
            {INDEX} - индексное поле
            $q = K_TOVAR_TABLE::select("where {INDEX} = 10","myData");
        ',$result);

        /*c:base::select -------------------------------------------------*/
        $q = base::select(array(
            K_TOVAR_TABLE=>"myData",
            K_TOVAR_DETAIL_TABLE,
            K_COLOR_TABLE
            ),
            K_TOVAR_TABLE::join(K_TOVAR_DETAIL,'ID_K_TOVAR','ID_K_TOVAR')->leftOuter(K_COLOR_TABLE,K_TOVAR_DETAIL_TABLE,'ID_K_COLOR','ID_K_COLOR'),
            "where K_TOVAR_TABLE::ARCH=K_TOVAR_DETAIL_TABLE::ARCH"
        );
        $this->out('<b>base::select</b>: select с использованием нескольких таблиц',
        '
        $q = base::select(array(
            K_TOVAR_TABLE=>"myData", /*используем нестандартный набор */
            K_TOVAR_DETAIL_TABLE,
            K_COLOR_TABLE
            ),
            K_TOVAR_TABLE::join(K_TOVAR_DETAIL,"ID_K_TOVAR","ID_K_TOVAR")             /*если таблицы имеют схожие поля, то поля можно не указывать связка по ним будет автматически*/
            ->leftOuter(K_COLOR_TABLE,K_TOVAR_DETAIL_TABLE,"ID_K_COLOR","ID_K_COLOR"),/* последующие join/leftOuter по умолчанию будут привязываться к последней используемой таблице */
            "where K_TOVAR_TABLE::ARCH=K_TOVAR_DETAIL_TABLE::ARCH"
        );
            
        ',$q);
        
        /*c:base::select_short -------------------------------------------------*/
        $result = base::select(array(
            K_TOVAR_TABLE,
            K_TOVAR_DETAIL_TABLE),
            K_TOVAR_TABLE::join(K_TOVAR_DETAIL),
            "where K_TOVAR_TABLE::ARCH=K_TOVAR_DETAIL_TABLE::ARCH");
        $this->out('<b>base::select(short)</b>: select с использованием нескольких таблиц и join',
        '
        
        
        $q = base::select(array(
            
            K_TOVAR_TABLE,
            K_TOVAR_DETAIL_TABLE
            
            ),
            
            K_TOVAR_TABLE::join(K_TOVAR_DETAIL),
            
            "where K_TOVAR_TABLE::ARCH=K_TOVAR_DETAIL_TABLE::ARCH"
        );
            
        ',$result);
        
        /*c:base::select_leftOuter -------------------------------------------------*/
        
        $q = base::select(array(
            K_TOVAR_TABLE,
            K_TOVAR_DETAIL_TABLE,
            K_COLOR_TABLE
            ),
            K_TOVAR_TABLE::join(K_TOVAR_DETAIL)->leftOuter(K_COLOR_TABLE),
            "where K_TOVAR_TABLE::ARCH=K_TOVAR_DETAIL_TABLE::ARCH"
        );
        $this->out('<b>base::select</b>: select с использованием нескольких таблиц и leftOuter',
        '
        
        $q = base::select(array(
            K_TOVAR_TABLE,
            K_TOVAR_DETAIL_TABLE,
            K_COLOR_TABLE
            ),
            K_TOVAR_TABLE::join(K_TOVAR_DETAIL)->leftOuter(K_COLOR_TABLE),
            "where K_TOVAR_TABLE::ARCH=K_TOVAR_DETAIL_TABLE::ARCH"
        );
            
        ',$q);
        
        /*c:update -------------------------------------------------*/
        
        $q = K_TOVAR_TABLE::update(array("::ID_K_TOVAR"=>1,"::NOM_PP"=>10,"::NAME"=>"tovar name"),"ID_K_TOVAR = 5");

            
        
        $this->out('<b>update</b>: создание update запроса ',
        '
        /** ВНИМАНИЕ!! данные передаваемые в запрос должны быть с префиксами, либо содержать макро команду :: */
        
        $q = K_TOVAR_TABLE::update(array("ID_K_TOVAR"=>1,"KT_NOM_PP"=>10,"KT_NAME"=>"tovar name"),"ID_K_TOVAR = 5");

        /** или */

        $q = K_TOVAR_TABLE::update(array("::ID_K_TOVAR"=>1,"::NOM_PP"=>10,"::NAME"=>"tovar name"),"ID_K_TOVAR = 5");

        ',$q);
        
        /*c:update_macro -------------------------------------------------*/
        $q = K_TOVAR_TABLE::update(array("::ID_K_TOVAR"=>1,"::NOM_PP"=>10,"::NAME"=>"tovar name"),"{INDEX} = ::");

            
        
        $this->out('<b>update</b>: макро команды ',
        '
        :: - при описании полей в данных означает, что поле может содержать префикс 
        ::NAME - значение соотвествующего поля из массива данны
        :: - (без всего) значение соотвествующего индексного поля из массива данны ( в данном примере ID_K_TOVAR)
        {INDEX} - имя индексного поля
        
        $data = array("ID_K_TOVAR"=>1,"::NOM_PP"=>10,"::NAME"=>"tovar name");
        $q = K_TOVAR_TABLE::update($data,"ID_K_TOVAR = ".$data["ID_K_TOVAR"]);
        или
        $q = K_TOVAR_TABLE::update($data,"{INDEX} = ::ID_K_TOVAR");
        или
        $q = K_TOVAR_TABLE::update($data,"{INDEX} = ::");

        ',$q);


        /*c:update_run -------------------------------------------------*/
        $q = K_TOVAR_TABLE::update(array("::ID_K_TOVAR"=>1,"::NOM_PP"=>10,"::NAME"=>"tovar name"),"{INDEX} = ::");

            
        
        $this->out('<b>update</b>: создание запроса и его выполнение',
        '
        base::startTransaction("base");
        try{
            
            K_TOVAR_TABLE::update($data,"{INDEX} = ::","base","coding");
            base::commit("base");
            
        }catch(Exception $e){
            
            base::rollback("base");
            
        }

        ',$q);

        /*c:insert -------------------------------------------------*/
        
        
        $q = USER_TABLE::insert(array('::UUID'=>"1245-6273-2838-2883"));    
        
        $this->out('<b>insert</b>: пример создания записи UUID',
        '
        base::startTransaction("base");
        try{
            
            $id = USER_TABLE::insert_uuid("base");
            
            base::commit("base");
            
        }catch(Exception $e){
            
            base::rollback("base");
            
        }

        ',$q);
        /*c:value -------------------------------------------------*/

        $this->out('<b>value</b>: получение значения поля из таблицы',
        '
            $mean = USER_TABLE::value("::NAME","{INDEX}=".5);

        ',' ');
    
    }
  
}      

if($Application->is_main(__FILE__)){
  
    $app = new TWS();
    // ----------------------------------------------------------
    //  $app->version = ''; - custom clear cache 
    //  $app->version = 'nocache'; no caching data always
    //  $app->verison = 'XXXXX'; caching data (version control)
    $app->version = ''; 
    
    // ----------------------------------------------------------
    //  $app->title - browser tab name
    $app->title = 'example';

    // ----------------------------------------------------------
    //  $app->tabColor - in mobile Chrome tab color
    $app->tabColor = '#DCDCDC';
    
    $app->RUN();

}
?>