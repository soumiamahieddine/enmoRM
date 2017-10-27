(function($)
{
    $.dataList = function(element, options)
    {
        var defaults    = {
            items: '*[data-list-item]',
            pagination: {
                visible: 5,
                prev: '*[data-list-pagination-prev]',
                next: '*[data-list-pagination-next]',
                info: '*[data-list-pagination-info]',
                save: true,
            },
            filter: {
                input: '*[data-list-filter-input]',
                fields: '*[data-list-filter-field]',
                threshold: 2,
                highlight: 'label label-info',
                save: true,
            }
        };

        var element     = element;
        var elements    = {};

        var hasLocalStorage = (typeof window.localStorage != "undefined");

        var plugin      = this;
        plugin.settings = {};

        /* Constructor */
        plugin.init = function()
        {
            // settings
            plugin.settings = $.extend({}, defaults, options);

            $(this).ready(function() {
                // elements
                elements = {
                    container: $(element),
                    items: $(plugin.settings.items),
                    pagination: {
                        prev: $(plugin.settings.pagination.prev),
                        next: $(plugin.settings.pagination.next),
                        info: $(plugin.settings.pagination.info),
                    },
                    filter: {
                        input: $(plugin.settings.filter.input),
                    }
                };

                // init pagination
                _setCurrentPage(1);
                if (_hasPaginationDataSaved())
                    _reloadPagination();
                _paginate();

                // event listener pagination
                elements.pagination.prev.click(function(event) {
                    _prevPage();
                });
                elements.pagination.next.click(function(event) {
                    _nextPage();
                });

                // init filter
                if (_hasFilterDataSaved())
                    _reloadFilter();
                _filter();

                // event listener filter
                elements.filter.input.keyup(function(event) {
                    _filter();
                });
            });
        }
        plugin.init();

        // pagination
        var _paginate = function()
        {
            if (_getTotalPages() == 1 || _getCurrentPage() > _getTotalPages() || !plugin.settings.pagination.visible) {
                _resetPagination();
            } else {
                var firstItemIndex = _getFirstItemIndex();
                var lastItemIndex = _getLastItemIndex();
                var paginableItems = _getPaginableItems();

                _hidePaginableItem(paginableItems);

                var index = 0;
                paginableItems.each(function() {
                    if (index >= firstItemIndex && index < lastItemIndex)
                        _showPaginableItem($(this));
                    index++;
                });
            }

            if (!_canPrev())
                _disablePrev();
            else
                _enablePrev();

            if (!_canNext())
                _disableNext();
            else
                _enableNext();

            _showPaginationInfo();
            _savePagination();
        }

        var _resetPagination = function()
        {
            _setCurrentPage(1);
            _showPaginableItem(_getPaginableItems());
        }

        var _getPaginableItems = function()
        {
            return $(plugin.settings.items + ":not(*[data-list-filter-hide])");
        }

        var _setCurrentPage = function(currentPage)
        {
            elements.container.attr('data-list-pagination-current', parseInt(currentPage));
        }
        var _getCurrentPage = function()
        {
            return parseInt(elements.container.attr('data-list-pagination-current'));
        }

        var _getTotalPages = function()
        {
            if (!plugin.settings.pagination.visible)
                return 1;
            else
                return Math.ceil(_getPaginableItems().length / plugin.settings.pagination.visible);
        }

        var _getFirstItemIndex = function()
        {
            return (_getCurrentPage() - 1) * plugin.settings.pagination.visible;
        }
        var _getLastItemIndex = function()
        {
            return _getCurrentPage() * plugin.settings.pagination.visible;
        }

        var _showPaginableItem = function($item)
        {
            $item.show().removeAttr('data-list-pagination-hide');
        }
        var _hidePaginableItem = function($item)
        {
            $item.hide().attr('data-list-pagination-hide', true);
        }

        var _prevPage = function()
        {
            if (!_canPrev())
                return;

            _setCurrentPage(_getCurrentPage() - 1);
            _paginate();
        }
        var _nextPage = function()
        {
            if (!_canNext()) 
                return;

            _setCurrentPage(_getCurrentPage() + 1);
            _paginate();
        }

        plugin.previousPage = function()
        {
            _prevPage();
        }
        plugin.nextPage = function()
        {
            _nextPage();
        }

        var _canPrev = function()
        {
            if (_getCurrentPage() <= 1)
                return false;
            else
                return true;
        }
        var _canNext = function()
        {
            if (_getCurrentPage() >= _getTotalPages())
                return false
            else
                return true;
        }

        var _enablePrev = function()
        {
            elements.pagination.prev.removeClass("disabled");
        }
        var _disablePrev = function()
        {
            elements.pagination.prev.addClass("disabled");
        }

        var _enableNext = function()
        {
            elements.pagination.next.removeClass("disabled");
        }
        var _disableNext = function()
        {
            elements.pagination.next.addClass("disabled");
        }

        var _showPaginationInfo = function()
        {
            elements.pagination.info.text(_getCurrentPage() + " / " + _getTotalPages());
        }

        var _savePagination = function()
        {
            if (hasLocalStorage && plugin.settings.pagination.save == true) {
                localStorage.setItem("dataList-" + window.location.pathname + "-paginationCurrentPage", _getCurrentPage());
            } else if (hasLocalStorage) {
                localStorage.removeItem("dataList-" + window.location.pathname + "-paginationCurrentPage");
            }
        }

        var _hasPaginationDataSaved = function()
        {
            if (hasLocalStorage && plugin.settings.pagination.save == true) {
                return (localStorage.getItem("dataList-" + window.location.pathname + "-paginationCurrentPage") != null);
            } else {
                return false;
            }
        }

        var _reloadPagination = function()
        {
            if (hasLocalStorage && plugin.settings.pagination.save == true) {
                _setCurrentPage(localStorage.getItem("dataList-" + window.location.pathname + "-paginationCurrentPage"));
            }
        }

        // filter
        var _filter = function()
        {
            if (!(regexpFilter = _getRegexpFilter())) {
                _resetFilter();
            } else {
                _hideFilterableItem(elements.items);

                elements.items.each(function() {
                    var listItem = $(this).closest(plugin.settings.items);

                    var filterFields = $(this).find(plugin.settings.filter.fields);
                    var text = '';
                    filterFields.each(function(){
                        text += " " + $(this).text() + " ";
                    });
                    var text = text.replace("  ", " ");

                    if (text.match(regexpFilter))
                        _showFilterableItem($(listItem));

                    if (plugin.settings.filter.highlight)
                        _highlightFilter();

                });
            }

            _paginate();
            _saveFilter();
        }

        var _resetFilter = function()
        {
            _showFilterableItem(elements.items);

            if (plugin.settings.filter.highlight)
                elements.items.find(plugin.settings.filter.fields).each(function() {
                    $(this).html($(this).text());
                });

            _paginate();
        }

        var _showFilterableItem = function($item)
        {
            $item.show().removeAttr('data-list-filter-hide');
        }
        var _hideFilterableItem = function($item)
        {
            $item.hide().attr('data-list-filter-hide', true);
        }

        var _highlightFilter = function()
        {
            $(plugin.settings.items + ":not(*[data-list-filter-hide]) " + plugin.settings.filter.fields).each(function(){
                $(this).html($(this).text().replace(_getRegexpHighlightFilter(), '<span class="' + plugin.settings.filter.highlight + '" style="font-size: inherit;">$1</span>'));
            });
        }

        var _getRegexpFilter = function()
        {
            var filterInputVals = _getFilterValues();
            for (index in filterInputVals)
                filterInputVals[index] = "(?=.*(" + filterInputVals[index] + "))";
            var filterInputVals = filterInputVals.join("");

            if (filterInputVals.length <= 0)
                return false;
            else
                return new RegExp("(" + filterInputVals + ")" , 'gi');
        }

        var _getRegexpHighlightFilter = function()
        {
            var filterInputVals = _getFilterValues().join("|");

            if (filterInputVals.length <= 0)
                return false;
            else
                return new RegExp("(" + filterInputVals + ")" , 'gi');
        }

        var _getFilterValues = function()
        {
            var filterInputVals = elements.filter.input.val().split(" ");

            for (var length=filterInputVals.length - 1, index = filterInputVals.length - 1; index >= 0; index--)
                if (filterInputVals[index].length < plugin.settings.filter.threshold)
                    filterInputVals.splice(index, 1);

            return filterInputVals;
        }

        var _saveFilter = function()
        {
            if (hasLocalStorage && plugin.settings.filter.save == true) {
                localStorage.setItem("dataList-" + window.location.pathname + "-filterInputVals", elements.filter.input.val());
            } else if (hasLocalStorage) {
                localStorage.removeItem("dataList-" + window.location.pathname + "-filterInputVals");
            }
        }

        var _hasFilterDataSaved = function()
        {
            if (hasLocalStorage && plugin.settings.filter.save == true) {
                return (localStorage.getItem("dataList-" + window.location.pathname + "-filterInputVals") != null);
            } else {
                return false;
            }
        }

        var _reloadFilter = function()
        {
            if (hasLocalStorage && plugin.settings.filter.save == true) {
                elements.filter.input.val(localStorage.getItem("dataList-" + window.location.pathname + "-filterInputVals"));
            }
        }
    }

    // Plugin instantiation
    $.fn.dataList = function(options) {
        return this.each(function() {
            if (undefined == $(this).data('dataList')) {
                var plugin = new $.dataList(this, options);
                $(this).data('dataList', plugin);
            }
        });
    }
})(jQuery);