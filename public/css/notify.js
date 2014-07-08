/**
 * Created by bastian on 11.04.14.
 */
(function ($, w, d) {

    /**
     * Testimplementierung
     */
    $(d).on('click keyup', '.notify[data-notifyid] > .close', function () {
        console.log($(this).parent().data('notifyid'));
    });

    $.getJSON('/notify/index', buildNotificationDropdown);

    function buildNotificationDropdown(data) {
        var num = data.length;
        if (num === 0) {
            return false;
        }
        var dropdown = $('#notify');
        dropdown.addClass('dropdown');
        oldlink = dropdown.children('a').html();
        dropdown.html('');
        dropdown.append('<a href="#" class="dropdown-toggle" data-toggle="dropdown">' + oldlink + ' <span class="count">' + num + '</span></a>');
        dropdown.append('<ul class="dropdown-menu"></ul>');

        for (i = 0; i < num; i++) {
            var notification = data[i];
            if (i == 10) {
                $('ul', dropdown).append('<li class="divider"></li><li class="index"><a href="/notify/index">' + (num - 10) + ' weitere Nachrichten</a></li>');
                break;
            }

            $('ul', dropdown).append('<li class="' + notification.state + '"><a href="' + notification.link + '">' + notification.show + '</a></li>');
        }
    }

})(jQuery, window, document);