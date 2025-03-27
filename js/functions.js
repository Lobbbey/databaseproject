const extension = 'php';

function login(){}

function logout(){
    document.cookie = "firstName= ; expires = Thu, 01 Jan 19970 00:00:00 GMT";
    window.location.href = "index.html";
}

function signUp(){
    let name = document.getElementById("name").value;
    let email = document.getElementById("signUpEmail").value;
    let password = document.getElementById("signUpPassword").value;
    document.getElementById("signupRes").innerHTML = "";
}

