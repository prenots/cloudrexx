cx.ready(function () {
    cx.favoriteListLoadBlock = function () {
        cx.ajax(
            'FavoriteList',
            'getCatalog',
            {
                data: {
                    themeId: cx.variables.get('themeId'),
                    lang: cx.variables.get('language')
                },
                beforeSend: function () {},
                success: function (data) {
                    cx.favoriteListUpdateBlock(data.data);
                }
            }
        );
    };
    cx.favoriteListLoadBlock();

    cx.favoriteListAddFavorite = function (element) {
        cx.ajax(
            'FavoriteList',
            'addFavorite',
            {
                data: {
                    themeId: cx.variables.get('themeId'),
                    lang: cx.variables.get('language'),
                    title: cx.jQuery(element).data('title'),
                    link: cx.jQuery(element).data('link'),
                    description: cx.jQuery(element).data('description'),
                    message: cx.jQuery(element).data('message'),
                    price: cx.jQuery(element).data('price'),
                    image1: cx.jQuery(element).data('image1'),
                    image2: cx.jQuery(element).data('image2'),
                    image3: cx.jQuery(element).data('image3')
                },
                beforeSend: function () {},
                success: function (data) {
                    cx.favoriteListUpdateBlock(data.data);
                }
            }
        );
    };

    cx.favoriteListRemoveFavorite = function (id) {
        cx.ajax(
            'FavoriteList',
            'removeFavorite',
            {
                data: {
                    id: id,
                    themeId: cx.variables.get('themeId'),
                    lang: cx.variables.get('language')
                },
                beforeSend: function () {},
                success: function (data) {
                    cx.favoriteListUpdateBlock(data.data);
                }
            }
        );
    };

    cx.favoriteListEditFavoriteMessage = function (id, element, updateBlock) {
        cx.ajax(
            'FavoriteList',
            'editFavoriteMessage',
            {
                data: {
                    id: id,
                    message: cx.jQuery(element).closest('.favoriteListBlockListEntity').find('[name="favoriteListBlockListEntityMessage"]').val(),
                    themeId: cx.variables.get('themeId'),
                    lang: cx.variables.get('language')
                },
                beforeSend: function () {},
                success: function (data) {
                    if (updateBlock) {
                        cx.favoriteListUpdateBlock(data.data);
                    }
                }
            }
        );
    };

    cx.favoriteListUpdateBlock = function (data) {
        cx.jQuery('#favoriteListBlock').empty();
        cx.jQuery(data).appendTo('#favoriteListBlock');
    };

    cx.jQuery('#favoriteListBlockActions a').click(function (event) {
        event.stopPropagation();
        cx.jQuery('#favoriteListBlock .favoriteListBlockListEntity').each(function () {
            var onclick = cx.jQuery(this).find('[onclick*="favoriteListEditFavoriteMessage"]').attr('onclick');
            if (onclick.indexOf('true') >= 0) {
                onclick = onclick.replace('true', 'false');
                var editButton = cx.jQuery(this).find('[onclick*="favoriteListEditFavoriteMessage"]');
                event.stopImmediatePropagation();
                editButton.attr('onclick', onclick);
                editButton.trigger('click');
            }
        });
        if (!cx.jQuery(event.target).is(this)) {
            cx.jQuery(this).trigger('click');
        }
    });
});
