/* eslint-disable no-plusplus */
/* eslint-disable class-methods-use-this */
/* eslint-disable camelcase */
/* eslint-disable no-underscore-dangle */
/* global JX,session,ut */

class CWs {
    constructor() {
        this._noAsync = [];
        this._aligns = [];
        this._lockAlign = 0;
        this._only = [];
        this._locking = false;
        this._repeat = false;
        this._ready = [];
    }


    ajax(o) {
        const p = $.extend(false, {
            id: -1,
            value: '',
            url: this.url,
            timeout: 3600000,
            done(...a) { this.done(...a); },
            error(...a) { this.error(...a); },
            method: 'POST',
            before(...a) { this.before_ajax(...a); },
            noAsync: false, /** запрещает выполнять запрос если не обработан запрос с таким же id */
            repeat: 1,
        }, o);


        let { repeat } = p;


        const dat = {
            AJAX: 1, ID: p.id, VALUE: p.value, SHARE: this.share,
        };

        // eslint-disable-next-line consistent-return
        const noAsync = (id, bool) => {
            if (bool === undefined) { return (this._noAsync.indexOf(id) >= 0); }

            if (bool) { this._noAsync.push(id); } else {
                const idx = this._noAsync.indexOf(id);
                if (idx >= 0) { this._noAsync.splice(idx, 1); }
            }
        };

        if (p.noAsync) {
            if (noAsync(p.id)) {
                if (p.error) {
                    p.error('noAsync', `id=${p.id} in process`);
                }
                return;
            }noAsync(p.id, true);
        }

        const _recv = () => {
            const request = $.ajax({
                url: p.url,
                method: p.method,
                timeout: p.timeout,
                data: dat,
                recvId: p.id,
            });

            if (!('context' in p)) {
                p.context = request;
            }

            request.done((_data) => {
                noAsync(this.recvId, false);

                const data = this.ajax_response(_data);

                if (data !== null) {
                /* include session module if session_mod is require */
                    if ((typeof (session) !== 'undefined')
                        && (this.share.session !== undefined)
                        && (session._fromAjax)) {
                        session._fromAjax(this.share.session, data[0].ID);
                    }
                    /*--------------------------------------------------*/
                    p.done(data[0].DATA, data[0].ID, p.context);
                } else {
                    p.error(null, `parse data [${_data}]`, p.context);
                }
            });

            request.fail((jqXHR, textStatus) => {
                // eslint-disable-next-line no-plusplus
                repeat--;
                if (repeat < 0) {
                    noAsync(this.recvId, false);
                    p.error(jqXHR, textStatus, p.context);
                } else {
                    console.log(`repeat[${repeat}]${p.id}`);
                    _recv();
                }
            });
        };

        _recv();
    }

    navigate(o) {
        const p = $.extend({
            module: '',
            value: '',
            url: this.url,
            before: undefined,
        }, o);

        const dat = { MODULE: p.module, VALUE: p.value, SHARE: this.share };
        let wrapper = '<form method=\'post\' id=\'_mod_load_frm_\' action=\'';
        wrapper += `${this.url}'>`;
        wrapper += `<input type='hidden' name='module' value='${JSON.stringify(dat)}' />`;
        wrapper += '</form>';
        $('body').append(wrapper);

        if ((p.before === undefined) || (p.before(p))) {
            $('#_mod_load_frm_').submit();
        }
    }

    ajax_response(data) {
        let res = null;
        try {
            res = $.parseJSON(data);
            this.share = res[1].SHARE;

            return res;
        } catch (err) {
            return null;
        }
    }

    // eslint-disable-next-line class-methods-use-this
    before_ajax() {

    }

