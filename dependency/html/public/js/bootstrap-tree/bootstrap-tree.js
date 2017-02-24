var BootstrapTree = {
    init: function(tree) {
        tree.find('.fa')
            .not('[data-closed-icon]')
            .data('closed-icon', 'fa-plus-square')
            .data('opened-icon', 'fa-minus-square');

        tree.find('li')
            .children('ul')
            .parent()
            .addClass('parent_li')
            .children('span')
            .children('i')
            .on('click', BootstrapTree.toggleNode);

        $('.parent_li').find('span:first')
                       .find('.fa:first')
                       .each(function() {
                            $(this).addClass($(this).data('closed-icon'));
                       })

        $('.parent_li').find(' > ul > li').hide();

        tree.find('li')
            .not('.parent_li')
            .find('.fa:first')
            .each(function(){
                $(this).addClass($(this).data('default-icon'));
            })
    },

    addRoot: function(tree, element) {
        if (!element || !tree) {
            return;
        }

        var ul = $('<ul/>')

        element.appendTo(ul);
        ul.appendTo(tree);
    },

    addNode: function(parent, element) {
        if (!element || !parent) {
            return;
        }

        var ul = parent.find('> ul');
        if (ul.length == 0) {
            ul = $('<ul/>').appendTo(parent);

            parent.addClass('parent_li');
            parentIcon = parent.find('.fa:first');
            parentIcon.addClass(parentIcon.data('closed-icon'))
                      .on('click', BootstrapTree.toggleNode);
        }
            
        elementIcon = element.find('.fa:first');
        elementIcon.addClass(elementIcon.data('default-icon'));

        if (!elementIcon.data('closed-icon')) {
            elementIcon.data('closed-icon', 'fa-plus-square')
                       .data('opened-icon', 'fa-minus-square');
        }

        element.appendTo(ul);
        //this.openNode(parent);
    },

    removeNode: function(element) {
        if (element.prop("tagName") != 'LI') {
            return;
        }

        var ul = element.closest('ul');
        var icon = ul.closest('li').find('.fa');

        if (ul.find('>li').length <= 1) {
            ul.remove();
            icon.removeClass(icon.data('opened-icon') + ' ' + icon.data('closed-icon'))
                .addClass(icon.data('default-icon'));

        } else {
            element.remove();
        }
    },

    openNode: function(element) {
        if (element.children('ul').children('li:hidden').length > 0) {
            element.find('i:first').click();
        }
    },

    toggleNode: function(event) {
        var children = $(this).closest('li.parent_li').find(' > ul > li');
        var i = $(this).parent().find(' > i');

        var closedIcon = i.data('closed-icon');
        var openedIcon = i.data('opened-icon');

        if (i.hasClass(openedIcon)) {
            children.hide('fast');
            i.addClass(closedIcon).removeClass(openedIcon);
        }
        else {
            children.show('fast');
            i.addClass(openedIcon).removeClass(closedIcon);
        }
        event.stopPropagation();
        $('.tree').find('.hideTreeElement').css('display', 'none');
    },

    findNode: function(tree, text) {
        this.openNode(tree.find("li:contains('"+text+"')"));
    },

    move: function(element, target) {
        if (!element || !target) {
            return;
        }

        var ul = target.find('> ul');
        if (ul.length == 0) {
            ul = $('<ul/>').appendTo(target);

            targetIcon = target.find('.fa:first')
            target.addClass('parent_li');

            targetIcon.addClass(targetIcon.data('opened-icon'))
                      .on('click', BootstrapTree.toggleNode);
        }
        ul.append(element);

        var targetUl = target.find('ul');
        if (targetUl.length == 0) {
            targetUl.remove();
        }
    }

}