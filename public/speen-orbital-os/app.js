const notLoggedInBox = document.querySelector(".terminal-not-logged-in");
let isLoggedIn = false;
async function checkLogin() {
    const response = await fetch('https://spinsha.re/api/user/current');
    const data = await response.json();

    isLoggedIn = data.status === 200 && data.data.id;
    updateLogin();
}
function updateLogin() {
    if(isLoggedIn) {
        notLoggedInBox.classList.remove("active");
    } else {
        notLoggedInBox.classList.add("active");
    }
}


const consoleOutput = document.querySelector(".output");
const consoleInput = document.querySelector("#input");

let usernamePassword = "";

consoleInput.addEventListener('keyup', (e) => {
  if (e.key !== 'Enter' && e.keyCode !== 13) {
    return;
  }

  print(`§55Fuser@speen-orbital:~# §o${consoleInput.value}`);
  evaluate(consoleInput.value);
});

function print(message, typewriter = false) {
  const printBox = document.createElement("div");

  const defaultColor = "inherit";
  let currentColor = defaultColor;

  // Escape HTML first (prevents injection)
  const escapeHTML = (str) =>
    str.replace(/&/g, "&amp;")
       .replace(/</g, "&lt;")
       .replace(/ /g, "&nbsp;")
       .replace(/>/g, "&gt;");

  let text = escapeHTML(message);

    // Wrap every a-z, A-Z and 0-9 in a <span> tag with opacity 0 if typewriter = true
    if (typewriter) {
    text = text.replace(
        /§o|§[0-9A-Fa-f]{3}|&(?:[a-zA-Z]+|#\d+);|[a-zA-Z0-9\-_!?.,:'()/=]/g,
        (match) => {
            if (match === "§o" || /^§[0-9A-Fa-f]{3}$/.test(match) || match.startsWith("&")) {
                    return match;
                }

                return `<span class="typewriter-char" style="opacity:0">${match}</span>`;
            }
        );
    }

    // Replace color codes
    text = text
        .replace(/§o/g, () => {
          currentColor = defaultColor;
          return `</span><span style="color:${currentColor}">`;
        })
        .replace(/§([0-9A-Fa-f]{3})/g, (_, hex) => {
          currentColor = `#${hex}`;
          return `</span><span style="color:${currentColor}">`;
        });

    // Wrap whole message in span to allow resets
    printBox.innerHTML = `<span style="color:${defaultColor}">${text}</span>`;

    // Animate the opacity of every span sequential to 1 (kinda like a typewriter effect). if typewriter = true
    if (typewriter) {
        const chars = printBox.querySelectorAll(".typewriter-char");
        chars.forEach((char, index) => {
            setTimeout(() => {
                char.style.opacity = "1";
            }, index * 25);
        });
    }

  consoleOutput.appendChild(printBox);
}

async function evaluate() {
    if(consoleInput.value === "clear") {
        consoleOutput.innerHTML = "";
        consoleInput.value = "";
        return;
    }
    if(consoleInput.value.startsWith("login ")) {
        usernamePassword = consoleInput.value.substring(6).trim();
    }

    const request = await fetch('/ssh.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Authorization': `${usernamePassword}`
        },
        body: `input=${encodeURIComponent(consoleInput.value)}`
    });
    const response = await request.text();
    if(consoleInput.value !== "start_transmission") {
        response.split("\n").forEach(line =>  print(line, true));

        consoleOutput.scrollTo(0, consoleOutput.scrollHeight);
        consoleInput.value = "";
    } else {
        window.location.href = response;
    }
}


(async () => {
    await checkLogin();

    if(isLoggedIn) {
        print('   _____                     ');
        print('  / ___/____  ___  ___  ____ ');
        print('  \\__ \\/ __ \\/ _ \\/ _ \\/ __ \\');
        print(' ___/ / /_/ /  __/  __/ / / /');
        print('/____/ .___/\\___/\\___/_/ /_/ ');
        print('    /_/                      ');
        print("[SPEEN ORBITAL OS]");
        print("§666# Day 91 of the year");
        print(" ");
        print("Welcome to the Speen Orbital OS.");
        print("You are currently unauthenticated.");
        print("Please log in to continue.");
    }
})();