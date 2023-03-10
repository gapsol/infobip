var form, url, surl, urli, surli, urlbtn, surlbtn;

function init() {
  let split = window.location.search.split("?id=");

  if (split.length > 1 && split[1] !== "") {
    findUrl(split[1]);
  } else {
    form = document.getElementById("shortenForm");
    url = document.getElementById("url");
    surl = document.getElementById("surl");
    urli = document.getElementById("url-info");
    surli = document.getElementById("surl-info");
    urlbtn = document.getElementById("url-btn");
    surlbtn = document.getElementById("surl-btn");

    form.classList.remove("hidden");
    url.addEventListener("click", hideInfo);
    surl.addEventListener("click", hideInfo);
    urlbtn.addEventListener("click", shortenUrl);
    surlbtn.addEventListener("click", copyUrl);

    getStats();
  }
}

function hideInfo() {
  if (urli && urli.classList && !urli.classList.contains("hidden")) {
    urli.classList.remove(...urli.classList);
    urli.classList.add("hidden");
  }
  if (surli && surli.classList && !surli.classList.contains("hidden")) {
    surli.classList.remove(...surli.classList);
    surli.classList.add("hidden");
  }
}

function shortenUrl(event) {
  hideInfo();
  surl.value = "";

  const xhr = new XMLHttpRequest();
  const url = "../be/?url=" + form.url.value;
  xhr.open("GET", url);
  xhr.responseType = "json";

  xhr.onload = () => {
    if (xhr.status !== 200) {
      urli.innerHTML = `(i) ${xhr.response.message}`;
      urli.classList.add("error");
      urli.classList.remove("hidden");
    } else {
      form.surl.value = xhr.response.data;
    }

    getStats();
  };

  xhr.onerror = () => {
    console.log("Request failed!");
  };

  xhr.send();
}

function findUrl(id) {
  const xhr = new XMLHttpRequest();
  const url = "../be/?id=" + id;
  xhr.open("GET", url);
  xhr.responseType = "json";

  xhr.onload = () => {
    if (xhr.status !== 200) {
      let err = document.getElementById("head-info");
      err.innerHTML = xhr.response.message;
      err.classList.remove("hidden");
    } else {
      window.location.href = xhr.response.data;
    }
  };

  xhr.onerror = () => {
    console.log("Request failed!");
  };

  xhr.send();
}

function copyUrl() {
  hideInfo();

  if (form.surl.value === "") {
    surli.classList.add("info");
    surli.innerHTML = "(i) Nothing to copy!";
  } else if (navigator.clipboard !== undefined) {
    navigator.clipboard.writeText(form.surl.value);
    surli.classList.add("success");
    surli.innerHTML = "(i) URL copied to clipboard!";
  } else {
    surli.classList.add("error");
    surli.innerHTML = "(i) Copying to clipboard failed!";
  }

  surli.classList.remove("hidden");

  getStats();
}

function getStats() {
  const xhr = new XMLHttpRequest();
  const url = "../data/stats.json";
  xhr.open("GET", url);
  xhr.responseType = "json";

  xhr.onload = () => {
    let stats = document.getElementById("stats-info");

    if (xhr.status === 200) {
      document.getElementById("s-badrequest").innerHTML = xhr.response.badrequest;
      document.getElementById("s-existing").innerHTML = xhr.response.existing;
      document.getElementById("s-invalid").innerHTML = xhr.response.invalid;
      document.getElementById("s-notfound").innerHTML = xhr.response.notfound;
      document.getElementById("s-redirected").innerHTML = xhr.response.redirected;
      document.getElementById("s-saved").innerHTML = xhr.response.saved;
      document.getElementById("s-used").innerHTML = xhr.response.used;
      stats.classList.remove("hidden");
    }
  };

  xhr.onerror = () => {
    console.log("Request failed!");
  };

  xhr.send();
}
