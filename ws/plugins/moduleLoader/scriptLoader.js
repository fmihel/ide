/**
 * динамическая загрузка скрипта
 */
class ScriptLoader {
    constructor() {
        this.list = [];
        
    }

    /** возвращает признак, что загружен скрипт
     * @param {string} addr of scripts
     * @returns {bool}
    */
    exist(url) {
        return this.list.indexOf(url) >= 0;
    }

    /** @returns {number} count of loading script */
    count() {
        return this.list.length;
    }

    /** return name loading script by index
     * @param {int} index
     * @returns {string}
    */
    get(i) {
        return this.list[i];
    }
   
    /** динамическая загрузка js скрипта
     * @param {string|object} string = "addr" object = {url:"addr"}
     * @param {string} - имя глобальной переменной в загружаемом скрипте, которая будет возвращена в случае удачи
     * @returns {Promise}
    */
    load(url, varName = false) {
        const p = {
            url: false,
            returnVar: varName,
            wait: 2000,
            ...(typeof url === 'string' ? { url } : url),
        };

        return new Promise((ok, err) => {
            if (!p.url) {
                throw Error('use load("addr") or load({url:"add"}');
            }

            if (!this.exist(p.url)) {
                const script = document.createElement('script');
                script.onload = () => {
                    this.list.push(p.url);
                    if (p.returnVar !== false) {
                        const hWait = setInterval(() => {
                            if (p.returnVar in window) {
                                clearInterval(hWait);
                                ok(window[p.returnVar]);
                            }
                            p.wait -= 100;
                            if (p.wait <= 0) {
                                clearInterval(hWait);
                                // eslint-disable-next-line prefer-promise-reject-errors
                                err(`can\`t load ${p.returnVar}`);
                            }
                        }, 100);
                    } else {
                        ok(p.url);
                    }
                };
                script.onerror = () => {
                    err(p.url);
                };

                script.src = p.url;
                document.head.append(script);
            } else {
                ok(p.returnVar ? window[p.returnVar] : p.url);
            }
        });
    }
    
    /** перезаписывает имя загруженного скрипта (для нужд moduleLoader)*/
    _replaceUrl(from,to){
        for(let i = 0;i<this.list.length;i++){
            if (this.list[i] === from){
                this.list[i] = to;
                return true;
            }
        }
        return false;
    }
}

const scriptLoader =  new ScriptLoader();
