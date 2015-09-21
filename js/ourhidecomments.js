$(function () {
    /* Do some javascripts on page load! */
});


window.updateShown = function () {
    var el = $(this);
    if (!el.hasClass('HiddenComment'))
        el = el.closest('.HiddenComment');
    el.removeClass('HiddenCommentAdmin');
    // @todo Change from an ID to a unique class
    var opt = $('a.Hide' + $(this).attr('id'));
    opt.text("Hide Comment");
    opt.prop("disabled", false);
};

window.updateHidden = function () {
    var el = $(this);
    if (!el.hasClass('HiddenComment'))
        el = el.closest('.HiddenComment');
    el.addClass('HiddenCommentAdmin');
    // @todo Change from an ID to a unique class
    var opt = $('a.Hide' + $(this).attr('id'));
    opt.text("Unhide Comment");
    opt.prop("disabled", false);
};