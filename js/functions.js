
const urlBase = 'http://104.248.2.194/api'
const extension = 'php';

let userID = 0;
let userName = "";
let userType = "";
let uniID = 0;

function login() {
    let login = document.getElementById("loginEmail").value;
    let password = document.getElementById("loginPassword").value;
    document.getElementById("loginRes").innerHTML = "";

    let tmp = { Email: login, Password: password };
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/User/login.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                userID = jsonObject.UID;

                if (userID < 1) {
                    document.getElementById("loginRes").innerHTML = "User/Password incorrect";
                    return;
                }

                userName = jsonObject.Name;
                userType = jsonObject.UserType;
                uniID = jsonObject.University_ID;
                saveCookie();
                if (userType == "SuperAdmin") {
                    window.location.href = "SuperAdmin.html";
                }
                else if (userType == "Admin") {
                    window.location.href = "adminRSO.html";
                }
                else {
                    window.location.href = "student.html";
                }
            }
        };
        xhr.send(jsonPayload);
    } catch (error) {
        document.getElementById("loginRes").innerHTML = error.message;
    }
}

function logout() {
    userID = 0;
    userName = "";
    userType = "";
    uniID = 0;
    document.cookie = "Name= ; expires = Thu, 01 Jan 19970 00:00:00 GMT";
    window.location.href = "index.html";
}

function signUp() {
    let name = document.getElementById("signUpName").value;
    let email = document.getElementById("signUpEmail").value;
    let password = document.getElementById("signUpPassword").value;
    let usertype = document.getElementById("signUpUserType").value;
    let university = document.getElementById("signUpUni").value;
    document.getElementById("signupRes").innerHTML = "";

    let tmp = { Name: name, Email: email, Password: password, UserType: usertype, University: university};
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/User/signup.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                userID = jsonObject.UID;

                if (userID < 1) {
                    document.getElementById("signupResult").innerHTML = "Sign up failed: " + jsonObject.error;
                    return;
                }

                userName = jsonObject.Name;
                userType = jsonObject.UserType;
                uniID = jsonObject.University_ID;
                saveCookie();
                if (userType == "SuperAdmin") {
                    window.location.href = "SuperAdmin.html";
                }
                else if (userType == "Admin") {
                    window.location.href = "adminRSO.html";
                }
                else {
                    window.location.href = "student.html";
                }
            }
        };

        xhr.send(jsonPayload);
    } catch (error) {
        document.getElementById("signupRes").innerHTML = err.message;
    }
}

function saveCookie() {
    let minutes = 20;
    let date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000));
    document.cookie = "Name=" + userName + ",userID=" + userID + ",UniversityID=" + uniID + ",userType=" + userType + ";expires=" + date.toGMTString();
}

function readCookie() {
    userID = -1;
    let data = document.cookie;
    let splits = data.split(",");
    for (var i = 0; i < splits.length; i++) {
        let thisOne = splits[i].trim();
        let tokens = thisOne.split("=");
        if (tokens[0] == "Name") {
            userName = tokens[1];
        }
        else if (tokens[0] == "userID") {
            userID = tokens[1];
        }
        else if (tokens[0] == "UniversityID") {
            uniID = tokens[1];
        }
        else if (tokens[0] == "userType") {
            userType = parseInt(tokens[1].trim());
        }
    }

    if (userID < 0) {
        window.location.href = "index.html";
    }
}

function promoteUser() {
    let email = document.getElementById("updateEmail").value;
    document.getElementById("updateResult").innerHTML = "";

    let tmp = { Email: email };
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/User/promote.' + extension;

    let xhr = new XMLHttpRequest();

    xhr.open("PUT", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("updateResult").innerHTML = "User Promoted";
            }
        };
        xhr.send(jsonPayload);
    }
    catch (error) {
        document.getElementById("updateResult").innerHTML = "Could not update user: " + error.message;
    }

}

