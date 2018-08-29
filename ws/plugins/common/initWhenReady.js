/**
 * Ассинхронная инициализация
 * Ф-ция initWhenReady позволяет определить блок кода action, который будет выполнен после того как будут реализованы некоторые условия
 * @param {function|string|undefined} 
 *      action - "function" - ф-ция, которая будет выполнена по выпонению условий cond, 
 *      string - имя блока завершившего свои действия 
 *      undefined - проверка состояний для условий определенных ф-цией
 * 
 * @param {undefined|string|array} -
 *      undefined - игнорируется
 *      string      - список блоков через "," по завершению отработки которых будет выполнен код action
 *      array       - список блоков по завершению отработки которых будет выполнен код action
 * 
 * К примеру есть три объекта, у которых ассинхронно вызывается блок init, необходимо, чтобы вызов блока init третьего объекта произошел строго после 
 * вызовов первых двух:
 * var obj1 = {
 *      init:function(){
 *          // code        
 *          initWhenReady('obj1');
 * 
 *      }     
 * }
 * ....
 * var obj2 = {
 *      init:function(){
 *          // code        
 *          initWhenReady('obj2');
 * 
 *      }     
 * }
 * 
 * var obj3 = {
 *      init:function(){
 *          initWhenReady(function(){
 *               
 *              //этот код будет выполнен после отработки obj1.init() и obj2.init();
 * 
 *          },'obj1,obj2');
 *      }     
 * }
 * 
 * obj3.init(); // код в этом блоке буде запущен, только после следующих двух
 * obj2.init();
 * obj1.init();
 * 
 * Пример определения условия выполнения через ф-цию:
 * var obj1 = {
 *      def:false,
 *      init:function(){
 *          obj1.def = true;     
 *          initWhenReady();
 *      }     
 * }
 * ....
 * var obj2 = {
 *      def:false,
 *      init:function(){
 *          obj2.def = true;
 *          initWhenReady('obj2');
 * 
 *      }     
 * }
 * 
 * var obj3 = {
 *      init:function(){
 *          initWhenReady(function(){
 *               
 *              //этот код будет выполнен после отработки obj1.init() и obj2.init();
 * 
 *          },function(){
 *              if ((typeof obj1!==undefined)&&(typeof obj2!==undefined))
 *                  return (obj1.def&&obj2.def);
 *              return false; 
 *          });
 *      }     
 * }
 * 
 * 
 */ 

function initWhenReady(action,cond){
    var t=this,ty=typeof action,i,j,it,idx,need;
    if (t.param===undefined){
        t.param = {
            list:[],
            conds:[]
        };
        
    }
    
    if (ty==='function'){
        if (cond === undefined){
            console.error('initWhenReady: need defined condition...');
            return false;
        }
        
        if (typeof cond==='string')
            cond = cond.split(',');
        if (typeof cond==='object'){
            cond = cond.unique();
            i=0;
            while(i<cond.length){
                if (t.param.conds.indexOf(cond[i])>=0)
                    cond.splice(i,1);
                else
                    i++;
            }
        }
        if (cond.length===0)
            action();
        else    
            t.param.list.push({action:action,cond:cond});
        
        return true;    
    }
    
    if (ty==='string'){
        t.param.conds.push(action);
        t.param.conds = t.param.conds.unique();
        
        for(i=0;i<t.param.list.length;i++){
            it = t.param.list[i];
            if (typeof it.cond==='object'){
                idx = it.cond.indexOf(action);
                if (idx>=0){ 
                    it.cond.splice(idx,1);
                    if (it.cond.length===0){
                        it.cond = false;
                        it.action();
                        it.action=false;
                        
                    } 
                }    
            }
        }
        return true;
    }
    
    if (ty==='undefined'){
        i = 0;
        while (i<t.param.list.length){
            it = t.param.list[i];
            if (typeof it.cond==='function'){
                if (it.cond()===true){
                    it.cond = false;
                    it.action();
                    it.action=false;
                    t.param.list.splice(i,1);
                    i--;
                }
            }    
            i++;
        }    
    }
    
    
    
    
}