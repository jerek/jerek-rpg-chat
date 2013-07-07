JF.site.chat = new function() {
    this.init = function(data) {
        JF.zend.toolbar.onCreate(function() {
            JF.zend.toolbar.remove();
            // JF.zend.toolbar.move(['top', 'offset']);
            // JF.zend.toolbar.hide();
        });

        JF.site.chat.render.content.init();
        if (data) {
            JF.site.chat.data.init(data);
        }
    };

    this.scrollToBottom = function(animate) {
        var $rowFrame = this.html.getRowFrame();
        var targetScroll = this.html.getRowFrameHeight($rowFrame);
        if (animate) {
            $rowFrame.animate({
                scrollTop: targetScroll,
                duration: 'fast'
            });
            return;
        }

        $rowFrame.scrollTop(targetScroll);
    };
};

JF.site.chat.html = new function() {
    var prefix = 'room';
    var $chat = false;

    this.getChat = function() {
        if (!$chat || !$chat.length) {
            $chat = $('table.' + prefix + '-rows');
        }
        return $chat;
    };

    this.getRowFrame = function() {
        return $('.' + prefix + '-rowframe');
    };

    this.getRowFrameHeight = function($rowFrame) {
        if (!$rowFrame) $rowFrame = this.getRowFrame();

        return $('.' + prefix + '-rowframe-inner', $rowFrame).height();
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
                    .click(JF.site.chat.actions.roll)
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
            $.ajax({
                url: '/room/' + roomId + '/content/' + type + '/' + value,
                dataType: 'json',
                complete: this.sent,
                error: this.error
            });
        }
    };

    this.sent = function(jqXHR, textStatus) {
        if (jqXHR.responseJSON) {
            JF.site.chat.render.row(jqXHR.responseJSON);
        }
    };

    this.error = function() {
        JF.debug(arguments);
    };

    this.submitMessage = function() {
        var $input = $('input', this);
        var message = $input.val();

        if (message) {
            JF.site.chat.actions.send('message', message);
            $input.val('');
        }

        return false;
    };

    this.roll = function() {
        var $this = $(this);
        var sides = $this.attr('data-sides');
        var count = $this.attr('data-count');

        if (sides) {
            if (count) {
                JF.site.chat.actions.send('roll', count + 'd' + sides);
            } else {
                JF.site.chat.actions.send('roll', sides);
            }
        }

        return false;
    };
};

JF.site.chat.data = new function() {
    var rows = [];

    this.init = function(data) {
        if ($.isPlainObject(data)) {
            rows = data;
        }
        JF.site.chat.render.refresh();
    };

    this.get = function() {
        return rows;
    };

    this.add = function(data) {
        if ($.isArray(data)) {
            for (var i in data) {
                this.add(data[i]);
            }
            return;
        }

        if (typeof data == 'object') {
            rows.push(data);
            JF.site.chat.render.row(data);
        }
    };
};

