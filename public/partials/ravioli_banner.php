<div class="ravioli-banner" id="ravioli-banner">
  <div class="ravioli-banner-inner-container" id="ravioli-banner-inner-container">
    <div class="ravioli-banner-text-container">
      <p class="ravioli-text">
        <?php
            echo trim(
              str_replace(
                '[PREIS]',
                wc_price(esc_html(trim(get_option( 'ravioli_settings_tab_fee' )))),
                esc_html(get_option( 'ravioli_settings_tab_teaser_text'))
              )
            )
        ?>
      </p>
    </div>
    <div class="ravioli-button-container">
      <div class="ravioli-yes-no-buttons-container">
        <button class="ravioli-button ravioli-button-yes" id="ravioli-button-yes">Ja</button>
        <button class="ravioli-button ravioli-button-no" id="ravioli-button-no">Nein</button>
      </div>
      <button class="ravioli-button-secondary ravioli-button-more" id="ravioli-button-more">Mehr erfahren</button>
    </div>
  </div>
</div>