function adminCreateUser() {
    let name = document.getElementById("signUpName").value;
    let email = document.getElementById("signUpEmail").value;
    let password = document.getElementById("signUpPassword").value;
    let usertype = document.getElementById("signUpUserType").value;
    let uni = document.getElementById("signUpUni").value;
    document.getElementById("signupRes").innerHTML = "";

    let tmp = { Name: name, Email: email, Password: password, UserType: usertype, University: uni};
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/User/signup.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                userID = jsonObject.UID;

                if (userID < 1) {
                    document.getElementById("signupResult").innerHTML = "Create User Failed: " + jsonObject.error;
                    return;
                }
            }
        };
        xhr.send(jsonPayload);
    } catch (error) {
        document.getElementById("signupRes").innerHTML = err.message;
    }
}

function createRSO() {
    let name = document.getElementById("rsoName").value;
    let uniID = document.getElementById("uniID").value;
    let desc = document.getElementById("rsoDescription").value;

    let tmp = { Name: name, University_ID: uniID, Description: desc };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/RSO/create.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                document.getElementById("rsoRES").innerHTML = "RSO Created";
            }
        };
        xhr.send(jsonPayload);
    }
    catch (error) {
        document.getElementById("rsoRES").innerHTML = "RSO Create Failed: " + error.message;
    }

}

function joinRSO(){}

function leaveRSO(){}

function createEvent() {}

async function loadUserRSOs(uid) {
    // Fetch the available and joined RSOs for the user

    const response = await fetch("/api/RSO/getRSO.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ UID: uid })
    });
    const data = await response.json();

    const joinContainer = document.getElementById("available-rsos");
    const joinedContainer = document.getElementById("joined-rsos");

    joinContainer.innerHTML = "";
    joinedContainer.innerHTML = "";

    console.log(data);
    // Available RSOs
    data.available_rsos.forEach(rso => {
        const rsoCard = document.createElement("div");
        rsoCard.className = "event-card";
        rsoCard.innerHTML = `
      <h3>${rso.Name}</h3>
      <p>${rso.Description}</p>
      <button class="btn" onclick="joinRSO(${uid}, ${rso.RSOID})">Join</button>
    `;
        joinContainer.appendChild(rsoCard);
    });

    // Joined RSOs
    data.joined_rsos.forEach(rso => {
        const rsoCard = document.createElement("div");
        rsoCard.className = "event-card";
        rsoCard.innerHTML = `
      <h3>${rso.Name}</h3>
      <p>${rso.Description}</p>
      <button class="btn" onclick="leaveRSO(${uid}, ${rso.RSOID})">Leave</button>
    `;
        joinedContainer.appendChild(rsoCard);
    });
}

function joinRSO(uid, rsoid) {
    fetch("/php/RSO/join.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ UID: uid, RSOID: rsoid })
    }).then(() => loadUserRSOs(uid));
}

function leaveRSO(uid, rsoid) {
    fetch("/php/RSO/leave.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ UID: uid, RSOID: rsoid })
    }).then(() => loadUserRSOs(uid));
}

async function loadRSOEvents() {
    try {
        const response = await fetch("/php/rsoEvents.php");
        const events = await response.json();

        const container = document.querySelector("section:nth-of-type(2) .grid") || createGrid("section:nth-of-type(2)");
        container.innerHTML = ""; // Clear existing cards

        events.forEach(event => {
            const card = document.createElement("div");
            card.classList.add("event-card");

            card.innerHTML = `
        <h3>${event.Name}</h3>
        <div class="event-type">${event.EventType} Event</div>
        <div class="event-details">
          <p><strong>Date:</strong> ${formatDate(event.Date)}</p>
          <p><strong>Start:</strong> ${formatTime(event.Start)}</p>
          <p><strong>End:</strong> ${formatTime(event.End)}</p>
          <p><strong>Location:</strong> ${event.Location}</p>
        </div>
        <p>${event.Description}</p>
        <div style="text-align: center; margin-top: 1rem;">
          <button class="btn" onclick="rateEvent('${event.Name}')">Rate</button>
          <button class="btn" onclick="commentEvent('${event.Name}')">Comment</button>
        </div>
      `;

            container.appendChild(card);
        });
    } catch (err) {
        console.error("Error loading RSO events:", err);
    }
}

async function loadPrivateEvents(){}

async function loadPublicEvents(){}

function formatDate(dateStr) {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateStr).toLocaleDateString(undefined, options);
}

function formatTime(timeStr) {
    const [hour, minute] = timeStr.split(":");
    return `${hour}:${minute}`;
}