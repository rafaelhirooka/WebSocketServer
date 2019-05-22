<script>
    // Then some JavaScript in the browser:
    var conn = new WebSocket('ws://localhost:8080');
    conn.onmessage = function(e) {
        console.log(e);
    };
    conn.onopen = function(e) {
        conn.send('{"command": "queue", "channel": "condominium.2", "message": "hello my friend"}');
        //conn.send('{"command": "subscribe", "channel": "condominium.2", "message": "hello my friend"}');
    };
</script>