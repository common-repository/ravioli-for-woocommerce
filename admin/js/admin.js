window.addEventListener("load", () => {
  showModalImagePreview();
  addMediaUploaderClickListeners();
  addDisplayModeClickListener();
  hideBannerColorOptions();
  hideTeaserText();
});

const addMediaUploaderClickListeners = () => {
  document
    .getElementById("ravioli-modal-image-upload-button")
    ?.addEventListener("click", showMediaUploader);
  
  document
    .getElementById("ravioli-modal-image-reset")
    ?.addEventListener("click", resetModalImage);
};

const showMediaUploader = (event) => {
  event.preventDefault();

  mediaUploader = wp.media.frames.file_frame = wp.media({
    multiple: false
  });

  mediaUploader.on("select", mediaUploaderSelectHandler);

  mediaUploader.open();
}

const resetModalImage = (event) => {
  setPreviewImage(ravioli_data.modal_image_default_url);
};

const mediaUploaderSelectHandler = () => {
  // Get media attachment details from the frame state
  const attachment = mediaUploader.state().get('selection').first().toJSON();

  // update image elment to display newly selected image
  setPreviewImage(attachment.url);
};

const showModalImagePreview = () => {
 // injects the modal image preview and button into the WooCommerce settings page

 // create image element
 const imageInput = document.getElementById("ravioli_settings_tab_image");
 const imageInputParent = imageInput.parentElement;

 const imageEl = document.createElement("img");
 imageEl.src = imageInput.value;
 imageEl.classList.add("ravioli-modal-image-preview");
 imageEl.setAttribute("id", "ravioli-modal-image-preview")

  // create button element
  const buttonEl = document.createElement("button");
  buttonEl.classList.add("button-secondary");
  buttonEl.classList.add("ravioli-modal-image-preview-button");
  buttonEl.innerText = "Bild auswählen";
  buttonEl.setAttribute("id", "ravioli-modal-image-upload-button")

  // create anchor element to reset image to default image
  const anchorEl = document.createElement("a");
  anchorEl.classList.add("ravioli-modal-image-preview-reset");
  anchorEl.setAttribute("id", "ravioli-modal-image-reset");
  anchorEl.innerText = "Bild zurücksetzen";

  // add to dom
  imageInputParent.prepend(anchorEl);
  imageInputParent.prepend(buttonEl);
  imageInputParent.prepend(imageEl);
};

const setPreviewImage = (url) => {
  document.getElementById("ravioli_settings_tab_image").value = url;
  document.getElementById("ravioli-modal-image-preview").src = url;
};

const addDisplayModeClickListener = () => {
  document
    .getElementById("ravioli_settings_tab_display_mode")
    ?.addEventListener("change", displayModeChangeHandler);
};

const displayModeChangeHandler = (_e) => {
  hideBannerColorOptions();
  hideTeaserText();
}

const hideBannerColorOptions = () => {
  // hide more button color options if display mode is modal
  const displayMode = document.getElementById("ravioli_settings_tab_display_mode").value;

  const moreButtonFontColorRowEl = document.getElementById("ravioli_settings_tab_ravioli_more_button_font_color").parentElement.parentElement;
  const moreButtonHoverColorRowEl = document.getElementById("ravioli_settings_tab_ravioli_more_button_color_hover").parentElement.parentElement;
  
  if (displayMode === "popup") {
    moreButtonFontColorRowEl.style.display = "none";
    moreButtonHoverColorRowEl.style.display = "none";
  } else if (displayMode === "banner") {
    moreButtonFontColorRowEl.style.display = "table-row";
    moreButtonHoverColorRowEl.style.display = "table-row";
  }
}

const hideTeaserText = () => {
  // hide teaser text option if display mode is modal
  const displayMode = document.getElementById("ravioli_settings_tab_display_mode").value;

  const teaserTextRowEl = document.getElementById("ravioli_settings_tab_teaser_text").parentElement.parentElement;

  if (displayMode === "popup") {
    teaserTextRowEl.style.display = "none";
  } else if (displayMode === "banner") {
    teaserTextRowEl.style.display = "table-row";
  }
}