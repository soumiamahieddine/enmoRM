var BootstrapTree = {
    init: function(tree) {

        tree.find('li')
            .children('ul')
            .parent()
            .addClass('parent_li')
            .find('> span')
            .find('i:first')
            .on('click', BootstrapTree.toggleNode);

        $('.parent_li').find('span:first')
                       .find('.fa:first')
                       .not('[data-closed-icon]')
                       .addClass('fa-plus-square');

        $('.parent_li').find('span:first')
                       .find('.fa[data-closed-icon]:first')
                       .each(function() {
                            console.log($(this).data('closed-icon'));
                            $(this).addClass($(this).data('closed-icon'));
                       })

        $('.parent_li').find(' > ul > li').hide();
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

            parent.addClass('parent_li')
              .find('span:first')
              .find('.fa:first')
              .addClass(this.openedIcon)
              .on('click', BootstrapTree.toggleNode);
        }
        //this.openNode(ul);
        element.appendTo(ul);
    },

    removeNode: function(element) {
        if (element.prop("tagName") != 'LI') {
            return;
        }

        var ul = element.closest('ul');
        var li = ul.closest('li');

        if (ul.find('>li').length <= 1) {
            ul.remove();
            li.find('i.fa').removeClass(this.openedIcon + ' ' + this.closedIcon);
        } else {
            element.remove();
        }

        this.openNode(li);
    },

    openNode: function(element) {
        element.parents('li').find('i.' + this.closedIcon + ':first').click();
    },

    toggleNode: function(event) {
        var children = $(this).closest('li.parent_li').find(' > ul > li');
        var i = $(this).parent().find(' > i');

        var closedIcon = i.data('closed-icon');
        var openedIcon = i.data('opened-icon');

        if (!closedIcon) {
            closedIcon = 'fa-plus-square';
            openedIcon = 'fa-minus-square';
        }

        if (children.is(':visible')) {
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
    }
}