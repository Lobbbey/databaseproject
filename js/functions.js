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
                saveCookie(userID, userName, uniID, userType);
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
                saveCookie(userID, userName, uniID, userType);
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

function saveCookie(userID, userName, uniID, userType) {
    let minutes = 90;
    let date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000));
    const expires = "expires=" + date.toGMTString();

    document.cookie = `Name=${userName}; ${expires}; path=/`;
    document.cookie = `userID=${userID}; ${expires}; path=/`;
    document.cookie = `UniversityID=${uniID}; ${expires}; path=/`;
    document.cookie = `userType=${userType}; ${expires}; path=/`;
}

function readCookie(key) {
    const cookies = document.cookie.split(";"); // Split cookies by semicolon
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim(); // Remove leading/trailing spaces
        const [cookieKey, cookieValue] = cookie.split("="); // Split key and value
        if (cookieKey === key) {
            return cookieValue; // Return the value if the key matches
        }
    }
    return null; // Return null if the key is not found
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
                document.getElementById("newUser-form").reset();
            }
        };
        xhr.send(jsonPayload);
    } catch (error) {
        document.getElementById("signupRes").innerHTML = err.message;
    }
}

async function createEventAdmin(){

    const eventData = {
        Name: document.getElementById("eventName").value,
        Catagory: document.getElementById("eventCatagory").value,
        Description: document.getElementById("eventDescription").value,
        Time: document.getElementById("eventTime").value,
        Date: document.getElementById("eventDate").value,
        EventType: document.getElementById("eventType").value,
        RSOID: document.getElementById("eventRSOorUni").value,
        Location: document.getElementById("eventLocation").value,
        Phone: document.getElementById("eventPhone").value,
        Email: document.getElementById("eventEmail").value
    };

    fetch("/api/Event/create.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(eventData)
    })
        .then(res => res.text())
        .then(text => {
            console.log("📦 Raw response:", text);
            const data = JSON.parse(text);
            document.getElementById("event-form").reset();
        })
        .catch(err => console.error("❌ Error creating event:", err));

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

async function loadUserRSOs(uid) {
    // Fetch the available and joined RSOs for the user
    let tmp = { UID: uid };
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/RSO/getRSO.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                const data = JSON.parse(xhr.responseText);
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
        };
        xhr.send(jsonPayload);
    }
    catch (error) {
        console.error("Error loading RSOs:", error.message);
    }
    
}

function joinRSO(uid, rsoid) {
    fetch("/api/RSO/join.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ UID: uid, RSOID: rsoid })
    }).then(() => loadUserRSOs(uid));
}

function leaveRSO(uid, rsoid) {
    fetch("/api/RSO/leave.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ UID: uid, RSOID: rsoid })
    }).then(() => loadUserRSOs(uid));
}

async function loadRSOEvents(uid) {
    fetch("/api/Event/getRSOEvents.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ UID: uid })  
    })
        .then(res => res.json())
        .then(data => {
            const rEvents = data.rEvents || [];
            displayEvents(rEvents, "rso-events");
        })
        .catch(err => console.error("❌ Failed to load RSO events:", err));
}

function loadPrivateEvents(userID) {
    fetch("/api/Event/getPrivateEvents.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ UID: userID })
    })
        .then(res => res.json())
        .then(data => {
            const pEvents = data.private_events || [];
            displayEvents(pEvents, "private-events");
        })
        .catch(err => console.error("Failed to load private events:", err));
}

function loadPublicEvents() {
    fetch("/api/Event/getPublicEvents.php")
        .then(res => res.json())
        .then(data => {
            const pubEvents = data.public_events || [];
            displayEvents(pubEvents, "public-events", userID);
        })
        .catch(err => console.error("Failed to load public events:", err));
}

function displayEvents(events, containerId, userID) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = "";

    events.forEach(event => {
        const card = document.createElement("div");
        card.className = "event-card";
        card.innerHTML = `
      <h3>${event.Name}</h3>
      <div class="event-type">${event.EventType} Event</div>
      <div class="event-details">
        <p><strong>Date:</strong> ${formatDate(event.Date)}</p>
        <p><strong>Time:</strong> ${formatTime(event.Time)}</p>
        <p><strong>Location:</strong> ${event.Lname}</p>
        <p><strong>Phone:</strong> ${event.Phone}</p>
        <p><strong>Email:</strong> ${event.Email}</p>
      </div>
      <p>${event.Description}</p>
        <h4 class="!pt-3">Comments</h4>
    `;
        container.appendChild(card);
        const commentsContainer = document.createElement("div");
        commentsContainer.id = `comments-${event.Event_ID}`;
        commentsContainer.className = "comments-container";
        card.appendChild(commentsContainer);
        loadEventComments(event.Event_ID, `comments-${event.Event_ID}`, userID);
    });
}

async function loadEventComments(eventID, containerId, userID) {
    console.log("Loading comments for event ID:", eventID);

    fetch("/api/Comment/getComments.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ EventID: eventID })
    })

        .then(res => res.json()) 
        .then(data => {
            console.log("Comments data:", data);
            const comments = data.comments || []; 
            console.log("Comments:", comments);
            const commentsContainer = document.getElementById(containerId);
            if (!commentsContainer) return;
            comments.forEach(async comment => {
                const commentCard = document.createElement("div");
                commentCard.className = "comment-card";
                commentCard.innerHTML = `
            <div class='font-bold'>${await getUserName(comment.User_ID)}</div>
            <p>${comment.CommentText}</p>
            <p><strong>Time:</strong> ${comment.Timestamp}</p>
            `;
            if (comment.User_ID == userID) {
                commentCard.innerHTML += `<button class="btn" onclick="deleteComment(${comment.Comment_ID})">Delete</button>`;
                commentCard.innerHTML += `<button class="btn" onclick="editComment(${comment.Comment_ID})">Edit</button>`;
            }
                commentsContainer.appendChild(commentCard);
            })
            if (comments.length === 0) {
                commentsContainer.innerHTML = "<p>No comments available for this event.</p>";
            }
        });
}

async function getUserName(User_ID){
    try {
        const response = await fetch("/api/User/getUserName.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ UID: User_ID })
        });
        const data = await response.json();
        return data.Name || "Unknown User"; // Return the userName properly
    } catch (err) {
        console.error("Failed to get user name:", err);
        return "Unknown User"; // Return a fallback value in case of an error
    }
        
}

function formatDate(dateStr) {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateStr).toLocaleDateString(undefined, options);
}

function formatTime(timeStr) {
    const [hour, minute] = timeStr.split(":");
    return `${hour}:${minute}`;
}