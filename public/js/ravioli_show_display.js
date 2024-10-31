const DEBOUNCE_DELAY = 3000;

const ALL_BILLING_FIELDS = [
  "billing_first_name",
  "billing_last_name",
  "billing_country",
  "billing_address_1",
  "billing_city",
  "billing_postcode",
  "billing_email"
];

const ALL_SHIPPING_FIELDS = [
  "shipping_first_name",
  "shipping_last_name",
  "shipping_country",
  "shipping_address_1",
  "shipping_city",
  "shipping_postcode",
];

const SERVED_ZIP_CODES = [
  "20099",
  "22767"
];

window.addEventListener("load", () => {
  initFields();

  // when page loads, it's possible that the details are already filled in, so we can show the modal

  // for now, we show the modal on page load for all zip codes
  //debounce(decideToshowRavioliDisplay, DEBOUNCE_DELAY)();
  if (localStorage.getItem("ravioliAdded") !== "yes") showRavioliDisplay();
});

initFields = () => {
  // for now, we show the modal on page load for all zip codes

  // loop through all fields and add event listener
  
  // const allFields = ALL_BILLING_FIELDS.concat(ALL_SHIPPING_FIELDS);
  // allFields.forEach((field) => {
  //   document.getElementById(field)?.addEventListener("input", debounce(inputHandler, DEBOUNCE_DELAY));
  // });

  // document.getElementById("ship-to-different-address-checkbox")?.addEventListener("input", debounce(inputHandler, DEBOUNCE_DELAY));
};

const inputHandler = (_e) => {
  // for now, we show the modal on page load for all zip codes
  // decideToshowRavioliDisplay();
};

const decideToshowRavioliDisplay = () => {  
  // in this case, billing address is also the shipping address
  if (!shipToDifferentAddress() && allBillingFieldsFilledOut() && zipCodeBillingInArea()) {
    if (localStorage.getItem("ravioliAdded") !== "yes") showRavioliDisplay();
    return;
  }

  // in this case, the billing and shipping addresses are different
  if (shipToDifferentAddress() && allFieldsFilledOut() && zipCodeShippingInArea()) {
    if (localStorage.getItem("ravioliAdded") !== "yes") showRavioliDisplay();
    return;
  }

  if (localStorage.getItem("ravioliAdded") === "yes") {
    // remove ravioli from cart if it had been added before and new zip code is not in area anymore
    updateHiddenFieldAndRefresh("false");
    // localStorage.setItem("ravioliDecision", "no");
    // localStorage.removeItem("ravioliDisplayShown");
    localStorage.removeItem("ravioliAdded");
  }
}

const allBillingFieldsFilledOut = () => {
  // check if all billing fields are filled out
  return ALL_BILLING_FIELDS.every((field) => {
    return document.getElementById(field)?.value;
  });
};

const allFieldsFilledOut = () => {
  // check if all fields are filled out
  // merge billing and shipping fields to check for both
  const allFields = ALL_BILLING_FIELDS.concat(ALL_SHIPPING_FIELDS);
  return allFields.every((field) => {
    return document.getElementById(field)?.value;
  });
};

const zipCodeBillingInArea = () => {
  // check if zip code is in our served area
  const zip_code = document.getElementById("billing_postcode").value; 
  return SERVED_ZIP_CODES.includes(zip_code);
};

const zipCodeShippingInArea = () => {
  // check if zip code is in our served area
  const zip_code = document.getElementById("shipping_postcode").value; 
  return SERVED_ZIP_CODES.includes(zip_code);
};

const shipToDifferentAddress = () => {
  return document.getElementById("ship-to-different-address-checkbox")?.checked;
};

const showRavioliDisplay = () => {
  // only show display if not already shown
  //if (localStorage.getItem("ravioliDisplayShown") === "true") return;

  // set localstorage value so that we know not to show ravioli display again
  //localStorage.setItem("ravioliDisplayShown", "true");

  // show modal (if modal option chosen)
  if (document.getElementById("ravioli-modal-container")) {
    document.getElementById("ravioli-modal-container").style.display = "block";
    return;
  }

  // show minimal banner (if banner option chosen and on mobile)
  if (document.getElementById("ravioli-banner") && window.innerWidth <= 768) {
    
    document.getElementById("ravioli-banner").style.display = "block";
    return;
  }

  // show full banner (if banner option chosen and on desktop)
  if (document.getElementById("ravioli-banner-full") && window.innerWidth > 768) {
    document.getElementById("ravioli-banner-full").style.display = "block";
  }
};

const debounce = (callback, delay) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      callback(...args);
    }, delay);
  };
}