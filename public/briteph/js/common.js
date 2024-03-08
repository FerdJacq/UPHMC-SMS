var Common = {
    init: function () {
        var self = this;
        
        self.addEvents();
    },

    addEvents: function () {
        var self = this;

        $('[data-dismiss="modal"]').unbind('click').bind('click', function (e) {
            e.preventDefault();

            $('.modal').removeClass('show');
        });
    }
};

$(function() {
    Common.init()
})