/**
 * класс обертка для модуля создания формы, 
 * основная задача задать общий интерфейс для всех модуля работы с формой
 * Пример использования:
 * 
 * class MyForm extends formClass{
 * init($plugin){
 *      this.param = {
 *          width:300,
 *          onAlign:this.onAlign,
 *      }
 * }
 * onAlign(){
 *  //code    
 * }
 *   
 * }
 * 
 * myForm = new MyForm(); // создание класса, но не формы
 * .....
 * 
 * myForm.create();// создание формы (форма не видна)
 * myForm.open();// открытие
 * или 
 * myForm.open(); // созлание и откоытие формы
 * 
 * ...
 * myForm.close(); 
 * 
 * 
 */ 
class formClass{
    
    constructor($plugin = undefined){
        this.$plugin = $plugin;
        this.state = 'none';
        this.param={};
        this.ids = {};
        this.childs = [];
        this.$childs = [];
        this.components = {};

    }

    create($plugin = undefined){
        let t = this;
        if (t.state !== 'none')
            return;

        t.$plugin = $plugin;

        if (!t.$plugin){
            let id = ut.id('form');
            Qs.modal.append(`<div id=${id} style='position:absolute;display:none'></div>`);
            t.$plugin = Qs.modal.find('#'+id);
            t.state = 'self';
        }else
            t.state = 'outer';
        
        this.init(t.$plugin);
        t.$plugin.mform(t.param);
    }

    init($plugin){
        // abstract
    }
    done(){
        this.$plugin.remove();
        this.$plugin.mform('destroy');
    }
    open(o={}){
        this.create();
        this.$plugin.mform('open',o);
    }
    close(){
        this.create();
        this.$plugin.mform('close');
    }
    /** 
     * создает(если не создан) и возвращает уникальный id для dom элемента и ассоциирует его с name
     * Ex:
     * this.id('MY_BUTTON') - > 'MY_BUTTON829203'
     * Добавив префикс # вернем тот же id но с # (для удобства запросов $.find('#xxxx');
     * this.id('#MY_BUTTON') -> '#MY_BUTTON829203'
     * 
    */
    id(name){
        let ret = false;
        let add = '';
        
        if (name[0] === '#'){
            name = name.slice(1);
            add = '#';
        }
            
        if (!(name in this.ids))
            this.ids[name] = ut.id(name+'_');
            
        return add+this.ids[name];
    }
    /**
     * создает последовательно объекты dom на форме и jquery обертки для них. Создает переменные для них с префиксом $
     * также создается два объекта 
     * childs - массив этих обектов
     * $childs - jquery список этих объектов
     * 
     * Ex:
     * add(['label','text','img']);
     * add(['label','text','img'],{css:'mystyle',style:'position:absolute'});
     * add(['label',{text:{id:'text1',css:'myStyle'}}])
     * add({text:{css:'class'},img:{style:'diplay:none'}},{css:'mystyle'});
     * 
     * Внимание! При задании одинаковых имен, имя будет изменено!!
     */ 
    addComponents(list,def={}){
        let t=this,code = '';
        let names = [];
        
        let find=name=>{
            if (names.find(prop=>{if (name == prop) return true;}))
                return true;
            
            for(let prop in t.components)
                if (name =='$'+prop) return true;
            return false;
        };
        
        let uniq = name=>{
            let newName =name;
            let idx = 1;
            while(find(newName)){
                newName = name+'_'+idx;
                idx++;
            }
            
            return newName;
        };

        let reg=(name,tag)=>{
            
            names.push(name);
                // значения по умолчанию для всего списка объектов
            for(let prop in def){
                if (!(prop in tag)&&(prop!=='id'))
                    tag[prop]=def[prop];
            }
            code+=ut.tag(tag);
        };

        if ($.isArray(list)){
            list.map(item=>{
                let tag = {},name='';
                if (typeof item === 'string'){
                    name = uniq(item);
                    tag.id = t.id(name);
                }else{
                    name = uniq(Object.keys(item)[0]);
                    tag = item[name];                
                    if (!('id' in tag))
                        tag.id = t.id(name);
                }
                reg(name,tag);
            });
        }else{
            for(let prop in list){
                let name = uniq(prop); 
                let tag = list[name];
                if (!('id' in tag))
                    tag.id = t.id(name);
                reg(name,tag);
            }
        }
        t.$plugin.append(code);
        
        names.map(name=>{
            t.components['$'+name] = t.$plugin.find(t.id('#'+name));
            t.childs.push(t.components['$'+name]);
        });
        
        t.$childs = t.$plugin.children();
    }
}
