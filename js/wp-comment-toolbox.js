jQuery(document).ready(function($) {
    $('#comment').wrap('<div class="wpct_toolbar"></div>');

    // Initialize Quicktags for comment field
    quicktags({
        id: "comment",
        buttons: "em,strong"
    });

    // Initialize Quicktags for bbPress forum content
    quicktags({
        id: "bbp_reply_content",
        buttons: "link,em,strong"
    });

    quicktags({
        id: "bbp_topic_content",
        buttons: "link,em,strong"
    });

    // Initialize Quicktags for post content (if applicable)
    quicktags({
        id: "posttext",
        buttons: "link,em,strong"
    });

    QTags.addButton('eg_underline', 'u', '<u>', '</u>', 'u')
});
