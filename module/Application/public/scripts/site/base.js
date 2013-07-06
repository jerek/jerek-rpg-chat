JF.site = new function() {
    this.getRoomNumber = function() {
        var url = location + '';
        var pieces = JF.urlPieces(url);

        if (pieces) {
            var match = pieces.path.match(/^room\/([0-9]+)/);
            if (match) {
                return match[1];
            }
        }

        return false;
    }
};