    get(o) {
        return new Promise((ok, err) => {
            const p = $.extend(false, {
                id: -1,
                value: '',
                url: this.url,
                timeout: 3600000,
                method: 'POST',
                noAsync: false, /** запрещает выполнять запрос если не обработан запрос с таким же id */
            }, o);


            const dat = {
                AJAX: 1, ID: p.id, VALUE: p.value, SHARE: this.share,
            };

            // eslint-disable-next-line consistent-return
            const noAsync = (id, bool) => {
                if (bool === undefined) {
                    return (this._noAsync.indexOf(id) >= 0);
                }

                if (bool) {
                    this._noAsync.push(id);
                } else {
                    const idx = this._noAsync.indexOf(id);

                    if (idx >= 0) this._noAsync.splice(idx, 1);
                }
            };

            if (p.noAsync) {
                if (noAsync(p.id)) {
                    err({ res: -3, msg: 'noAsync `id=${p.id} in process`', data: null });
                    return;
                }
                noAsync(p.id, true);
            }

            const request = $.ajax({
                url: p.url,
                method: p.method,
                timeout: p.timeout,
                data: dat,
                recvId: p.id,
            });

            if (!('context' in p)) { p.context = request; }

            request.done((_data) => {
                noAsync(this.recvId, false);

                const data = this.ajax_response(_data);

                if (data !== null) {
                    /* include session module if session_mod is require */
                    if ((typeof (session) !== 'undefined') && (this.share.session !== undefined) && (session._fromAjax)) {
                        session._fromAjax(this.share.session, data[0].ID);
                    }
                    /*--------------------------------------------------*/
                    const rec = data[0].DATA;
                    // p.done(rec, rec.ID, p.context);
                    if (('res' in rec)) {
                        if (rec.res == 1) {
                            ok(('data' in rec) ? rec.data : null);
                        } else {
                            err({ res: rec.res, msg: ('msg' in rec ? rec.msg : ''), data: rec });
                        }
                    } else {
                        ok(rec);
                    }

                    /*--------------------------------------------------*/
                } else {
                    err({ res: -2, msg: `parse data [${_data} ]`, data: null });
                }
            });

            request.fail((jqXHR, textStatus) => {
                noAsync(this.recvId, false);
                // eslint-disable-next-line prefer-promise-reject-errors
                err({ res: -1, msg: `system error: [${textStatus} ]`, data: jqXHR });
            });
        });
    }

    done(data, id, context) {

    }

    error(obj, msg, context) {
        console.error(`Ws.error:${msg}`);
    }


    align(o) {
        if (o === undefined || o === 'repeat') {
            if (this._lockAlign !== 0) return;

            if (this._locking) {
                this._repeat = true;
                return;
            }
            this._locking = true;

            try {
                const recall = []; const remove = []; let i; let
                    item;

                this._align();

                for (i = 0; i < this._aligns.length; i++) {
                    item = this._aligns[i];
                    try {
                        if (item.func) item.func();
                        if (item.recall) recall.push(item);
                    } catch (e) {
                        console.error(`align: ${this._aligns[i].id} remove handler`);
                        remove.push(item);
                    }
                }

                for (i = 0; i < remove.length; i++) { this.removeAlign(remove[i].id); }


                for (i = 0; i < recall.length; i++) {
                    item = recall[i];
                    if (item.func) item.func();
                }

                JX.lh('update');
            } catch (e) {
                console.warn(e);
            }
            this._locking = false;

            if (this._repeat) {
                this._repeat = false;
                this.align('repeat');
            }
        } else {
            if (o === 'begin') {
                this._lockAlign++;
                return;
            }
            if (o === 'end') {
                this._lockAlign--;
                if (this._lockAlign < 0) {
                    console.error('Ws._lockAlign < 0 ...!');
                }
                return;
            }

            let a = false; const
                id = ut.id('alfn');
            if (typeof o === 'function') {
                a = {
                    id,
                    func: o,
                    recall: false,
                };
            } else if ((o.func) && (typeof o.func === 'function')) {
                a = $.extend(false, {
                    id,
                    func: undefined,
                    recall: false,
                }, o);
            }

            if (a) {
                this._aligns.push(a);
            }

            return a;
        }
    }

    removeAlign(id) {
        const a = this._aligns; let
            i;
        for (i = 0; i < a.length; i++) {
            if (a[i].id === id) {
                a.splice(i, 1);
                return;
            }
        }
    }

    only(name, f) {
        if (f === undefined) {
            try {
                this._only[name]();
            } catch (e) {
                console.error(name, e);
            }
        } else if (!(name in this._only)) {
            this._only[name] = f;
        } else {
            console.error(`name [${name}] is already exists!`);
        }
    }

    ready(...arg) {
        if (arg.length === 0) {
            for (let i = 0; i < this._ready.length; i++) {
                const f = this._ready[i].func;
                try {
                    if (f) f();
                } catch (e) {
                    console.error(`ready: ${this._ready[i].id}`);
                }
            }
        } else {
            const o = arg[0];
            let a;
            const
                id = ut.id('rdfn');
            if (o.func) {
                a = $.extend({
                    id,
                    func: undefined,
                }, o);
            } else { a = { id, func: o }; }
            this._ready.push(a);
        }
    }
}
const Ws = new CWs();
