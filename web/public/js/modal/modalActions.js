$(".actionButton").on('click', function () {
        var archiveIds = [$(this).data("archiveid")];
        var actionId = $(this).data("actionid");
        WorkflowAction.send(actionId, archiveIds);
    });