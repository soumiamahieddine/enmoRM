/* OPTIONS
    
    datas           -> json datas to merge,
    rowMerge        -> datas merging function,
    rowMaxNumber    -> default rowNumber,
    rowTranslation  -> translation for "row"
    paginationType  -> pagination presentation :
                        input : input to select page
                        buttons : button with page number
    emptyMessage    -> html to show when the list is empty
    sorting         -> array of object that define wich properties of datas can be sorted
                       object have four properties : the name (fieldName), the label (label), the type (type with 'num' or 'txt' value) of the sortable property, and a "default" (with 'ASC' or 'DESC') property to sort by default 
    unsearchable    -> array of unserchable property
    itemsName       -> item name to display in result number as an array. The first element is the singular form the second is the plural form
    translation     -> array with key with translation
*/

var DataList = {
	dataList: {},
    filterList    :'<div class="form-group filterList pull-right" style="margin-left:5px; display:float; max-width:150px">'+
                        '<div class="input-group">'+
                            '<span class="input-group-addon" style="background-color:#fff; padding:5px 5px;"><i class="fa fa-filter" \/><\/span>'+
                            '<input type="text" class="form-control input-sm" \/>'+
                        '<\/div>'+
                    '<\/div>',
	inputPagination  :'<div class="datalistPagination pull-right">'+ // CHOICE PAGE
                		'<nav>'+
                    		'<ul class="pagination pagination-sm" style="margin:0">'+
								'<li><a href="#" class="firstPage" title="First"><span class="fa fa-angle-double-left"><\/span><\/a><\/li>'+
                        		'<li><a href="#" class="previousPage" title="Previous"><span class="fa fa-angle-left"><\/span><\/a><\/li>'+
                                '<li><a href="#" style="padding:0px"><input type="text" style="width:30px; border:none; height:27px; text-align: center; padding: 5px 2px" value="1" title="choice" class="form-control input-sm"\/></a><\/li>'+
                         		'<li><a href="#" class="nextPage" title="Next"><span class="fa fa-angle-right"><\/span><\/a><\/li>'+
					 			'<li><a href="#" class="lastPage" title="Last"><span class="fa fa-angle-double-right"><\/span><\/a><\/li>'+
                     		'<\/ul>'+
                	 	'<\/nav>'+
            		 '<\/div>',
    buttonPagination :'<div class="datalistPagination pull-right hide">'+
                		'<nav>'+
                    		'<ul class="pagination pagination-sm" style="margin:0">'+
                        		'<li><a href="#" class="previousPage" title="Previous"><span class="fa fa-angle-left"><\/span><\/a><\/li>'+
                        		'<li><a href="#" class="nextPage" title="Next"><span class="fa fa-angle-right"><\/span><\/a><\/li>'+
                    		'<\/ul>'+
                		'<\/nav>'+
            		'<\/div>',
	rowNumberInput   :'<div class="form-group pull-right datalistRowNumber" style="margin-left:5px; display:float">'+
						'<select class="form-control input-sm pull-right" style="padding:5px 5px">'+
								'<option value="10">10</option>'+
								'<option value="20">20</option>'+
								'<option value="30">30</option>'+
								'<option value="40">40</option>'+
						'</select>'+
					'</div>',
    sortingBtn      :'<div class="btn-group">'+
                        '<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                            '<i class="fa fa-sort-amount-asc"\/>'+
                        '<\/button>'+
                     '<\/div>',
	selectAllHTML   :'<h4 class="pull-left" style="width:15px"><i class="selectAll multipleSelection fa fa-square-o" style="cursor:pointer"\/><\/h4>',
    selectorHTML    :'<h4 class="pull-left" style="width:15px"><i class="multipleSelection fa fa-square-o" style="cursor:pointer"\/><\/h4>',
    resultNumberHTML:'<h2 class="itemNumber" style="margin:0px 0px 0px 40px"><small><span class="resultNumber"\/><span class="itemsName"\/><\/small><\/h2>',
    resultNumberHTMLWithTotal:'<h2 class="itemNumber" style="margin:0px 0px 0px 40px"><small><span class="resultNumber"\/><span class="itemsName"\/>&nbsp;/&nbsp;<span class="total"\/>&nbsp;total<\/small><\/h2>',

	init: function(options, element) {
		var id = Math.round(new Date().getTime() + (Math.random() * 100));

        var header = element.children('.row:first').css('padding', '0px 5px').css('border-bottom', '1px solid #DDD');
        var footer = element.children('.footer').css('padding', '0px 5px');
        var list = $('<div/>').addClass('list').appendTo(element);


        if (header.length == 0) {
            header = $('<div/>').addClass('row').prependTo(element);
        }
        if (footer.length == 0) {
            footer = $('<div/>').addClass('row footer').css('margin-top', '10px').appendTo(element);
        }

        this.dataList[id] = {
            element      : element,
            list         : list,
            toolbar      : header,
            footer       : footer
        };
        		
        // Build header row
        header.prepend(this.selectAllHTML)
           .prepend(this.filterList);

        // Build sorting input
        if (options.sorting) {
            header.prepend(this.initSortingInput(options.sorting));
        }

        if(options.paginationType){
            footer.append(this.inputPagination);

        } else {
            footer.append(this.buttonPagination);
        }

        if(!options.rowTranslation) {
            options.rowTranslation = "lines";
        }

		header.prepend(this.initRowNumberSelect(options.rowTranslation, options.rowMaxNumber))
           .removeClass('hide')
           .find('.selectAll').on('click', DataList.bind_selectAll).on('click', DataList.bind_selection);

        // Set message for empty list
        if (options.emptyMessage) {
            this.dataList[id].emptyMessage = $(options.emptyMessage);
            list.before(this.dataList[id].emptyMessage.addClass('emptyMessage hide'));
        }

        this.dataList[id].resultNumber = "<h4><span class='resultNumber'\/><\/h4>";
        if(!options.itemsName) {
            options.itemsName = ["result", "results"];
        }

        if (options.total >= options.rowMaxNumber) {
            header.append(this.resultNumberHTMLWithTotal);
        } else {
            header.append(this.resultNumberHTML);
        }

        if (options.translation) {
            $.each(options.translation, function(key, value) {
                header.find('[title='+key+']').attr('title', value);
            })
        }

		this.build(id, options);

		return id;
	},

	destroy: function(id) {
		delete(this.dataList[id]);
        buildPaginationButtons(id);

	},

    initSortingInput: function(fields) {
        var sortingInput = $('<div>').addClass('pull-right dataList-sorting').css('padding', '0px 5px');
        var sortingBtn = this.sortingBtn;
        var ul = $('<ul/>').addClass('dropdown-menu');
        sortingInput.prepend(sortingBtn);

        

        $.each(fields, function() {
            var asc = '<i class="fa fa-sort-alpha-asc"\/>';
            var desc = '<i class="fa fa-sort-alpha-desc"\/>';

            if (this.type == 'num') {
                asc = '<i class="fa fa-sort-numeric-asc"\/>';
                desc = '<i class="fa fa-sort-numeric-desc"\/>';
            }

            var ascLi = $('<li/>').data('value', this.fieldName).data('order', '<').append(
                            $('<a/>').attr('href', '#').html(" "+this.label).prepend(asc).on('click', DataList.bind_dataOrdering));

            var descLi = $('<li/>').data('value', this.fieldName).data('order', '>').append(
                            $('<a/>').attr('href', '#').html(" "+this.label).prepend(desc).on('click', DataList.bind_dataOrdering));

            ascLi.appendTo(ul);
            descLi.appendTo(ul);

            if (this.default && this.default == "ASC") {
                ascLi.addClass('active').attr('data-default', '');
            }
            if (this.default && this.default == "DESC") {
                descLi.addClass('active').attr('data-default', '');
            }
        });

        sortingInput.find('.btn-group').append(ul);

        return sortingInput;
    },

    initRowNumberSelect: function(lineText, defaultValue) {
        var select = $(this.rowNumberInput);

        select.find('option[value="10"]').text("10 " + lineText);
        select.find('option[value="20"]').text("20 " + lineText);
        select.find('option[value="30"]').text("30 " + lineText);
        select.find('option[value="40"]').text("40 " + lineText);

        if (defaultValue) {
            select.find('select').val(defaultValue);
        }

        return select;
    },

    build: function(id, options) {
        this.dataList[id] = {
            datas           : options.datas,
            rowMerge        : options.rowMerge,
            rowMaxNumber    : options.rowMaxNumber,
            currentRange    : options.currentRange,
            paginationType  : options.paginationType,
            unsearchable    : options.unsearchable,
            itemsName       : options.itemsName,
            total           : options.total,
            element         : this.dataList[id].element,
            list            : this.dataList[id].list,
            toolbar         : this.dataList[id].toolbar,
            footer          : this.dataList[id].footer,
            emptyMessage    : this.dataList[id].emptyMessage
        };

        this.buildPaginationButtons(id);

        this.dataList[id].unsearchable = ['html'];

        // Order the list if an order option is selected
        var orderSelect = this.dataList[id].element.find('.dataList-sorting li[data-default]');
        if (orderSelect.length) {
            this.sort(id, orderSelect.data('value'), orderSelect.data('order'));

        } else {
            this.buildList(id);
        }
    },

	buildPaginationButtons: function(id, filteredDatas) {
        var pagination = this.dataList[id].element.find('.datalistPagination');
		var rowNumber = this.dataList[id].element.find('.datalistRowNumber');
        var filterInput = this.dataList[id].element.find('.filterList');

        var datas = this.dataList[id].datas;
        if(filteredDatas != undefined){

            datas = filteredDatas;
        } 
            filterInput.find('input').off().on('keyup', DataList.bind_filterList);

        if (datas.length > this.dataList[id].rowMaxNumber) {
            var lastLi = pagination.find('ul > li:last-child');
            var pageLi = [];
            var pageNumber = Math.floor(datas.length / this.dataList[id].rowMaxNumber);

            if (datas.length % this.dataList[id].rowMaxNumber != 0) { pageNumber++ }  
            this.dataList[id].pageNumber = pageNumber;

            filterInput.find('input').off().on('keyup', DataList.bind_filterList);
            this.dataList[id].currentRange = 0;

            if (this.dataList[id].paginationType == "input") {
                pagination.removeClass('hide')
                          .find('input').val('1').off().on('keyup', DataList.bind_pageChoice)
                          .closest('ul').find('a').off().on('click', DataList.bind_pageChanging);

            } else {
                pagination.find('.pageBtn').parent().remove();
                pagination.find('.dots').remove();
                for (var i=1; i<= pageNumber; i++) {
                    var li = $('<li/>').append($('<a/>').attr('href', '#').addClass('pageBtn').html(i));
                    lastLi.before(li);
                    pageLi.push(li);
                }

                pageLi[0].addClass('active');
                pagination.removeClass('hide').find('a').off().on('click', DataList.bind_pageChanging);
                this.condensePaginationDisplay(id);
            }
            
            rowNumber.removeClass('hide').find('select').off().on('change', DataList.bind_rowNumberSelection);

        } else {
            pagination.addClass('hide');
        }
    },

    condensePaginationDisplay: function(id) {
        if (this.dataList[id].pageNumber <7) {
            return;
        }

        var list = this.dataList[id].footer.find('.datalistPagination');
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

        selection.removeClass('hide');
        buttons.not(permanantButtons).not(selection).addClass('hide');
    },

    buildList: function(id, range, filteredDatas) {
        var totalResult = this.dataList[id].total;
        var datas = this.dataList[id].datas;

        if(filteredDatas != undefined){
            datas = filteredDatas;
        } else {
            this.dataList[id].element.find('.filterList').find('input').val('');

        }

        if (datas.length == 1) {
            itemsName = this.dataList[id].itemsName[0];
        } else {
            itemsName = this.dataList[id].itemsName[1];
        }
        this.dataList[id].toolbar.find('.itemsName').html(" "+itemsName);

        this.dataList[id].list.empty();

        if (this.dataList[id].emptyMessage) {
            if (datas.length == 0) {
                this.dataList[id].emptyMessage.removeClass('hide');
                this.dataList[id].toolbar.find('.itemNumber').addClass('hide');
                this.dataList[id].toolbar.find('.selectAll').addClass('hide');
            } else {
                this.dataList[id].emptyMessage.addClass('hide');
                this.dataList[id].toolbar.find('.itemNumber').removeClass('hide');
                this.dataList[id].toolbar.find('.selectAll').removeClass('hide');
            }
        }

	    if (!range) {
	        range = 0;
	    }

	    var rowStart = range * this.dataList[id].rowMaxNumber;
	    var rowEnd = parseInt(rowStart) + parseInt(this.dataList[id].rowMaxNumber);

	    this.dataList[id].currentRange = range;

	    for(var i=rowStart; i<rowEnd && i<datas.length; i++) {
	        if (!datas[i].html) {
	            var row = this.dataList[id].rowMerge(datas[i]);
                row.css('padding', '0px 5px').addClass('dataListElement').prepend(this.selectorHTML);
	            datas[i].html = row;
	        }

	        this.dataList[id].list.append(datas[i].html.data('index', i));
	    }

        // Set the search result displayed (maxResult in conf)
        this.dataList[id].toolbar.find('.itemNumber .resultNumber').html(datas.length);

        // Set the total result number
        if (totalResult >= datas.length) {
            this.dataList[id].toolbar.find('.itemNumber .total').html(totalResult);
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

    /* EVENT BINDING METHODS */

    bind_selection: function() {
        var checkbox = $(this);

        if (checkbox.hasClass('fa-square-o')) {
            checkbox.removeClass('fa-square-o').addClass('fa-check-square-o')
                    .closest('.dataListElement').addClass('bg-info selected');
        } else {
            checkbox.removeClass('fa-check-square-o').addClass('fa-square-o')
                    .closest('.dataListElement').removeClass('bg-info selected');
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

        DataList.dataList[id].element.trigger("datalist.pagechanging");
        
        if (a.hasClass('previousPage')) {
            var range = DataList.dataList[id].currentRange - 1;
            if (range >= 0) {
                DataList.buildList(id, range);
                if (DataList.dataList[id].paginationType == "input") {
                    pagination.find('input').val(range + 1);
                } else {
                    pagination.find('.active').removeClass('active').prev().addClass('active');
                    DataList.condensePaginationDisplay(id);
                }
            }
        } else if (a.hasClass('nextPage')) {
            var range = DataList.dataList[id].currentRange + 1;
            if (range <= DataList.dataList[id].pageNumber - 1) {
                DataList.buildList(id, range);
                if (DataList.dataList[id].paginationType == "input") {
                    pagination.find('input').val(range + 1);
                } else {
                    pagination.find('.active').removeClass('active').next().addClass('active');
                    DataList.condensePaginationDisplay(id);
                }    
            }
        } else if (a.hasClass('firstPage')) {
            DataList.buildList(id, 0);
            if (DataList.dataList[id].paginationType == "input") {
                pagination.find('input').val(1);
            }
           
        } else if (a.hasClass('lastPage')) {
            DataList.buildList(id, DataList.dataList[id].pageNumber - 1);
            if (DataList.dataList[id].paginationType == "input") {
                pagination.find('input').val(DataList.dataList[id].pageNumber);
            }
        } else if (a.hasClass('pageBtn')) {
            DataList.buildList(id, parseInt(a.text())-1);
            pagination.find('.active').removeClass('active');
            a.parent().addClass('active');
            DataList.condensePaginationDisplay(id);
        }        
    },
	
	bind_pageChoice: function() {
		var a = $(this);
        var pagination = a.closest('.datalistPagination');
        var id = a.closest('.dataList').data('datalist-id');
        var input = pagination.find('input');

        if((input.val().match('^[0-9]*$')) && (input.val() > 0) && (input.val() < DataList.dataList[id].pageNumber + 1)){
	        DataList.buildList(id, pagination.find('input').val() - 1); 
        
        } else{
            function current(){
                input.val(DataList.dataList[id].currentRange +1);
            }
            
            setTimeout(current, 2000);
        }
    },

    bind_rowNumberSelection: function() {
		var a = $(this);
        var id = a.closest('.dataList').data('datalist-id');

		DataList.dataList[id].rowMaxNumber = a.val();
		DataList.buildPaginationButtons(id);
		DataList.buildList(id,0);
	},

    bind_dataOrdering: function() {
        var a = $(this);
        var li = a.parent();
        var id = a.closest('.dataList').data('datalist-id');

        a.closest('ul').find('.active').removeClass('active');
        a.parent().addClass('active');

        return DataList.sort(id, li.data('value'), li.data('order'));
    },

    bind_filterList: function(){
        var a = $(this);
        var id = a.closest('.dataList').data('datalist-id');
        var filterInput = a.closest('.filterList');
        var filterValue = filterInput.find('input').val().toLowerCase();
        var filteredDatas = [];

        if (filterValue == "") {
            filteredDatas = undefined;        
        } else {
            $.each(DataList.dataList[id].datas, function(key, element) {
                var position = -1;
                var unsearchable = false;
                $.each(element, function(key, value) {
                    unsearchable = DataList.dataList[id].unsearchable.indexOf(key) != -1;

                    if (!unsearchable) {
                        var haystack = value;
                        if (haystack) {
                            if (typeof haystack === 'string' || haystack instanceof String) {
                                haystack = haystack.toLowerCase();
                            }
                        }
                        if (value) {
                            if (typeof haystack === 'string' || haystack instanceof String) {
                                if(value.match(/^[0-9]{4}-[0-9]{2}-[0-9]{2}/)) {
                                    haystack = value.substring(0, 10);
                                }
                            } 
                        }
                        if((typeof(haystack) == "string") || (typeof(haystack) == "number")){
                            position = haystack.indexOf(filterValue);
                        }
                    }

                    if(position != -1){
                        filteredDatas.push(element);
                        return false;
                    }
                });
            });
        }
        DataList.buildPaginationButtons(id, filteredDatas);
        DataList.buildList(id, 0, filteredDatas);      
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
