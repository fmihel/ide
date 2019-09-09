/**
 * ф-ция поиск DOM элемента (работает примерно в 2 раза быстрее чем $('xx')[0])
 * @param {string} selector - строка содержащая id|class|tagName 
 * @param {string} parentDOM  - элемент от, которого идет поиск (если не указан, то от body)
 * @return DOMElement | null
 */ 
 
function DOM(selector, parentDOM = false) {
    const own = parentDOM || document;
    try {
        if (selector[0] === '#') {
            return own.getElementById(selector.substring(1));
        } if (selector[0] === '.') {
            return own.getElementsByClassName(selector.substring(1))[0];
        } return own.getElementsByTagName(selector)[0];
    } catch (e) {
        return null;
    }
}
/**
 * Аналогично DOM но возвращает список (массив) элементов
 * @param {string} selector - строка содержащая id|class|tagName 
 * @param {string} parentDOM  - элемент от, которого идет поиск (если не указан, то от body)
 * @return array
 */ 
function DOMS(selector, parentDOM = false) {
    const own = parentDOM || document;
    try {
        return own.querySelectorAll(selector);
    } catch (e) {
        return [];
    }
}
