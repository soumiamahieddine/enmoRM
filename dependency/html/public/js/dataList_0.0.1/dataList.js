var DataList = {
	dataList: {},
	paginationHTML :'<div class="datalistPagination pull-right hide">'+
                		'<nav>'+
                    		'<ul class="pagination pagination-sm" style="margin:0">'+
                        		'<li><a href="#" class="previousPage" title="Previous"><span class="fa fa-angle-double-left"></span></a></li>'+
                        		'<li><a href="#" class="nextPage" title="Next"><span class="fa fa-angle-double-right"></span></a></li>'+
                    		'</ul>'+
                		'</nav>'+
            		'</div>',
	headerHTML :'<h4 class="pull-left" style="width:15px"><i class="selectAll archiveSelection fa fa-square-o"/></h4>',
        

	init: function(options, element) {
		var id = Math.round(new Date().getTime() + (Math.random() * 100));
        var row = element.children('.row:first');
        var list = $('<div/>').addClass('list').appendTo(element);
        var sortingInput = $('<div>').addClass('pull-right dataList-sorting').css('padding', '0px 5px');

        if (row.length == 0) {
            row = $('<div/>').addClass('row').prependTo(element);
        }

        row.removeClass('hide');
        
        if (options.sorting) {
            var select = $('<select/>').addClass('form-control input-sm orderBy').css('color', 'grey').prependTo(sortingInput);
            $.each(options.sorting, function() {
                $('<option/>').val(this.fieldName).data('order', '<').text('< '+this.label).appendTo(select);
                $('<option/>').val(this.fieldName).data('order', '>').text('> '+this.label).appendTo(select);
            });
        }
		
        row.prepend(this.headerHTML).prepend(sortingInput).prepend(this.paginationHTML);

        this.dataList[id] = {
            element      : element,
            list         : list
        };

        if (options.emptyMessage) {
            this.dataList[id].emptyMessage = $(options.emptyMessage);
            element.before(this.dataList[id].emptyMessage.addClass('emptyMessage hide'));
        }
        
		this.build(id, options);

		return id;
	},

	destroy: function(id) {
		delete(this.dataList[id]);
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
        var orderSelect = this.dataList[id].element.find('select.orderBy');
        if (orderSelect.length) {
            this.sort(id, orderSelect.val(), orderSelect.find('option:selected').data('order'));
        } else {
            this.buildList(id);
        }
    },

	buildPaginationButtons: function(id) {
        var pagination = this.dataList[id].element.find('.datalistPagination');

        if (this.dataList[id].datas.length > this.dataList[id].rowMaxNumber) {
            var lastLi = pagination.find('ul > li:last-child');
            var pageLi = []
            pagination.find('ul > li').not(':first').not(':last').html('');

            var pageNumber = this.dataList[id].datas.length / this.dataList[id].rowMaxNumber;
            if (this.dataList[id].datas.length % this.dataList[id].rowMaxNumber != 0) { pageNumber++ }

            this.dataList[id].pageNumber = pageNumber;

            for (var i=1; i<= pageNumber; i++) {
                var li = $('<li/>').append($('<a/>').attr('href', '#').html(i));
                lastLi.before(li);
                pageLi.push(li);
            }

            pageLi[0].addClass('active');

            pagination.removeClass('hide');

        } else {
            pagination.addClass('hide');
        }
    },

    buildList: function(id, range) {
        this.dataList[id].list.html('');

        if (this.dataList[id].emptyMessage) {
            if (this.dataList[id].datas.length == 0) {
                this.dataList[id].emptyMessage.removeClass('hide');
                this.dataList[id].element.addClass('hide');
            } else {
                this.dataList[id].emptyMessage.addClass('hide');
                this.dataList[id].element.removeClass('hide');
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
                row.addClass('dataListElement')
	            this.dataList[id].datas[i].html = row;
	        }

	        this.dataList[id].list.append(this.dataList[id].datas[i].html.data('index', i));
	    }

	    this.dataList[id].element.find('.selectAll').removeClass('fa-check-square-o').addClass('fa-square-o');
	},

    remove: function(id, index) {
        this.dataList[id].datas.splice(index,1);
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

// List multiple selection
$(document).on('click', '.dataList .archiveSelection', function() {
    var checkbox = $(this);
    if (checkbox.hasClass('fa-square-o')) {
        checkbox.removeClass('fa-square-o').addClass('fa-check-square-o')
                .closest('.dataListElement').addClass('bg-info');
    } else {
        checkbox.removeClass('fa-check-square-o').addClass('fa-square-o')
                .closest('.dataListElement').removeClass('bg-info');
    }
})

$(document).on('click', '.dataList .selectAll', function() {
    var checkbox = $(this);
    var dataList = checkbox.closest('.dataList');

    if (checkbox.hasClass('fa-square-o')) {
        dataList.find('.fa-check-square-o').click();
    } else {
        dataList.find('.fa-square-o').click();
    }
})

// List pagination
$(document).on('click', '.dataList .datalistPagination a', function() {
    var a = $(this);
    var pagination = a.closest('.datalistPagination');
    var datalistId = a.closest('.dataList').data('datalist-id');

    if (a.hasClass('previousPage')) {
        var range = DataList.currentRange - 1;
        if (range >= 0) {
            DataList.buildList(datalistId, range);
            pagination.find('.active').removeClass('active').prev().addClass('active');
        }
    } else if (a.hasClass('nextPage')) {
        var range = DataList.currentRange + 1;
        if (range <= DataList.maxRange) {
            DataList.buildList(datalistId, range);
            pagination.find('.active').removeClass('active').next().addClass('active');

        }
    } else {
        DataList.buildList(datalistId, parseInt(a.text())-1);
        pagination.find('.active').removeClass('active');
        a.parent().addClass('active');
    }
})

// List sorting
$(document).on('change', '.orderBy', function(){
    var a = $(this);
    var id = a.closest('.dataList').data('datalist-id');

    return DataList.sort(id, a.val(), a.find('option:selected').data('order'));
})