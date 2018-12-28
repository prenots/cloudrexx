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
    document.getElementById('headerLeft').parentNode.parentElement.parentElement.style.display = disp;
}

function toggle_footer()
{
    var disp = (document.getElementsByName('footerOn')[0].checked ? "block" : "none");
    document.getElementById('footerLeft').parentNode.parentElement.parentElement.style.display = disp;
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


    document.getElementById('headerRight').value = document.getElementById('form-0-headerRight').value;
    document.getElementById('footerRight').value = document.getElementById('form-0-footerRight').value;

    document.getElementById('headerRight').onchange = function () {
        document.getElementById('form-0-headerRight').value = document.getElementById('headerRight').value
    };
    document.getElementById('footerRight').onchange = function () {
        document.getElementById('form-0-footerRight').value = document.getElementById('footerRight').value
    };
});

function updateDefault(defaultEntity) {
    cx.jQuery(".adminlist tbody tr").has('.id').each(function(index, el) {
        var entity = cx.jQuery(el);
        if (parseInt(cx.jQuery(defaultEntity).closest('tr').find('.id').text()) != parseInt(entity.find('.id').text())) {
            entity.find('.default input').removeProp('checked');
            entity.find('.default input').val(0);
        } else {
            entity.find('.default input').val(1);
        }
    });
}