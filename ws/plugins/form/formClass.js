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
        
        this.init($plugin);
        t.$plugin.mform(t.param);
    }

    init(){
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
}
