/*global ut,$*/
/**
 * библиотека управления загрузкой файлов на сервер
*/ 
 
 
/* client side:
    //open select file dialog
    mupload.open({
        onFileSelect:function(o){
            console.info(o.file);
        }     
    });
 
    // run upload 
    if (mupload.ready()){
        mupload.upload({
            onStart:function(o){
                console.info(o.file);                          
           },
           onStop:function(o){
                if (o.status==="ok"){
                    console.info(o.file);
                }else{
                    console.info("error");
                }
            }
        });
    }
  
*/
 
 
/* server side:
<?php
    
    $varName = 'mupload'; 
    $uploadDir = '../upload_files/';
    $uploadFile = $uploadDir.basename($_FILES[$varName]['name']);
     
    if (move_uploaded_file($_FILES[$varName]['tmp_name'], $uploadFile)) 
        echo 1;
    else
        echo 0;
?>
*/ 
var mupload={
    
init:function(o){
    var t=mupload;
    if (!t.param){
    
        t.param = $.extend(true,{
            body:$("body"),
            id:ut.id('mupload'),
            frame:undefined,
            form:undefined,
            btnDialog:undefined,
            btnUpload:undefined,
         
            url:ut.hrefPath()+'server.php',
            maxFileSize:0,
            varName:'mupload',
            
            onStart:undefined,
            onStop:undefined,
            onFileSelect:undefined,
        },o);
    
        t._createFrame();
        t._initFrame();
    
        t._event();
        t._put(t.param);
    }else
        t._put(o);
    
},
fileName:function(o){
    var t=mupload;
    t.init(o);

    return ut.extFileName(t.param.btnDialog.val());
},    
/** открыть диалог для выбора файла*/ 
open:function(o){
    var t=mupload;
    t.init(o);

    t.param.btnDialog.trigger("click");
},
/* запустить механизм загрузки */
upload:function(o){
    var t=mupload;
    t.init(o);
    var p = t.param;
    
    if (!t.readyToUpload()) return;
    p._prevFile = t.fileName();
    if (p.onStart) p.onStart({sender:t,file:p._prevFile});
    
    p.btnUpload.trigger("click");
},

/* проверка возможности запуска механизма загрузки */
readyToUpload:function(o){
    var t=mupload;
    t.init(o);

    return (t.fileName()!=='');
},
_put:function(o){
    if (!o) return; 
    
    var t=mupload;
    $.each(o,function(n,v){
        t._attr(n,v);
    });
},
_attr:function(n/*v*/){
    /*-----------------------------------*/
    if (arguments.length===0) 
        return;
    var t=this,p=t.param,v,r=(arguments.length===1);
    if (!r) 
        v=arguments[1];

    /*-----------------------------------*/
    if (n==='onStart'){
        if (r) 
            return p.onStart;
        else    
            p.onStart=v;
    }
    /*-----------------------------------*/
    if (n==='onStop'){
        if (r) 
            return p.onStop;
        else    
            p.onStop=v;
    }
    /*-----------------------------------*/
    if (n==='onFileSelect'){
        if (r) 
            return p.onFileSelect;
        else    
            p.onFileSelect=v;
    }
    /*-----------------------------------*/
    if (n==='maxFileSize'){
        if (r) 
            return p.maxFileSize;
        else{    
            p.maxFileSize=v;
            p.form.find("#maxFileSize").attr("MAX_FILE_SIZE",v);
        }    
    }
    /*-----------------------------------*/
    if (n==='url'){
        if (r) 
            return p.url;
        else{    
            p.url=v;
            p.form.attr("action",p.url);
        }    
    }
    /*-----------------------------------*/
    if (n==='varName'){
        if (r) 
            return p.varName;
        else{    
            p.varName=v;
            p.btnDialog.attr("name",p.varName);
        }    
    }
    /*-----------------------------------*/
},
_createFrame:function(){
    var t=mupload,p=t.param;
    p.body.append('<iframe id="'+p.id+'" style="display:none"></iframe>');
    p.frame = p.body.find("#"+p.id);
},

_event:function(){
    var t=mupload,p=t.param;
    p.frame.on("load",function(){
        
        var res = p.frame.contents().text();
        
        
        t._initFrame();                    
        if (p.onStop) p.onStop({sender:t,file:p._prevFile,status:(res=="1"?"ok":"error")});
        p._prevFile = "";
        
    });
},
_initFrame:function(){
    var t=mupload,p=t.param, 
    c = '<form id="form_'+p.id+'" enctype="multipart/form-data" action="'+p.url+'" method="POST">'+
        '<input id = "maxFileSize" type="hidden" name="MAX_FILE_SIZE" value="'+p.maxFileSize+'" />'+
        '<input id="btnDialog" name="'+p.varName+'" type="file" />'+
        '<input id="btnUpload" type="submit" value="send" />'+
    '</form>';
    
    /*p.frame = p.body.find("#"+p.id);        */
    p.frame.contents().find("body").html(c);
    

    p.form      = p.frame.contents().find("#form_"+p.id);
    p.btnDialog = p.frame.contents().find("#btnDialog");
    p.btnUpload    = p.frame.contents().find("#btnUpload");
            
    p.btnDialog.on("change",function(){
        if (p.onFileSelect) p.onFileSelect({sender:this,file:t.fileName()});                   
    });


}

};



    