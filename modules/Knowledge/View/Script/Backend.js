function showStatusMessage(response)
{
    var divId = (response.status === 'success') ? 'okbox' : 'alertbox';

    $J('#messageBox')
        .append('<div id="' + divId + '">' + response.message + '</div>')
        .find(':last-child')
        .fadeIn('slow').animate({opacity: 1.0}, 3000)
        .fadeOut(5000, function(){$J(this).remove()});
}