var scope = 'shopDelete';
cx.bind("delete", function (deleteIds) {
    if (confirm(
        cx.variables.get('TXT_CONFIRM_DELETE', scope)+'\n'+ cx.variables.get('TXT_ACTION_IS_IRREVERSIBLE', scope)
    )) {
        window.location.replace(
            "?deleteids=" + encodeURI(deleteIds) + "&csrf=" + cx.variables.get('CSRF_PARAM', scope) + "&vg_increment_number=0"
        );
    }
}, 'shopDelete');


function toggle_header()
{
    var disp = (document.getElementsByName('headerOn')[0].checked ? "block" : "none");
    document.getElementsByName('headerLeft')[0].parentNode.parentElement.style.display = disp;
    document.getElementsByName('headerRight')[0].parentNode.parentElement.style.display = disp;
}

function toggle_footer()
{
    var disp = (document.getElementsByName('footerOn')[0].checked ? "block" : "none");
    document.getElementsByName('footerLeft')[0].parentNode.parentElement.style.display = disp;
    document.getElementsByName('footerRight')[0].parentNode.parentElement.style.display = disp;
}

function toggle_categories(status)
{
    var check = jQuery('#category-all:checked').length > 0;
    if(status && !check){
        return;
    }
    jQuery('.category input').prop('checked', check);
}

jQuery(document).ready(function($){
    $('.category').change(function(){
        var check = true;
        $('.category').each(function(){
            if(!$(this).find('input').is(":checked")){
                check = false;
            }
            $('#category-all').prop('checked', check);
        });
    });
    $('#category-all').change(function(){
        toggle_categories(false);
    });
    $('#form-0-headerOn').change(function(){
        toggle_header();
    });
    $('#form-0-footerOn').change(function(){
        toggle_footer();
    });
    toggle_header();
    toggle_footer();
    toggle_categories(true);

});
