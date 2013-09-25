// zajaxovatění odkazů provedu takto
$("a.ajax").live("click", function(event) {
    event.preventDefault();
    $.get(this.href);
});

// odesílání formulářů
$('form.ajax').live('submit', function(event) {
    event.preventDefault();
    $.post(this.action, $(this).serialize());
});

(function($) {
    $.fn.sumHTML = function() {
        var sum = 0;
        this.each(function() {
            var num = parseInt($(this).html(), 10);
            sum += (num || 0);
        });
        return sum;
    };
})(jQuery);

$(function() {
    $.nette.init();
});