JF.site.chat.render = new function() {
    var columns = 2;
    var dayColumnIndex = 0;
    var last = {
        date: false,
        time: false,
        user: false
    };

    this.refresh = function() {
        var data = JF.site.chat.data.get();
        var $chat = JF.site.chat.html.getChat();
        $chat.empty();
        this.rows(data.rows);
        JF.site.chat.scrollToBottom();
    };

    this.dayRow = function(day) {
        var $chat = JF.site.chat.html.getChat();

        var $row = $('<tr/>');
        $row.attr('data-type', 'day');

        var $td;
        for (var i = 0; i < columns; i++) {
            if (i == dayColumnIndex) {
                $td = $('<td/>');
                $td
                    .append(day);
                $row.append($td);
            } else {
                $row.append($('<td/>'));
            }
        }

        $chat.append($row);
    };

    this.rows = function(rows) {
        for (var i in rows) {
            this.row(rows[i], true);
        }
    };

    this.row = function(row, doNotScroll) {
        var $chat = JF.site.chat.html.getChat();

        if (!row.user) { // This shouldn't be possible outside of testing.
            row.user = {
                id: -1,
                displayName: 'Unknown User'
            };
        }

        var rowDay = this.day(row.time);
        var timestamp = this.timestamp(row.time);

        var startDate = (last.date != rowDay);
        var startTime = (last.time != timestamp.time);
        var startUser = (last.user != row.user.id);
        last.date = rowDay;
        last.time = timestamp.time;
        last.user = row.user.id;

        if (startDate) {
            this.dayRow(rowDay);
            startUser = true;
        }

        var $row = $('<tr/>');
        $row.attr('data-type', row.type.name);
        if (startDate) {
            $row
                .addClass('start-user')
                .addClass('first-in-day');
        } else if (startUser) {
            $row.addClass('start-user');
        }

        $row.append(this.getContentColumn(row));
        $row.append(this.getTimestampColumn(row, timestamp, startTime));
        $chat.append($row);

        if (!doNotScroll) {
            JF.site.chat.scrollToBottom(true);
        }
    };

    this.getTimestampColumn = function(row, timestamp, startTime) {
        var $td = $('<td/>');
        $td.addClass('timestamp');

        var $timestamp = $('<div/>');
        if (startTime) {
            $timestamp.html(timestamp.time);
        }
        $timestamp
            .addClass('time')
            .attr('title', timestamp.detail)
            .mouseenter(function() {
                $(this).parents('tr').addClass('hover');
            })
            .mouseleave(function() {
                $(this).parents('tr').removeClass('hover');
            })
            .tooltip({
                animation: false,
                placement: 'left'
            });

        return $td.append($timestamp);
    };

    this.getContentColumn = function(row) {
        var $td = $('<td/>');
        $td
            .addClass('body')
            .append('<span class="user">' + row.user.displayName + '</span>');

        var $content = $('<span class="content"/>');
        this.content[row.type.name](row, $content);

        return $td.append($content);
    };

    this.day = function(time) {
        if (JF.dateIsToday(time.date)) {
            return 'Today';
        } else if (strtotime(time.date) > strtotime('-7 days')) {
            // Within the past 7 days
            return date('l', strtotime(time.date));
        } else if (date('Y', strtotime(time.date)) == date('Y', strtotime('now'))) {
            // The same year
            return date('F jS', strtotime(time.date));
        } else {
            // Different everything
            return date('F jS, Y', strtotime(time.date));
        }
    };

    this.timestamp = function(time) {
        return {
            time: date('g:i A', strtotime(time.date)),
            detail: date('g:i.s A, F jS, Y', strtotime(time.date))
        };
        /*
         var timestamp = {
             detail: date('g:i A, F jS, Y', strtotime(time.date)),
             old: true
         };
        // strtotime(time.date) > strtotime('-12 hours') && date('a', strtotime(time.date)) == date('a', strtotime('now'))
        // Within the last 12 hours & the same meridiem
        if (JF.dateIsToday(time.date)) {
            // Today
            timestamp.time = date('g:i A', strtotime(time.date));
            timestamp.old = false;
        } else if (strtotime(time.date) > strtotime('-7 days')) {
            // Within the past 7 days
            timestamp.time = date('l', strtotime(time.date));
        } else if (date('Y', strtotime(time.date)) == date('Y', strtotime('now'))) {
            // The same year
            timestamp.time = date('F jS', strtotime(time.date));
        } else {
            // Different everything
            timestamp.time = date('F jS, Y', strtotime(time.date));
        }
        return timestamp;
        */
    };
};

JF.site.chat.render.content = new function() {
    this.init = function() {
        // Prepare message entry
        var $entryForm = JF.site.chat.html.getMessageWrapper();
        $entryForm.empty();
        $entryForm.submit(JF.site.chat.actions.submitMessage);

        var $input = JF.site.chat.html.createMessageInput();

        $entryForm.append($('<form/>').append($input));

        $input.focus();

        // Prepare dice entry
        var $dice = JF.site.chat.html.getDiceWrapper();
        $dice.empty();

        JF.site.chat.html.createDice($dice);
    };

    this.message = function(row, $content) {
        $content.append(row.message.value);
    };

    this.roll = function(row, $content) {
        var sides = false,
            rolls = [],
            roll;
        for (var i in row.rolls) {
            sides = row.rolls[i].sides;
            rolls.push(row.rolls[i].value);
        }
        $content
            .append('rolled ')
            .append(this.rollLink(sides, rolls.length, $content))
            .append(' and got ');
        this.rollResults(rolls, sides, $content);
        $content
            .append('.');

        // Easter Egg
        if (sides == 1) {
            $content.append(' Also, reality collapses.');
        }
    };

    this.rollLink = function(sides, count, $content) {
        var $a = $('<a/>')
        $a
            .attr('href', 'javascript:;')
            .attr('data-sides', sides)
            .click(JF.site.chat.actions.roll);
        if (count == 1) {
            $a.html('<b>' + sides + '</b>-sided die');
            $content
                .append('a ')
                .append($a);
        } else {
            $a
                .attr('data-count', count)
                .html(count + ' <b>' + sides + '</b>-sided dice');
            $content
                .append($a);
        }
        return $a;
    };

    this.rollResults = function(values, sides, $content) {
        if (values.length > 1) {
            var total = 0;
            for (var i in values) {
                total += values[i];
            }
            $content
                .append(this.rollResult(total, sides))
                .append(' (');
        }
        console.log('loop');
        for (var i = 0; i < values.length; i++) {
            console.log(values[i],sides, values.length);
            $content.append(this.rollResult(values[i], sides, values.length > 1));
            if (i + 1 < values.length) {
                $content.append(', ');
            }
        }
        console.log('loop done');
        if (values.length > 1) {
            $content.append(')');
        }
        console.log('finished func');
    };

    this.rollResult = function(value, sides, unbolded) {
        if (unbolded) {
            return $($.parseHTML('' + value));
        } else {
            if (!isNaN(sides) && value == sides || value == 1) {
                return $('<strong><em>' + value + '</em></strong>');
            } else {
                return $('<strong>' + value + '</strong>');
            }
        }
    };
};
