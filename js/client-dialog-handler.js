function show() {
  let dialog = document.getElementById("dialog");
  function showDialog() {
    dialog.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        e.preventDefault();
      }
    });
    dialog.showModal();
  }
  showDialog();
}

function performClose() {
  document.getElementById("dialog").close();
}
/*
window.onload = (event) => {
  show();
};
*/

window.addEventListener ? window.addEventListener("load", show, false) : 
  window.attachEvent && window.attachEvent("onload", show);

function redirect(newUrl)
{
  window.open(newUrl, "_self");
  document.getElementById("div_forbidden").remove();
}