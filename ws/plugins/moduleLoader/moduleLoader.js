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
        this.onInitList = {}; // список callback привязаных к алиасу ( вызовы по конкретным модулям)
        this.onInitFunc = []; // список callback не привязанных к алиасу (вызовы при init в любом модуле)
        this.dev = false; // признак, что модули подгружаются в режиме development
    }
    /** загрузка молуля 
     * @param {string|object} - относительный путь к модулю
     * @returns {Promise}
    */
    async load(p) {
      
        let item = this._item(p) || p;
        let varName = p.varName?p.varName:item.varName;
        let name = this._getRealName(item);
        let storyName = name;
        let dev = false;

        const is_first_load = !scriptLoader.exist(name);
        
        if (is_first_load && this.dev) {
            dev  = await this._devInit(name);
            name = dev?dev.name:name;
        }
        
        const load =  await scriptLoader.load(name);
        
        if (is_first_load && this.dev && dev) {
            await this._devFree(dev);
            scriptLoader._replaceUrl(name,storyName);
            name = storyName;
        }

        // при первой загрузке генерируем событие onInit
        if (is_first_load){
            
            if (item.alias && item.alias in this.onInitList){
                this.onInitList[item.alias].map((f)=>{
                    try{    
                        f(item);
                    }catch(e){
                        console.warn(e);
                    }
                });
            };
            
            this.onInitFunc.map((f)=>{
                try {   
                    f(item);
                } catch (error) {
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
    /**  преобразовние имени и подмена файла*/ 
    async _devInit(name){
        let find = ()=>{
            for (let i = 0;i<this.dev.length;i++){
                if (name.indexOf(this.dev[i])>-1){
                    return true;
                }
            }
            return false
        }
        if (this.dev === true || find()){
            return await Ws.get({
                id:'MODULELOADER_INIT_DEV',
                value:{name},
            });
        }
        return undefined;
           
    }
    async _devFree(dev){
        return await Ws.get({
            id:'MODULELOADER_FREE_DEV',
            value:dev,
        });
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
    /** экспорт глобальных переменных ( клон add) */
    export(o){
        this.add(o);
    }
    /** импорт глобальных (не тоже самое что load) переменных добавленных export 
     *  если переменная не существует, то будет Exception
    */
    import(name){
        if (name in this.vars)
            return this.vars[name];
        else{
            const msg = 'import not exists var "'+name+'"';
            console.error(msg);
            throw msg;
        }    
            
    }
    /** 
     * onInit({mod1(){
     * }}
     * or 
     * onInit(()=>{
     * })
    */
    onInit(o){
        let type = typeof(o);
        if (type === 'object'){
            let aliases = Object.keys(o);
            aliases.map(alias=>{
                let evs = Object.keys(this.onInitList);
                if (evs.indexOf(alias)===-1)
                    this.onInitList[alias] = [];
                this.onInitList[alias].push(o[alias]);
            });
        }else
        if (type === 'function'){
            this.onInitFunc.push(o);
        }
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
                const item = this._item(o);
                return param.call(this.vars[item.varName],...args);
            } catch (error) {
                console.info(error);    
            }
        }
        return defalt;
                    
    }
}

const moduleLoader = new ModuleLoader();
