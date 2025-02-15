jQuery(document).ready(function($) {
    $('#comment').wrap('<div class="wpct_toolbar"></div>');
    quicktags({
        id: "comment",
        buttons: "em,strong"
    });
    QTags.addButton('eg_underline', 'u', '<u>', '</u>', 'u')
});
