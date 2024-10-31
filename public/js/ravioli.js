window.addEventListener("load", () => {
  ravioliSprinkleDisplay();
  document.addEventListener("click", removeRavioliFromCheckout);
  document.addEventListener("click", addRavioliFromDisplay);
  document.addEventListener("click", removeRavioliFromDisplay);
  document.addEventListener("click", showMore);
  document.addEventListener("click", hideBanner);
});

// we currently have two display options for ravioli: modal and banner

function closeRavioliDisplay() {
  // hide modal (if there's a modal)
  if (document.getElementById("ravioli-modal-container")) {
    document.getElementById("ravioli-modal-container").style.display = "none";
  }

  // or hide banner (if there's a banner)
  if (document.getElementById("ravioli-banner")) {
    document.getElementById("ravioli-banner").style.display = "none";
  }

  // or hide full banner (if there's a full banner)
  if (document.getElementById("ravioli-banner-full")) {
    document.getElementById("ravioli-banner-full").style.display = "none";
  }
  
}

function addRavioliFromDisplay(e) {
  if (e.target.id === "ravioli-button-yes") {
    e.preventDefault();
    updateHiddenFieldAndRefresh("true");
    //localStorage.setItem("ravioliDecision", "yes");
    localStorage.setItem("ravioliAdded", "yes")
    closeRavioliDisplay();
  }
  
}

function removeRavioliFromDisplay(e) {
  if (e.target.id === "ravioli-button-no") {
    e.preventDefault();
    updateHiddenFieldAndRefresh("false");
    closeRavioliDisplay();
  }
}

function updateHiddenFieldAndRefresh(fieldValue) {
  const ravioliField = document.getElementById("ravioli-add_ravioli_field")
  if (ravioliField) {
    ravioliField.value = fieldValue;
    document.body.dispatchEvent(new Event("update_checkout"));
  }
}

function ravioliSprinkleDisplay() {
  // add event listeners to yes button
  document
    .getElementById("ravioli-button-yes")
    ?.addEventListener("click", addRavioliFromDisplay);

  // add event listener to no button
  document
    .getElementById("ravioli-button-no")
    ?.addEventListener("click", removeRavioliFromDisplay);
}

// remove button in checkout view
function removeRavioliFromCheckout(e) {
  if (e.target.id === "ravioli-remove-checkout") {
    e.preventDefault();
    updateHiddenFieldAndRefresh(false);
  }
}

const showMore = (e) => {
  // show full banner if more button is clicked
  if (e.target.id === "ravioli-button-more") {
    e.preventDefault();
    document.getElementById("ravioli-banner-inner-container").replaceChildren(...document.getElementById("ravioli-banner-full-inner-container").children);
    
    // if full banner is overflowing, align on top (useful for smaller phone screens)
    const ravioliBannerEl = document.getElementById("ravioli-banner")
    if (ravioliBannerEl.scrollHeight > (window.innerHeight || document.documentElement.clientHeight)) {
      ravioliBannerEl.classList.add("ravioli-top-0");
    }
  }
}

const hideBanner = (e) => {
  // hide ravioli banner if close button is clicked
  if (e.target.parentElement.id === "ravioli-banner-close-container") {
    e.preventDefault();
    document.getElementById("ravioli-banner").style.display = "none";
  }
}