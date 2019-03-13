/** 
 * объект обслуживания сессии
 * работает вместе с модулем MOD_SESSION
 * 
*/

var session={
 /** признак, что сессия включена */
 enable:false,
 /** имя переменной содержащей токен */
 tokenName:"token",
 /** имяпеременной содержащей идентификатор браузера */
 devName:"dev",
 /** идентификатор браузера */
 dev:null,
 /** токен */
 token:null,
 /** тип сессии session - только в одной вкладке и только на время сессии , local - действует и после полного закртия браузера */
 type:"session",
 /** событие на момент начала сессии */
 onStart:undefined,
 /** событие на момент закрытия сессии */
 onStop:undefined,
 
 _handler:new jhandler(),
 
 _events:['SESSION_INIT','SESSION_AUTORIZE','SESSION_STOP','SESSION_DATA_CHECK'],
 
 data:{},
/** 
 * автоматический запуск сессии ( к примеру при старте ), 
 * если токен существует, то будет отправлен запрос на возможность его использования 
*/ 
auto:function(done){
    var t=session;
    
    /*инициализируем устройство */
    if (!bdata.exist({key:t.devName,type:"local"})){
        t.dev = ut.random(1000,9999)+"-"+ut.random(1000,9999)+"-"+ut.random(1000,9999)+"-"+ut.random_str(4);
        bdata.set({key:t.devName,val:t.dev,type:"local"});
    }else
        t.dev = bdata.get({key:t.devName,type:"local"});
    
    if (Ws.share.session===undefined) 
        Ws.share.session = {};
    
    Ws.share.session.dev = t.dev;
    
    
    /*отправляем запрос на авторизацию*/
    if (bdata.exist({key:t.tokenName,type:t.type})){    
        
        Ws.ajax({
            id:'SESSION_INIT',
            value:{
                token:bdata.get({key:t.tokenName,type:t.type}),
                dev:t.dev
            },
            error:
            function(){
                if (done) done();
            },
            done:
            function(data){
                if (data.res==1)
                    t._start(data);
                else
                    t.stop();
                
                if (done) done();    
                    
            }
        });
        
    }else{
        if (done) done();
    }    
},
/** авторизация по данным пользователя 
 * данные будут отправлены в php:: Autorize->fromData(data,dev)
*/
start:function(data,done){
    var t = session;

    Ws.ajax({
        id:'SESSION_AUTORIZE',
        value:{
            data:data,
            dev:t.dev
        },
        error:
        function(){ 
            if (done) done();
        },
        done:
        function(data){
            if (data.res==1)
                /*console.info(data);*/
                t._start(data);
                
            if (done) done();
        }
    });
},
/** остановка сессии */
stop:function(done){
    var t=session,h=t._handler;    
    if (t.enable)
    Ws.ajax({
        id:'SESSION_STOP',
        value:{
            token:t.token,
            dev:t.dev
        },
        error:
        function(){ 
            if(done) done();
        },
        done:
        function(data){

            if (data.res==1){
                t._clearData();
                
                if (t.onStop) t.onStop();
                h.do('stop');
                h.do('change');
            }else
                console.error('error',arguments);
            
            if(done) done();
        }
    });

    
},
_clearData:function(){
    var t=session;
    if (Ws.share.session!==undefined)
        delete Ws.share.session.token;
                    
    bdata.del({key:t.tokenName,type:t.type});
    t.enable = false;
    t.token = null;
    t.data = {};
    
},
/** проверка данных пользователя 
 * данные будут переданы в Autorize->checkData(data,dev)
 */ 
check:function(data,done){
    var t = session;
    

    Ws.ajax({
        id:'SESSION_DATA_CHECK',
        value:{
            data:data,
            dev:t.dev
        },
        error:
        function(){ 
            if (done) done("error"); 
        },
        done:
        function(data){
            if (done) done(data);
        }
    });

    
},
/** запуск сессии (запускается либо из auto() или start()*/
_start:function(o){
    var t = session,h=t._handler;
    
    if (Ws.share.session===undefined) 
        Ws.share.session = {};
    
    Ws.share.session.token = o.token;
    
    t.token = o.token;
    bdata.set({key:t.tokenName,type:t.type,val:o.token});
    t.enable = true;

    t.data = o;

    if (t.onStart) t.onStart();
    
    h.do('start');
    h.do('change');

},
_fromAjax:function(from,id){
    var t=session,h=t._handler;
    if ((t.enable)&&(from.enable!=1)){
        
        if (t._events.indexOf(id)==-1){
            t._clearData();
            if (t.onStop) 
                t.onStop();
            h.do('stop');
            h.do('change');
        }    
        
    }
},
attach:function(toEvent,o){
    var t = session,h=t._handler,a=false;

    if (typeof o==='function')
       o={func:o};
  
    if (typeof o==='object'){
       o.group = toEvent;
       return h.add(o).name;
    }
  
    return false;    
},

detach:function(name){
    var t = session,h=t._handler;
    h.remove({name:name,group:'start'});
    h.remove({name:name,group:'stop'});
    h.remove({name:name,group:'change'});
}
};