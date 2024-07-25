let creditRadio = document.getElementById("creditRadio");
let formInputCredit = document.getElementById("formInputCredit");

function showCreditInput() {
  if (creditRadio.checked) {
    formInputCredit.style.display = "flex";
  }
}

creditRadio.addEventListener("change", showCreditInput);
