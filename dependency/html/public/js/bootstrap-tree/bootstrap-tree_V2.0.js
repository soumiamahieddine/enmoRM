
$(document).ready (function () {
    $('.tree > ul').attr('role', 'tree');
    initNode($('.tree > ul'));
})


function initNode (node) {

    node.find('>li')
        .addClass("parent_li")
        .attr('role', 'treeitem')
        .loadNodeChildren()
        .find('i:first')
        //.addClass('fa fa-plus-square')
        .on('click', function (e) {
            var node = $(this).closest('.parent_li');
            var childrenTree = node.find(' > ul')
            var children = childrenTree.find('> li');
            if(node.data('unoppened')) {
                node.data('unoppened', false);
                initNode(childrenTree);
            }
            if (children.is(':visible')) {
                childrenTree.hide('fast');
                $(this).parent().find(' > i').addClass('fa-plus-square').removeClass('fa-minus-square');
            }
            else {
                childrenTree.show('fast');
                $(this).parent().find(' > i').addClass('fa-minus-square').removeClass('fa-plus-square');
            }
            e.stopPropagation();
            $('.tree').find('.hideTreeElement').css('display', 'none');
        });
}

$.fn.loadNodeChildren = function loadNodeChildren (node) {
    return this.each(function() {
        var node = $(this);
        
        var url = node.data('onopen');
        
        node.attr('data-unoppened', true);
        if(url) {
            var children = $('<ul/>').attr('role', 'group').css('display', 'none').appendTo(node);

            $.ajax({
                url     : url,
                type    : "GET",
                dataType: 'html',
                success : function(response) {
                    children.html(response);
                    if($.trim(response) != "") {
                        node.find('i:first').addClass('fa fa-plus-square')
                    }
                },
                error   : function() {
                    $.gritter.add({
                        text: 'Fail to load children.',
                        class_name: "gritter-danger"
                    });
                }
            })
        }
    });
}