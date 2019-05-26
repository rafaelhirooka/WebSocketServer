<script>
    // Then some JavaScript in the browser:
    var conn = new WebSocket('ws://127.0.0.1:8080');
    conn.onmessage = function(e) {
        console.log(e);

        return false;
    };
    conn.onopen = function(e) {
        conn.send('{"command": "connect", "sessionId": "9af664493ccfbf86dafcc1c607f7b1b1", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
        //conn.send('{"command": "subscribe", "channel": "app.1", "sessionId": "5a677de0e80ee506590fb7e1772fdd2a", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
        conn.send('{"command": "queue", "channel": "app.1", "message": "hello my friend", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
    };
    conn.onclose = function () {
        return false;
    }
</script>


