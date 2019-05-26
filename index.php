<script>
    // Then some JavaScript in the browser:
    var conn = new WebSocket('ws://127.0.0.1:8080');
    conn.onmessage = function(e) {
        console.log(e);

        return false;
    };
    conn.onopen = function(e) {
        conn.send('{"command": "connect", "sessionId": "475409a4320d71a0e8a31731381ae24c", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
        //conn.send('{"command": "connect", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
        //conn.send('{"command": "subscribe", "channel": "app.1", "sessionId": "475409a4320d71a0e8a31731381ae24c", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
        conn.send('{"command": "queue", "channel": "condominium.1", "message": "hello my friend", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
    };
    conn.onclose = function () {
        return false;
    }
</script>


