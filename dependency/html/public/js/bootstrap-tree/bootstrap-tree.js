var BootstrapTree = {
    init: function(tree) {

        tree.find('li')
            .children('ul')
            .parent()
            .addClass('parent_li')
            .find('> span')
            .find('i:first')
            .on('click', BootstrapTree.toggleNode);

        $('.parent_li').find('span:first').find('.fa:first').addClass('fa-plus-square');
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
              .addClass('fa-minus-square')
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
            li.find('i.fa').removeClass('fa-minus-square fa-plus-square');
        } else {
            element.remove();
        }

        this.openNode(li);
    },

    openNode: function(element) {
        element.parents('li').find('i.fa-plus-square:first').click();
    },

    toggleNode: function(event) {
        var children = $(this).closest('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            children.hide('fast');
            $(this).parent().find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
        }
        else {
            children.show('fast');
            $(this).parent().find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
        }
        event.stopPropagation();
        $('.tree').find('.hideTreeElement').css('display', 'none');
    },

    findNode: function(tree, text) {
        //tree.find('i.fa-minus-square').click();
        this.openNode(tree.find("li:contains('"+text+"')"));
    }
}