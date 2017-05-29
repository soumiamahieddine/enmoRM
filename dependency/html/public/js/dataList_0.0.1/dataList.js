var DataList = {
	dataList: {},
	paginationHTML :'<div class="datalistPagination pull-right">'+ // CHOICE PAGE
                		'<nav>'+
                    		'<ul class="pagination pagination-sm" style="margin:0">'+
								'<li><a href="#" class="firstPage" title="First"><span class="fa fa-angle-double-left"><\/span><\/a><\/li>'+
                        		'<li><a href="#" class="previousPage" title="Previous"><span class="fa fa-angle-left"><\/span><\/a><\/li>'+
								'<li><a href="#" style="padding:0px"><input type="text" style="width:40px; border:none; height:27px" value="1" title="choice" id="inputChoix" class="form-control input-sm"\/></a><\/li>'+
                        		'<li><a href="#" class="nextPage" title="Next"><span class="fa fa-angle-right"><\/span><\/a><\/li>'+
								'<li><a href="#" class="lastPage" title="Last"><span class="fa fa-angle-double-right"><\/span><\/a><\/li>'+
                    		'<\/ul>'+
                		'<\/nav>'+
            		'<\/div>',
    paginationPages :'<div class="datalistPagination pull-right hide">'+
                		'<nav>'+
                    		'<ul class="pagination pagination-sm" style="margin:0">'+
                        		'<li><a href="#" class="previousPage" title="Previous"><span class="fa fa-angle-left"><\/span><\/a><\/li>'+
                        		'<li><a href="#" class="nextPage" title="Next"><span class="fa fa-angle-right"><\/span><\/a><\/li>'+
                    		'<\/ul>'+
                		'<\/nav>'+
            		'<\/div>',
    /*labelSelect    :'<div class="form-group pull-right" style="padding-left:10px; padding-top: 5px">'+
                        '<label style="font-weight:normal; font-size:12px"> Number of lines : </label>'+
                    '</div>',*/
	selectNB 	   :'<div class="form-group pull-right" style="margin-left:5px; display:float">'+
						'<select class="form-control input-sm pull-right" id="selectNB" style="height:29px">'+
								'<option value="10">10</option>'+
								'<option value="20">20</option>'+
								'<option value="30">30</option>'+
								'<option value="40">40</option>'+
						'</select>'+    
					'</div>',
    sortingBtn     :'<div class="btn-group">'+
                        '<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                            '<i class="fa fa-sort-amount-asc"\/>'+
                        '<\/button>'+
                    '<\/div>',         
	selectAllHTML  :'<h4 class="pull-left" style="width:15px"><i class="selectAll multipleSelection fa fa-square-o" style="cursor:pointer"\/><\/h4>',
    selectorHTML   :'<h4 class="pull-left" style="width:15px"><i class="multipleSelection fa fa-square-o" style="cursor:pointer"\/><\/h4>',

	
    // CHOISIR LE TYPE DE PAGINATION : --> true = Choice / false = Pages Num


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

        this.dataList[id] = {
            element      : element,
            list         : list,
        };
        		
        // Build header row
        row.prepend(this.selectAllHTML)
           .prepend(sortingInput);

        if(options.typePagination){
            row.prepend(this.paginationHTML);

        }
        else {
            row.prepend(this.paginationPages);
        }
        
        //var label = $(this.labelSelect);
        var select = $(this.selectNB);

        if(!options.textLabel){
            options.textLabel = "lines";
        } 

        select.find('option[value="10"]').text("10 " + options.textLabel);
        select.find('option[value="20"]').text("20 " + options.textLabel);
        select.find('option[value="30"]').text("30 " + options.textLabel);
        select.find('option[value="40"]').text("40 " + options.textLabel);
        
		row.prepend(select)
           .removeClass('hide')
           .find('.selectAll').on('click', DataList.bind_selectAll).on('click', DataList.bind_selection);
		   

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

        if(options.typePagination){
            this.buildPaginationButtons(id); 

        } else {
            this.buildPaginationButtonsBis(id);
        }

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

		var selectNB = this.dataList[id].element.find('.form-group');   


        if (this.dataList[id].datas.length > this.dataList[id].rowMaxNumber) {
            var lastLi = pagination.find('ul > li:last-child');
            var pageLi = [];

            var pageNumber = this.dataList[id].datas.length / this.dataList[id].rowMaxNumber;
            if (this.dataList[id].datas.length % this.dataList[id].rowMaxNumber != 0) { pageNumber++ }  

            this.dataList[id].pageNumber = pageNumber;

			// Find renvoie le premier élément trouvé (ici le premier input).
			//pagination.removeClass('hide').find('input').off().on('keypress', DataList.bind_choiceEnter); // Pourquoi aucune détection du clic ... ?
			pagination.removeClass('hide').find('input').off().on('keyup', DataList.bind_choicePage); 
            pagination.find('a').off().on('click', DataList.bind_pageChanging);
            selectNB.removeClass('hide').find('select').off().on('change', DataList.bind_selectNB);
            this.dataList[id].currentRange = 0;
            //this.condensePaginationDisplay(id);


        } else {
            pagination.addClass('hide');
        }
    },

    buildPaginationButtonsBis: function(id) {
        var pagination = this.dataList[id].element.find('.datalistPagination');
        var selectNB = this.dataList[id].element.find('.form-group');
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

            pagination.removeClass('hide').find('a').off().on('click', DataList.bind_pageChangingBis);

            selectNB.removeClass('hide').find('select').off().on('change', DataList.bind_selectNBBis);
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
	    var rowEnd = parseInt(rowStart) + parseInt(this.dataList[id].rowMaxNumber);


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
                $('#inputChoix').val(range + 1);
            }
        } else if (a.hasClass('nextPage')) {
            var range = DataList.dataList[id].currentRange + 1;
            if (range <= DataList.dataList[id].pageNumber - 1) {
                DataList.buildList(id, range);
                $('#inputChoix').val(range + 1);
    
            }
        } else if (a.hasClass('firstPage')) {
           DataList.buildList(id, 0);
           $('#inputChoix').val(1);
           
        } else if (a.hasClass('lastPage')) {
            $('#inputChoix').val(DataList.dataList[id].pageNumber-1);
            DataList.buildList(id, DataList.dataList[id].pageNumber - 1);
        }
    },

    bind_pageChangingBis: function() {
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
	
	bind_choicePage: function() {
		
		var a = $(this);
        var pagination = a.closest('.datalistPagination');
        var id = a.closest('.dataList').data('datalist-id');
		DataList.buildList(id, $('#inputChoix').val() - 1);
    },
	
	bind_selectNBBis: function() {
		
		
		var a = $(this);
        var id = a.closest('.dataList').data('datalist-id');
		DataList.dataList[id].rowMaxNumber = $('#selectNB').val();
		DataList.buildPaginationButtons(id);
		DataList.buildList(id,0);
		
		
		
		
	},

    bind_selectNB: function() {
		
		
		var a = $(this);
        var id = a.closest('.dataList').data('datalist-id');
		DataList.dataList[id].rowMaxNumber = $('#selectNB').val();
		DataList.buildPaginationButtonsBis(id);
		DataList.buildList(id,0);
		$('#inputChoix').val(1);
		
		
		
		
	},
	
	bind_choiceEnter: function() {
		
		alert("ON PASSE ICI !!");
		var a = $(this);
        var pagination = a.closest('.datalistPagination');
        var id = a.closest('.dataList').data('datalist-id');
		DataList.buildList(id, $('#inputChoix').val() - 1);
		
        
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
