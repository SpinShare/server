<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Speen Orbital OS</title>

        <link rel="stylesheet" href="app.css" />
    </head>
    <body>
        <div class="terminal-not-logged-in active">
            <h1>Login required</h1>
            <p>Please log into your SpinSha.re account to continue</p>

            <a href="https://spinsha.re/login">Login</a>
        </div>

        <div class="output">
        </div>
        <div class="input-box">
        user@speen-orbital:~# <input type="text" autocomplete="off" id="input" />
        </div>

        <div class="overlay"></div>
        <script src="app.js"></script>
    </body>
</html>