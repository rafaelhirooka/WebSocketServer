<script>
    // Then some JavaScript in the browser:
    var conn = new WebSocket('ws://18.229.50.99:6001');
    conn.onmessage = function(e) {
        console.log(e);
    };
    conn.onopen = function(e) {
        //conn.send('{"command": "queue", "channel": "condominium.2", "message": "hello my friend"}');
        conn.send('{"command": "subscribe", "channel": "condominium.2", "secret": "rIyCgHH2j8fQnyCKhGgJjjzGIInlaA0O"}');
    };
</script>