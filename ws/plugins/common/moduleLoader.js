/** отложенная загрузка моудлей.
 *  Если запуск происходит при mode = development
 *  то модули грузяться из их мест расположения
 *  mode = production, мудули будут грузиться из папки this.renderPath(устанавливается в ws.php)
 *  mode = assemble - все модули помещаются в корень папку _assembly, если имена файлов совпадают,
 *  то будут создаваться имена с номерными индексами, для их соотвествия используется this.paths (устанавливается в ws.php)
 * 
*/
class ModuleLoader {
    constructor(){
        this.renderPath = '_render/';
        this.paths = []; // [ { source,path,varName,alias},..]
        this.hrefPath = ut.hrefPath();
        this.vars = {};
        this.cache = false;
        this.onInitList = {};
    }
    /** загрузка молуля 
     * @param {string|object} - относительный путь к модулю
     * @returns {Promise}
    */
    async load(p) {
      
        let item = this._item(p) || p;
        let varName = p.varName?p.varName:item.varName;
        let name = this._getRealName(item);
        
        const is_first_load = !scriptLoader.exist(name);
        const load =  await scriptLoader.load(name);

        // при первой загрузке генерируем событие onInit
        if (is_first_load && item.alias && item.alias in this.onInitList){
            
            this.onInitList[item.alias].map((f)=>{
                try{    
                    f(item);
                }catch(e){
                    console.warn(e);
                }
            })
            
        };
        // если есть связанная переменная, то пытаемся ее выдать
        if (varName){
            if (this.vars[varName])
                return this.vars[varName]
            else{
                const wait = ()=> new Promise((ok)=>{ setTimeout(()=>{ 
                    if (this.vars[varName])
                        ok(this.vars[varName])
                    else
                        throw "cant find varName="+varName;
                },100);});
                return wait();
            }
            
        }else   
            return load;

    }
    _getRealName(item){
        if(item!==undefined){
            if (item.path)
                return this._cacheName(this.renderPath+item.path);
            else
                return this._cacheName(this.hrefPath+item.source);
        }
        return undefined;
    }
    _cacheName(name){
        return name+((name && this.cache)?('?'+this.cache):'');    
    }
    registered(...o){
        o.map(p=>{
            try {
                let item = this._item({alias:p.alias});
                if (item)
                    throw 'alias '+p.alias+' is exists, rename alias';
                item = this._item({source:p.source});
                if (!item)
                    this.paths.push(p);
                else{
                    item.alias = p.alias;
                    item.source = p.source;
                    item.varName = p.varName;
                }
                    
            } catch (e) {
                console.error(e);
            }
        });
    }
    _setPaths(paths){
        paths.map(path=>{
            let item = this._item(path);
            if (item){
                item.source = path.source;
                item.path = path.path;
            }else{
                this.paths.push(path);
            }
        })
    }
    _item(param){
        let p;
        if (typeof param === 'string')
            p = {alias:param};
        else
            p = {...param};

        for(let i = 0;i<this.paths.length;i++){
            let item = this.paths[i];
            if ((p.alias) && (item.alias === p.alias))
                return item;
            if ((p.source) && (item.source === p.source))
                return item;
        }
        return undefined;
    }
    /** добавляет ссылку на модуль, для поиска */
    add(o){
        let names = Object.keys(this.vars);
        let in_names = Object.keys(o);
        in_names.map(name=>{
            if (names.indexOf(name)>=0){
                console.warn('module varName='+name+' is exist in moduleLoader, rename this');
            }else{
                this.vars[name] = o[name];
            }
        })
    }
    /** 
     * onInit({mod1(){
     * }}
    */
    onInit(o){
        let aliases = Object.keys(o);
        aliases.map(alias=>{
            let evs = Object.keys(this.onInitList);
            if (evs.indexOf(alias)===-1)
                this.onInitList[alias] = [];
            this.onInitList[alias].push(o[alias]);
        });
    }
    exist(o){
        try{
            const item = this._item(o);
            return item?scriptLoader.exist(this._getRealName(item)):false
        }catch(e){
            console.warn(e);
        }
        return false;
    }
    /** ф-ция несинхронного доступа к данным модуля, в случае
     * если моудль не загружен то вернет defalt
     */
    param(o,paramName=undefined,defalt=false){
        if (this.exist(o)){
            const item = this._item(o);
            if (item!==undefined){

                if (paramName===undefined)
                    return this.vars[item.varName]

                if (Array.isArray(paramName))
                    return ut.get(this.vars[item.varName],...paramName,defalt)

                return this.vars[item.varName][paramName];

            }    
        }
        return defalt;
    }
    /** ф-ция несинхронного доступа к ф-циям модуля, в случае
     * если моудль не загружен то вернет defalt
     * @param {any} идентификатор модуля
     * @param {string} имя ф-ции
     * @param {array} спиок параметров передаваемых в ф-цию
     * @param {any} значение по умолчанию если модуль не загружен или при выполнении ф-ции произошел сбой
     */
    func(o,name,args=[],defalt=false){
        let param = this.param(o,name);
        if (typeof(param) === 'function'){
            try {
                return param(...args);
            } catch (error) {
                console.info(error);    
            }
        }
        return defalt;
                    
    }
}

const moduleLoader = new ModuleLoader();
