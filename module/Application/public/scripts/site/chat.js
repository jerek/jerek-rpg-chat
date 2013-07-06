JF.site.chat = new function() {
    this.init = function() {
        JF.zend.toolbar.onCreate(function() {
            JF.zend.toolbar.move(['top', 'offset']);
            JF.zend.toolbar.hide();
        });

        JF.site.chat.actions.messages.init();
        JF.site.chat.actions.roll.init();
        JF.site.chat.scrollToBottom();
    };

    this.scrollToBottom = function(animate) {
        var $rowFrame = $('.room-rowframe');
        if (animate) {
            $rowFrame.animate({
                scrollTop: $rowFrame.height() + 100,
                duration: 'fast'
            });
            return;
        }

        $rowFrame.animate({
            scrollTop: $rowFrame.height() + 100,
            duration: 0
        });
    };
};

JF.site.chat.html = new function() {
    var prefix = 'room';

    this.getChat = function() {
        return $('table.' + prefix + '-rows');
    };

    this.getMessageWrapper = function() {
        return $('.' + prefix + '-message');
    };

    this.createMessageInput = function() {
        return $('<input type="text" maxlength="255" class="' + prefix + '-message-input">');
    };

    this.getDiceWrapper = function() {
        return $('.' + prefix + '-dice');
    };

    var dice = [ 4, 6, 8, 10, 12, 20, 100 ];

    this.createDice = function($target) {
        var $div = $target || $('<div/>');
        for (var i in dice) {
            $div.append(
                $('<a href="javascript:;"/>')
                    .html(dice[i])
                    .attr('data-sides', dice[i])
                    .click(JF.site.chat.actions.roll.click)
            );
        }
        return $div;
    };
};

JF.site.chat.actions = new function() {
    this.send = function(type, value) {
        if (!value) return;

        var roomId = JF.site.getRoomNumber();

        if (roomId) {
            $.ajax('/room/' + roomId + '/' + type + '/' + value, {
                complete: this.sent,
                error: this.error
            });
        }
    };

    this.sent = function(jqXHR, textStatus) {
        JF.log(jqXHR);
        JF.log(jqXHR.responseText);

        if (jqXHR.responseText) {
            var $chat = JF.site.chat.html.getChat();
            $chat.append(jqXHR.responseText);
        }

        JF.site.chat.scrollToBottom();
    };

    this.error = function() {
        JF.debug(arguments);
    };
};

JF.site.chat.actions.messages = new function() {
    this.init = function() {
        var $entryForm = JF.site.chat.html.getMessageWrapper();
        $entryForm.empty();
        $entryForm.submit(JF.site.chat.actions.messages.submit);

        var $input = JF.site.chat.html.createMessageInput();

        $entryForm.append($('<form/>').append($input));

        $input.focus();
    };

    this.submit = function() {
        var $message = $('input', this);

        JF.site.chat.actions.send('message', $message.val());

        return false;
    };
};

JF.site.chat.actions.roll = new function() {
    this.init = function() {
        var $dice = JF.site.chat.html.getDiceWrapper();
        $dice.empty();

        JF.site.chat.html.createDice($dice);
    };

    this.click = function() {
        var sides = $(this).attr('data-sides');

        if (sides) {
            JF.site.chat.actions.send('roll', sides);
        }

        return false;
    };
};
