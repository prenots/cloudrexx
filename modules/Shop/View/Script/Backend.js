cx.bind("delete", function (deleteIds) {
    var scope = 'order';
    if (confirm(
        cx.variables.get('TXT_CONFIRM_DELETE_ORDER', scope)+'\n'+ cx.variables.get('TXT_ACTION_IS_IRREVERSIBLE', scope)
    )) {
        var stockUpdate = false;
        if (confirm(
            cx.variables.get('TXT_SHOP_CONFIRM_RESET_STOCK', scope)
        )) {
            stockUpdate = true;
        }
        window.location.replace(
            "?csrf=" + cx.variables.get('CSRF_PARAM', scope) + "&deleteids="
            + encodeURI(deleteIds) + (stockUpdate ? '&stock_update=1' : '')
        );
    }
}, 'order');