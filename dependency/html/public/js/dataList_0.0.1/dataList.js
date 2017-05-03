var DataList = {
	dataList: {},
	paginationHTML :'<div class="datalistPagination pull-right hide">'+
                		'<nav>'+
                    		'<ul class="pagination pagination-sm" style="margin:0">'+
                        		'<li><a href="#" class="previousPage" title="Previous"><span class="fa fa-angle-double-left"><\/span><\/a><\/li>'+
                        		'<li><a href="#" class="nextPage" title="Next"><span class="fa fa-angle-double-right"><\/span><\/a><\/li>'+
                    		'<\/ul>'+
                		'<\/nav>'+
            		'<\/div>',
    sortingBtn     :'<div class="btn-group">'+
                        '<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                            '<i class="fa fa-filter"\/>'+
                        '<\/button>'+
                    '<\/div>',         
	selectAllHTML  :'<h4 class="pull-left" style="width:15px"><i class="selectAll multipleSelection fa fa-square-o" style="cursor:pointer"\/><\/h4>',
    selectorHTML   :'<h4 class="pull-left" style="width:15px"><i class="multipleSelection fa fa-square-o" style="cursor:pointer"\/><\/h4>',
        

	init: function(options, element) {
		var id = Math.round(new Date().getTime() + (Math.random() * 100));

        var row = element.children('.row:first');
        var list = $('<div/>').addClass('list').appendTo(element);
        var sortingInput = $('<div>').addClass('pull-right dataList-sorting').css('padding', '0px 5px');

        if (row.length == 0) {
            row = $('<div/>').addClass('row').prependTo(element);
        }

        // Build sorting input
        if (options.sorting) {
            var sortingBtn = this.sortingBtn;
            var ul = $('<ul/>').addClass('dropdown-menu');

            sortingInput.prepend(sortingBtn);
            $.each(options.sorting, function() {
                $('<li/>').data('value', this.fieldName).data('order', '<').append(
                    $('<a/>').attr('href', '#').text('< '+this.label).on('click', DataList.bind_dataOrdering)
                ).appendTo(ul);
                $('<li/>').data('value', this.fieldName).data('order', '>').append(
                    $('<a/>').attr('href', '#').text('> '+this.label).on('click', DataList.bind_dataOrdering)
                ).appendTo(ul);
            });
            ul.children('li:first').addClass('active');
            sortingInput.find('.btn-group').append(ul);
            /*var select = $('<select/>').addClass('form-control input-sm').css('color', 'grey').prependTo(sortingInput);
            $.each(options.sorting, function() {
                $('<option/>').val(this.fieldName).data('order', '<').text('< '+this.label).appendTo(select);
                $('<option/>').val(this.fieldName).data('order', '>').text('> '+this.label).appendTo(select);
            });

            select.on('change', DataList.bind_dataOrdering);
            */
        }
		
        // Build header row
        row.prepend(this.selectAllHTML)
           .prepend(sortingInput)
           .prepend(this.paginationHTML)
           .removeClass('hide')
           .find('.selectAll').on('click', DataList.bind_selectAll).on('click', DataList.bind_selection);;

        this.dataList[id] = {
            element      : element,
            list         : list
        };

        // Set message for empty list
        if (options.emptyMessage) {
            this.dataList[id].emptyMessage = $(options.emptyMessage);
            list.before(this.dataList[id].emptyMessage.addClass('emptyMessage hide'));
        }
        
		this.build(id, options);

		return id;
	},

	destroy: function(id) {
		delete(this.dataList[id]);
        buildPaginationButtons(id);

	},

    build: function(id, options) {
        this.dataList[id] = {
            datas           : options.datas,
            rowMerge        : options.rowMerge,
            rowMaxNumber    : options.rowMaxNumber,
            currentRange    : options.currentRange,
            element         : this.dataList[id].element,
            list            : this.dataList[id].list,
            emptyMessage    : this.dataList[id].emptyMessage,
        };

        this.buildPaginationButtons(id);

        // Order the list if an order option is selected
        var orderSelect = this.dataList[id].element.find('.dataList-sorting select');
        if (orderSelect.length) {
            this.sort(id, orderSelect.val(), orderSelect.find('option:selected').data('order'));

        } else {
            this.buildList(id);
        }
    },

	buildPaginationButtons: function(id) {
        var pagination = this.dataList[id].element.find('.datalistPagination');
        pagination.find('li').not('li:first, li:last').remove();

        if (this.dataList[id].datas.length > this.dataList[id].rowMaxNumber) {
            var lastLi = pagination.find('ul > li:last-child');
            var pageLi = []
            pagination.find('ul > li').not(':first').not(':last').empty();

            var pageNumber = this.dataList[id].datas.length / this.dataList[id].rowMaxNumber;
            if (this.dataList[id].datas.length % this.dataList[id].rowMaxNumber != 0) { pageNumber++ }

            this.dataList[id].pageNumber = pageNumber;

            for (var i=1; i<= pageNumber; i++) {
                var li = $('<li/>').append($('<a/>').attr('href', '#').html(i));
                lastLi.before(li);
                pageLi.push(li);
            }

            pageLi[0].addClass('active');

            pagination.removeClass('hide').find('a').off().on('click', DataList.bind_pageChanging);
            this.dataList[id].currentRange = 0;
            this.condensePaginationDisplay(id);


        } else {
            pagination.addClass('hide');
        }
    },

    condensePaginationDisplay: function(id) {

        if (this.dataList[id].pageNumber <7) {
            return;
        }

        var list = this.dataList[id].element.find('.datalistPagination');
        list.find('.dots').remove();
        var buttons = list.find('li');
        buttons.removeClass('hide');

        var permanantButtons = list.find('li:first, li:eq(1), li:eq('+ parseInt(this.dataList[id].pageNumber) +'), li:last');
        var selection  = null;

        var dots = $('<li/>').addClass('dots').append($('<a/>').attr('href', '#').text('...')).addClass('disabled');

        if (this.dataList[id].currentRange < 4) {
            selection = buttons.slice(0,6);
            selection.last().after(dots.clone());

        } else if (this.dataList[id].currentRange > this.dataList[id].pageNumber - 5) {
            selection = buttons.slice(this.dataList[id].pageNumber - 4, this.dataList[id].pageNumber+1);
            selection.first().before(dots.clone());

        } else {
            selection = list.find('li:eq('+parseInt(this.dataList[id].currentRange)+'), li:eq('+parseInt(this.dataList[id].currentRange + 1)+'), li:eq('+parseInt(this.dataList[id].currentRange + 2)+')')
            selection.first().before(dots.clone());
            selection.last().after(dots.clone());
        }

        buttons.not(permanantButtons).not(selection).addClass('hide');
    },

    buildList: function(id, range) {
        this.dataList[id].list.empty();

        if (this.dataList[id].emptyMessage) {
            if (this.dataList[id].datas.length == 0) {
                this.dataList[id].emptyMessage.removeClass('hide');
            } else {
                this.dataList[id].emptyMessage.addClass('hide');
            }
        }

	    if (!range) {
	        range = 0;
	    }

	    var rowStart = range * this.dataList[id].rowMaxNumber;
	    var rowEnd = rowStart + this.dataList[id].rowMaxNumber;

	    this.dataList[id].currentRange = range;

	    for(var i=rowStart; i<rowEnd && i<this.dataList[id].datas.length; i++) {
	        if (!this.dataList[id].datas[i].html) {
	            var row = this.dataList[id].rowMerge(this.dataList[id].datas[i]);
                row.addClass('dataListElement').prepend(this.selectorHTML);
	            this.dataList[id].datas[i].html = row;
	        }

	        this.dataList[id].list.append(this.dataList[id].datas[i].html.data('index', i));
	    }

	    this.dataList[id].element.find('.selectAll').removeClass('fa-check-square-o').addClass('fa-square-o');
        this.dataList[id].element.find('.multipleSelection').not('.selectAll').on('click', DataList.bind_selection);
	},

    remove: function(id, index) {
        this.dataList[id].datas.splice(index,1);
        this.buildPaginationButtons(id);
        this.buildList(id, this.dataList[id].currentRange);
    },

    sort: function(id, fieldName, order) {
        this.dataList[id].datas.sort(function (a, b) {
            var aVal = a[fieldName].toLowerCase();
            var bVal = b[fieldName].toLowerCase();

            if (order == ">" || order.toLowerCase() == "desc") {
                return ((aVal > bVal) ? -1 : ((aVal < bVal) ? 1 : 0));
            }
            return ((aVal < bVal) ? -1 : ((aVal > bVal) ? 1 : 0));
        });
        this.buildList(id);
        this.dataList[id].element.find('.datalistPagination').find('li').removeClass('active').eq(1).addClass('active');
    },

    bind_selection: function() {
        var checkbox = $(this);

        if (checkbox.hasClass('fa-square-o')) {
            checkbox.removeClass('fa-square-o').addClass('fa-check-square-o')
                    .closest('.dataListElement').addClass('bg-info');
        } else {
            checkbox.removeClass('fa-check-square-o').addClass('fa-square-o')
                    .closest('.dataListElement').removeClass('bg-info');
        }
    },

    bind_selectAll: function() {
        var checkbox = $(this);
        var dataList = checkbox.closest('.dataList').children('.list');

        if (checkbox.hasClass('fa-check-square-o')) {
            dataList.find('.fa-check-square-o').not('.selectAll').click();
        } else {
            dataList.find('.fa-square-o').not('.selectAll').click();
        }
    },

    bind_pageChanging: function() {
        var a = $(this);
        var pagination = a.closest('.datalistPagination');
        var id = a.closest('.dataList').data('datalist-id');
        if (a.hasClass('previousPage')) {
            var range = DataList.dataList[id].currentRange - 1;
            if (range >= 0) {
                DataList.buildList(id, range);
                pagination.find('.active').removeClass('active').prev().addClass('active');
            }
        } else if (a.hasClass('nextPage')) {
            var range = DataList.dataList[id].currentRange + 1;
            if (range <= DataList.dataList[id].pageNumber - 1) {
                DataList.buildList(id, range);
                pagination.find('.active').removeClass('active').next().addClass('active');
            }
        } else {
            DataList.buildList(id, parseInt(a.text())-1);
            pagination.find('.active').removeClass('active');
            a.parent().addClass('active');
        }

        DataList.condensePaginationDisplay(id);
    },

    bind_dataOrdering: function() {
        var a = $(this);
        var li = a.parent();
        var id = a.closest('.dataList').data('datalist-id');

        a.closest('ul').find('.active').removeClass('active');
        a.parent().addClass('active');

        return DataList.sort(id, li.data('value'), li.data('order'));
    }

}

$.fn.dataList = function(options) {
    return this.each(function() {
        var id = $(this).data('datalist-id');
        if (!id) {
            var datalistId = DataList.init(options, $(this));
            $(this).data('datalist-id', datalistId)
                   .addClass('dataList')
                   .on('remove', function() {
                        DataList.destroy(datalistId);
            });
        } else {
            DataList.build(id, options);
        }
    });
}

$.fn.removeFromDataList = function() {
    return this.each(function() {
        var index = $(this).data('index');
        if (index !== undefined) {
            var id = $(this).closest('.dataList').data('datalist-id');
            DataList.remove(id, index);
        } else {
            console.error("Not a dataList element.");
        }
    });
}
