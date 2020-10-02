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
    }
    /** загрузка молуля 
     * @param {string|object} - относительный путь к модулю
     * @returns {Promise}
    */
    async load(p) {
      
        let item = this._item(p) || p;
        let varName = p.varName?p.varName:item.varName;
        let name;
        
        if(item!==undefined){
            if (item.path)
                name = this.renderPath+item.path;
            else
                name = this.hrefPath+item.source;
        }
        const load =  await scriptLoader.load(name);
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
}

const moduleLoader = new ModuleLoader();
