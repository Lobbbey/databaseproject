
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
                if (userType) {
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
    document.getElementById("signupRes").innerHTML = "";

    let tmp = { Name: name, Email: email, Password: password, UserType: usertype };
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + 'User/signup.' + extension;

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
                if (userType) {
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

function saveCookie(){
    let minutes = 20;
    let date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000));
    document.cookie = "Name=" + Name + ",userID=" + userID + ",UniversityID=" + uniID + ",userType=" + userType + ";expires=" + date.toGMTString();
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
        else if(tokens[0] == "UniversityID"){
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