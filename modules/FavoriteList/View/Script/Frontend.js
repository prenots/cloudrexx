cx.ready(function () {
    cx.favoriteListUpdateBlock = function (data) {
        cx.jQuery('#favoriteListBlock').empty();
        cx.jQuery(data).appendTo('#favoriteListBlock');
    };

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

    cx.favoriteListEditFavorite = function (id, attribute, value, update) {
        return cx.ajax(
            'FavoriteList',
            'editFavorite',
            {
                data: {
                    id: id,
                    attribute: attribute,
                    value: value,
                    themeId: cx.variables.get('themeId'),
                    lang: cx.variables.get('language')
                },
                beforeSend: function () {},
                success: function (data) {
                    update = typeof update !== 'undefined' ? update : true;
                    if (update) {
                        cx.favoriteListUpdateBlock(data.data);
                    }
                }
            }
        );
    };

    cx.favoriteListSave = function () {
        var promises = [];
        cx.jQuery('#favoriteListBlock .favoriteListBlockListEntity').each(function () {
            var id = cx.jQuery(this).data('id');
            var value = cx.jQuery(this).find('[name="favoriteListBlockListEntityMessage"]').val();
            promises.push(cx.favoriteListEditFavorite(id, 'message', value, false));
        });
        return promises;
    };

    cx.jQuery('#favoriteListBlockActions a').click(function (event) {
        event.preventDefault();
        var $this = cx.jQuery(this);
        var promises = cx.favoriteListSave();
        promises.forEach(function (promise, index) {
            promise.done(function () {
                promises.splice(index, 1);
                if (promises.length == 0) {
                    window.location.href = $this.attr('href');
                }
            });
        });
    });
});
