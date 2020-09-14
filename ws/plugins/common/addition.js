function nil(o){
    return ((o===undefined)||(o===null));     
}

if (!(String.replaceAll))
{
    String.prototype.replaceAll=function(find, replace_to)
    {
        if (typeof find==='string')
            return this.replace(new RegExp(find, "g"), replace_to);
        else{
            var i,out = this;
            for(i=0;i<find.length;i++)
                out = out.replaceAll(find[i],(typeof replace_to==='string'?replace_to:replace_to[i]));
            return out;    
        }
    };
}

if (!(Array.unique)){
    
    Array.prototype.unique=function(){
        var A = this,n = A.length, k = 0, B = [],i,j;
        for(i = 0; i < n; i++){
            j = 0;
            while (j < k && B[j] !== A[i]) 
                j++;
            if (j == k) 
                B[k++] = A[i];
        }
        return B;
    };
}

if (!Array.prototype.find) {
  Object.defineProperty(Array.prototype, 'find', {
    value: function(predicate) {
     // 1. Let O be ? ToObject(this value).
      if (this == null) {
        throw new TypeError('"this" is null or not defined');
      }

      var o = Object(this);

      // 2. Let len be ? ToLength(? Get(O, "length")).
      var len = o.length >>> 0;

      // 3. If IsCallable(predicate) is false, throw a TypeError exception.
      if (typeof predicate !== 'function') {
        throw new TypeError('predicate must be a function');
      }

      // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
      var thisArg = arguments[1];

      // 5. Let k be 0.
      var k = 0;

      // 6. Repeat, while k < len
      while (k < len) {
        // a. Let Pk be ! ToString(k).
        // b. Let kValue be ? Get(O, Pk).
        // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
        // d. If testResult is true, return kValue.
        var kValue = o[k];
        if (predicate.call(thisArg, kValue, k, o)) {
          return kValue;
        }
        // e. Increase k by 1.
        k++;
      }

      // 7. Return undefined.
      return undefined;
    },
    configurable: true,
    writable: true
  });
}
/** если элементы массива объекты, то поиск идет 
 * по значению в поле в объекта
 * по умолчанию возвращает индекс (result = 'index') (-1 - нет объекта)
 * result = 'object' - вернет объект (undefined - нет объекта)
 * result = 'bool'  - вернет признак существует ли нет
 * result = 'all' - {i:int,o:object,b:bool}
 * Ex:
 * var arr = [{id:1,name:"Mike"},{id:2,name:"Soma"},{id:3,name:"Tomb"}];
 * var idx = arr.findByProp('id',2);
 * >>>> idx = 1;
 * 
 * var idx = arr.findByProp({id:2});
 * >>>> idx = 1;
 */ 
if (!(Array.findByProp)){
    
    Array.prototype.findByProp=function(prop,search,result,eq){
        var A = this,i, res = {i:-1,o:undefined,b:false},
        tp = typeof prop,ok=false,f,fst;
        if ((tp === 'object')||(tp ==='function')){
            eq      = result;
            result  = search;
        }    
        
        if (result === undefined) result = 'index';
        if (eq === undefined) eq = '==';
        if ((eq!=='==')&&(eq!=='===')) eq = '==';
        
        for(i = 0; i < A.length; i++){
            if (typeof A[i]==='object'){
                if (tp==='string'){
                    ok = ((prop in A[i])&&(((eq==='==')&&(A[i][prop]==search))||((eq==='===')&& (A[i][prop]===search)) ));
                }else if (tp==='object'){
                    
                    ok=false;
                    fst = true;
                    for(f in prop){
                        ok = (fst||ok)&&((f in A[i])&&(((eq==='==')&&(A[i][f]==prop[f]))||((eq==='===')&& (A[i][f]===prop[f])) ));    
                        fst=false;
                    }

                }else if (tp==='function')
                    ok = (prop(A[i],i,A)===true);
                
            
                if (ok){
                    res = {i:i,o:A[i],b:true,a:A};
                    break;
                }    
            }    
        }
        
        if (result === 'index')
            return res.i;
        if (result === 'object')
            return res.o;
        if (result === 'bool')
            return res.b;
        if (result === 'all')
            return res;

    };
}
/**
 * 
 */ 
if (!Array.eq){
    Array.prototype.eq=function(b,soft = true,ordering = true){
        let a = this;
        if ((!Array.isArray(b)) || (a.length!==b.length)) return false;
        if (a.length === 0)
            return true;
        for(let i = 0;i<a.length;i++){
            if (ordering){
                if ((soft && (a[i]!=b[i])) || (!soft && (a[i]!==b[i])))
                    return false;
            }else{
                let have = false;
                for(let j = 0;j<b.length;j++){
                    if ((soft && (a[i]==b[j])) || (!soft && (a[i]===b[j]))){
                        have = true;
                        break;
                    }    
                }
                if (!have) 
                    return false;
            }
        }
        return true;
    };
}
function $D(o,name){
    try{
        if (name===undefined)
            name = 'data';
        return $.data((o instanceof jQuery?o[0]:o),name);        
    }catch(e){
        console.error('error in $D() ',e);
        return false;        
    }
}

(function ($){
    $.fn.finc = function(css){
        return this.find('.'+css);
    };
    
    $.fn.fini = function(id){
        return this.find('#'+id);
    };
    
    $.fn.mean = function(val){
        if (this.length===0) return this;
        var t = this.length===1?this:this.eq(0);
        
        if (t[0].tagName ==='INPUT'){
            
            if (val === undefined)
                return t.val();
            else
                t.val(val);
        }else{
            if (val === undefined)
                return t.text();
            else
                t.text(val);
        }        
        return this;
    };

    $.fn.float = function(def){
        var s = this.mean();
        var f= /\d+[\.\,]?\d*/.exec(s.replaceAll(',','.'));
        if (f!==null)
            return parseFloat(f[0]);
        else
            return (def!==undefined?def:0);
        
    };
    $.fn.int = function(def){
        var s = this.mean();
         var out = parseInt(s);
         if (isNaN(out))
             return def!==undefined?def:0;
         return out;
    };
    
    $.fn.setCursorPosition = function(pos) {
        this.each(function(index, elem) {
        if (elem.setSelectionRange) {
            elem.setSelectionRange(pos, pos);
        } else if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
  });
  return this;
};
    
    
})(jQuery);

/** для старого хрома, в котором нет key в событиях originalEvent */
function fixOriginalEvent(oe){
    if (!('key' in oe)){
        if (oe.keyCode === 110){
            oe.key = '.';
        }else if (oe.keyCode>=96 && oe.keyCode<=105){
            oe.key = String(oe.keyCode-96);
        }else{
            const chrCode = oe.keyCode - 48 * Math.floor(oe.keyCode / 48);
            oe.key = String.fromCharCode((96 <= oe.keyCode) ? chrCode: oe.keyCode);    
        }    
    }
    return oe;
}