/*global ut*/

/**
 * стековый признак блокировки на выполнение
 * Ex1:
 * 
 *   var a = new jlock();// создали новый объект
 *   a.lock();
 * 
 *   if (a.can()){
 *     //TO DO   
 *   }
 *   a.unlock(function(){
 *        //code on unlock counter == 0   
 *    });
 * 
 * Ex2:
 *   var a = new jlock();// создали новый объект
 *   a.lock('align');
 * 
 *   if (a.can('align')){
 *      //TO DO   
 *   }
 *   a.unlock('align',function(){...});
 * 
 * Ex3:
 *   var a = new jlock();// создали новый объект
 *   a.lock(['align','change']);
 * 
 *   if (a.can(['align',change])){
 *      //TO DO   
 *   }
 *   a.unlock(['align','change'],function(){ ....});
 * 
 * 
 * 
*/

function jlock(){
    
    var t=this;
    t.common=ut.id('common');
    
    t.funcs = [];
    t.funcs[t.common] = 0;
    
}

jlock.prototype.lock=function(name,onstart){
    
    var t = this,n=[],i,need_start = true;
    
    if (name===undefined)
        n=[t.common];
    else if (typeof(name)==='string')
        n=[name];
    else if (typeof(name)==='function'){
        n=[t.common];
        onstart = name;
    }else 
        n = name;

    for(i=0;i<n.length;i++){
        
        if (t.funcs[n[i]]===undefined)
            t.funcs[n[i]] = 0;

        t.funcs[n[i]]+=1;
        
        if (t.funcs[n[i]]!==1) 
            need_start = false;
        
    }    
    
    if ((need_start)&&(onstart)) onstart();
    
    return need_start;
};

jlock.prototype.unlock=function(name,onstop){
    var t = this,n=[],i,need_stop = true;
    
    if (name===undefined)
        n=[t.common];
    else
    if (typeof(name)==='string')
        n=[name];    
    else
    if (typeof(name)==='function'){
        n=[t.common];    
        onstop = name;
    }else
        n=name;

    for(i=0;i<n.length;i++){
        
        if (t.funcs[n[i]]===undefined){
            console.error('jlock.unlock:  func '+n[i]+' not exists');
            t.funcs[n[i]] = 0;
            need_stop = false;
        }    

        t.funcs[n[i]]-=1;
        
        if (t.funcs[n[i]]<0){
            console.error('jlock.unlock:  loop call '+n[i]+': '+t.funcs[n[i]]);
            need_stop = false;
        }
        if (t.funcs[n[i]]!==0)
            need_stop = false;
    }    
    
    if ((need_stop)&&(onstop)) onstop();
    
    return need_stop;

};
jlock.prototype.can=function(name){
    var t = this,n=[],i,res = true;
    if (name===undefined)
        n=[t.common];
    else if (typeof(name)==='string')
        n=[name];
    else
        n=name;

    for(i=0;i<n.length;i++){
        
        if ((t.funcs[n[i]]!==undefined)&&(t.funcs[n[i]]>0)){
            res=false;
            break;
        }
    }    
    
    return res;

};

