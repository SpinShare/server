<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speen Orbital OS</title>

    <link rel="stylesheet" href="app.css" />
</head>
<body>
<div class="output">
    <div class="output">
        <div><span style="color:inherit">&nbsp;&nbsp;&nbsp;_____&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
        <div><span style="color:inherit">&nbsp;&nbsp;/&nbsp;___/____&nbsp;&nbsp;___&nbsp;&nbsp;___&nbsp;&nbsp;____&nbsp;</span></div>
        <div><span style="color:inherit">&nbsp;&nbsp;\__&nbsp;\/&nbsp;__&nbsp;\/&nbsp;_&nbsp;\/&nbsp;_&nbsp;\/&nbsp;__&nbsp;\</span></div>
        <div><span style="color:inherit">&nbsp;___/&nbsp;/&nbsp;/_/&nbsp;/&nbsp;&nbsp;__/&nbsp;&nbsp;__/&nbsp;/&nbsp;/&nbsp;/</span></div>
        <div><span style="color:inherit">/____/&nbsp;.___/\___/\___/_/&nbsp;/_/&nbsp;</span></div>
        <div><span style="color:inherit">&nbsp;&nbsp;&nbsp;&nbsp;/_/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
        <div><span style="color:inherit">[SPEEN&nbsp;ORBITAL&nbsp;OS]</span></div>
        <div>&nbsp;</div>
        <div>&nbsp;</div>
        <div class="transmission">TRANSMISSION: </div>
        <div style="opacity: 0"><span style="color:inherit">TRANSMISSION COMPLETE</span></div>
        <div style="opacity: 0"><span style="color:inherit">THANK YOU FOR PARTICIPATING IN THIS LITTLE APRIL FOOLS ARG</span></div>
        <div style="opacity: 0"><span style="color:inherit">YOU HAVE RECEIVED A PROFILE CARD FOR YOUR EFFORTS</span></div>
        <div style="opacity: 0">&nbsp;</div>
        <div style="opacity: 0"><span style="color:inherit">&lt;3 - Team SpinShare</span></div>
    </div>

    <div class="overlay"></div>

    <script>
        (() => {
            const transmission = document.querySelector(".transmission");

            const allDivs = document.querySelectorAll(".output div");

            let interval;
            interval = setInterval(() => {
                transmission.innerText += ".";
            }, 100);
            setTimeout(() => {
                clearInterval(interval);
                allDivs.forEach(div => div.style.opacity = "1");
            }, 5 * 1000);
        })();
    </script>
</body>
</html>