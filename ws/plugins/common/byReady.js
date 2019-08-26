/**
 * Ассинхронная инициализация
 * Ф-ция byReady позволяет определить блок кода action, который будет выполнен после того как будут реализованы некоторые условия
 * @param {function|string|undefined} 
 *      action - "function" - ф-ция, которая будет выполнена по выпонению условий conditions, 
 *      string - имя блока завершившего свои действия 
 *      undefined - проверка состояний для условий определенных ф-цией
 * 
 * @param {undefined|string|array} -
 *      undefined   - игнорируется
 *      string      - список блоков через "," по завершению отработки которых будет выполнен код action
 *      array       - список блоков по завершению отработки которых будет выполнен код action
 *      func        - ф-ция условия, если вернет true значит будет запущен соотвествующий блок action
 * 
 * К примеру есть три объекта, у которых ассинхронно вызывается блок init, необходимо, чтобы вызов блока init третьего объекта произошел строго после 
 * вызовов первых двух:
 * 
 * var obj1 = {
 *      init:function(){
 *          console.info('obj1');       
 *          byReady('obj1');
 *      }     
 * }
 * ....
 * var obj2 = {
 *      init:function(){
 *          // code        
 *          byReady('obj2');
 * 
 *      }     
 * }
 * 
 * var obj3 = {
 *      init:function(){
 *          byReady(()=>{
 *              
 *              //этот код будет выполнен после отработки obj1.init() и obj2.init();
 *              console.info('obj3');    
 * 
 *          },'obj1,obj2');
 *      }     
 * }
 * 
 * obj3.init(); // код в этом блоке буде запущен, только после следующих двух
 * obj2.init();
 * obj1.init();
 * 
 * 
 * Пример определения условия выполнения через ф-цию:
 * var obj1 = {
 *      def:false,
 *      init:function(){
 *          obj1.def = true;     
 *          byReady('obj1');
 *      }     
 * }
 * ....
 * var obj2 = {
 *      def:false,
 *      init:function(){
 *          obj2.def = true;
 *          byReady('obj2');
 * 
 *      }     
 * }
 * 
 * var obj3 = {
 *      init:function(){
 *          byReady(function(){
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

function byReady(action = undefined,conditions = undefined){
    var t=byReady;
    if (t.param === undefined){
        t.param = {
            list:[],
            wasted:[],
            lock:false,
            repeat:true,
        };
    }
    let p = t.param,
        typeAction=typeof action,
        typeCondition = typeof conditions;

    // ----------------------------------------------------------------
    if (typeAction==='function'){ // регистрация ф-ции 
        if ((conditions === undefined)||(conditions === '')||(conditions === null)){
            action();
            return true;
        }
        
        if (typeCondition==='string')
            conditions = conditions.split(',').unique();

        if (typeCondition==='object') //is array
            conditions = conditions.unique();
        
        // сохраняем в коллекции
        p.list.push({action,conditions});
        
    }
    // ----------------------------------------------------------------
    if (typeAction==='string'){ // указываем, что ф-ция отработана
        p.wasted.push(action);
        p.wasted.unique();
    }
    // ----------------------------------------------------------------
    
    if (!p.lock){
        p.lock = true;    
        
        p.list = p.list.filter(item=>{
            let leave = true;            
            if (typeof item.conditions === 'object'){ // if array
                // убираем из массива все элементы которорые уже отработаны
                item.conditions = item.conditions.filter(v=>p.wasted.indexOf(v)===-1);
                if (item.conditions.length === 0){ // все влияющие ф-ции отработаны
                    try{
                        item.action();
                    }catch(e){
                        console.error(e);
                    }
                    leave = false;
                }
            }else
            if (typeof item.conditions === 'function'){
                
                try{
                    if (item.conditions()){
                        item.action();
                        leave = false;
                    }
                    
                }catch(e){
                    leave = false;
                    console.error(e);
                }
                
                
            }
            return leave;
        });
        p.lock = false;
        if (p.repeat){
            
            p.repeat = false;
            byReady();
        }
        
    }else{
        p.repeat = true;    
    }
    // ----------------------------------------------------------------
    return true;    
}
/**
 * используется для инициализации упорядоченной загрузки модулей.
 * модуль должен содержать 
 *      метод init()  - выполнится один раз
 *      свойство loadAfter - условие после которого можно запускать модуль
 * 
 * @param string name - имя модуля
 * Ex: 
 *     var mod1 = {
 *          loadAfter:'Qs',
 *          init(){
 *              ...
 *          }
 *      }
 *      byReady.load('mod1');
 *
 * Ex: 
 *     var mod1 = {
 *          init(){
 *              ...
 *          }
 *      }
 *      byReady.load('mod1','Ws');
 * 
 */ 
byReady.load = function(name,loadAfter = undefined){
    let module = window[name];
    if (module){
        byReady(()=>{
            try{
                module.init();
                module.modLoad = true;
                byReady(name);
            } catch (e){
                console.error(name);
            }
        },(loadAfter!==undefined?loadAfter:module.loadAfter));
    }
};

/**
 * возвращает признак был ли уже загружен модуль с именем name
 */ 

byReady.isLoad = function(name){
    if (byReady.param)
        return byReady.param.wasted.indexOf(name)>=0;
    return false;
};
