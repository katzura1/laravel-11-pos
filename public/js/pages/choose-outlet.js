class ChooseOutletPage {
  constructor() {
    this.chooseOutletForm = document.getElementById("form-choose-outlet");
    this.btnSave = document.getElementById("btn-save");

    this.initializeEventListeners();
  }

  initializeEventListeners() {
    this.chooseOutletForm.addEventListener("submit", (e) => {
      e.preventDefault();
      this.handleSaveData();
    });

    this.btnSave.addEventListener("click", () => {
      this.chooseOutletForm.dispatchEvent(new Event("submit"));
    });
  }

  async handleSaveData() {
    const outletId = this.chooseOutletForm.querySelector(
      "input[name='outlet_id']:checked"
    ).value;

    //cek apakah outletId ada
    if (!outletId) {
      showToast("Please choose an outlet", "error");
      return;
    }

    const data = {
      outlet_id: outletId,
    };
    const reponse = await submitForm("/set-outlet", data);

    if (reponse.ok) {
      showToast("Outlet saved", "success");
      setTimeout(() => {
        window.location.href = "/dashboard";
      }, 1000);
    } else {
      showToast("Failed to save outlet", "error");
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  new ChooseOutletPage();
});
