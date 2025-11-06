<?php

$level_mode = \AgileStoreLocator\Helper::expertise_level();

//  simple level
if($level_mode == '1'): ?>
<style type="text/css">
.sl-complx {
    display: none;
}
</style>
<?php endif; ?>
<div class="asl-p-cont asl-new-bg">
    <div class="hide">
        <svg xmlns="http://www.w3.org/2000/svg">
            <symbol id="i-trash" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Trash','asl_locator') ?></title>
                <path
                    d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
            </symbol>
            <symbol id="i-clock" viewBox="0 0 32 32" width="20" height="18" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <circle cx="16" cy="16" r="14" />
                <path d="M16 8 L16 16 20 20" />
            </symbol>
            <symbol id="i-plus" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Add','asl_locator') ?></title>
                <path d="M16 2 L16 30 M2 16 L30 16" />
            </symbol>
            <symbol id="i-chevron-top" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M30 20 L16 8 2 20" />
            </symbol>
            <symbol id="i-chevron-bottom" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M30 12 L16 24 2 12" />
            </symbol>
            <symbol id="i-geo-location" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-1">
                <path
                    d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                </path>
                <circle cx="12" cy="10" r="3"></circle>
            </symbol>

            <symbol id="i-gear" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                <path
                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
            </symbol>
            <symbol id="i-globe" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z" />
            </symbol>
            <symbol id="i-upload" viewBox="0 0 32 32" width="25" height="25" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" x2="12" y1="15" y2="3"></line>
            </symbol>
            <symbol id="i-phone" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                <!-- <path
                    d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" /> -->
            </symbol>
            <symbol id="i-email" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
            </symbol>
            <symbol id="i-store" viewBox="0 0 64 64" stroke-width="3" stroke="currentColor" fill="none">
                <path d="M52,27.18V52.76a2.92,2.92,0,0,1-3,2.84H15a2.92,2.92,0,0,1-3-2.84V27.17" />
                <polyline points="26.26 55.52 26.26 38.45 37.84 38.45 37.84 55.52" />
                <path
                    d="M8.44,19.18s-1.1,7.76,6.45,8.94a7.17,7.17,0,0,0,6.1-2A7.43,7.43,0,0,0,32,26a7.4,7.4,0,0,0,5,2.49,11.82,11.82,0,0,0,5.9-2.15,6.66,6.66,0,0,0,4.67,2.15,8,8,0,0,0,7.93-9.3L50.78,9.05a1,1,0,0,0-.94-.65H14a1,1,0,0,0-.94.66Z" />
                <line x1="8.44" y1="19.18" x2="55.54" y2="19.18" />
                <line x1="21.04" y1="19.18" x2="21.04" y2="8.4" />
                <line x1="32.05" y1="19.18" x2="32.05" y2="8.4" />
                <line x1="43.01" y1="19.18" x2="43.01" y2="8.4" />
            </symbol>
              <symbol xmlns="i-google" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z" />
            </symbol>
            <!-- <symbol id="i-info" fill="currentColor" viewBox="0 0 16 16">
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
  <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
</symbol> -->
<symbol id="i-info"  viewBox="0 0 19 19" fill="none"><g  stroke-width="0"></g><g stroke-linecap="round" stroke-linejoin="round"></g><g > <path fill="currentColor" fill-rule="evenodd" d="M10 3a7 7 0 100 14 7 7 0 000-14zm-9 7a9 9 0 1118 0 9 9 0 01-18 0zm8-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm.01 8a1 1 0 102 0V9a1 1 0 10-2 0v5z"></path> </g></symbol>
        </svg>
    </div>
    <div class="container sl-add-store-page">
        <div class="row asl-inner-cont">
            <div class="col-md-12">
                <div class="card p-0 mb-4">
                    <div class="card-title"><div>
                            <h3>
                                <?php echo esc_attr__('Create New Store','asl_locator') ?><?php echo \AgileStoreLocator\Helper::getLangControl(); ?>
                            </h3>
                            <p class="card-text">
                                <?php echo esc_attr__('Fill in the details to add a new store to your network','asl_locator') ?>
                            </p>
                        </div>
                        <a target="_blank" class="btn btn-outline-light"
                            href="https://www.youtube.com/watch?v=PmHCtZIP-KE"><?php echo esc_attr__('Guide', 'asl-wc') ?>
                            <i ><svg style="margin-bottom:2px;" width="15" height="15">
                                    <use xlink:href="#i-info"></use>
                                </svg></i></a>
                    </div>
                    <div class="card-body p-0">
                        <form id="frm-addstore">
                            <div class="row">
                                <div class="col-12 asl-tabs">
                                    <div class="asl-tabs asl-store-tabs p-0">
                                        <div class="asl-tabs-body">
                                            <ul class="nav nav-pills flex-column">
                                                <li class="active "><a data-toggle="pill" href="#sl-store-address">
                                                        <i class="mr-2"><svg width="22" height="22">
                                                                <use xlink:href="#i-geo-location"></use>
                                                            </svg></i>
                                                        <div>
                                                            <h6><?php echo esc_attr__('Store Address','asl_locator') ?>
                                                            </h6>
                                                            <p class>
                                                                <?php echo esc_attr__('Location & Contact Info','asl_locator') ?>
                                                            </p>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class=""><a data-toggle="pill" href="#sl-other-details"><i
                                                            class="mr-2"><svg width="24" height="24">
                                                                <use xlink:href="#i-gear"></use>
                                                            </svg></i>
                                                        <div>
                                                            <h6><?php echo esc_attr__('Other Details','asl_locator') ?>
                                                            </h6>
                                                            <p class>
                                                                <?php echo esc_attr__('Settings & Configuration','asl_locator') ?>
                                                            </p>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li class=""><a data-toggle="pill" href="#sl-stores-timings">
                                                        <i class="mr-2"><svg width="20" height="20">
                                                                <use xlink:href="#i-clock"></use>
                                                            </svg></i>
                                                        <div>
                                                            <h6><?php echo esc_attr__('Store Timing','asl_locator') ?>
                                                            </h6>
                                                            <p class>
                                                                <?php echo esc_attr__('Operating Hours','asl_locator') ?>
                                                            </p>
                                                        </div>
                                                    </a>
                                                </li>
                                                <?php if(class_exists('ASL_WC_Instance')): ?>
                                                <li class=""><a data-toggle="pill" href="#sl-woocommerce"><i
                                                            class="mr-2"><svg width="20" height="20">
                                                                <use xlink:href="#i-store"></use>
                                                            </svg></i>
                                                        <div>
                                                            <h6><?php echo esc_attr__('WooCommerce','asl_locator') ?>
                                                            </h6>
                                                            <p class>
                                                                <?php echo esc_attr__('Shipping Methods','asl_locator') ?>
                                                            </p>
                                                        </div>
                                                    </a>
                                                </li>
                                                <?php endif; ?>

                                                <?php if(class_exists('ASL_GRR_Instance')): ?>
                                                <li class=""><a data-toggle="pill"
                                                        href="#sl-grr"><?php echo esc_attr__('Google Place ID','asl_locator') ?></a>
                                                </li>
                                                <?php endif ?>

                                            </ul>
                                            <div class="tab-content">
                                                <div id="sl-store-address" class="tab-pane in active p-0 mb-5">
                                                    <div class="row">
                                                        <div class="col-12 mb-4">
                                                            <h2 class="asl-tab-content-title">
                                                                <?php echo esc_attr__('Store Address & Contact','asl_locator') ?>
                                                            </h2>
                                                            <p class="asl-tab-content-text">
                                                                <?php echo esc_attr__('Enter the store location and contact information','asl_locator') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_title"><?php echo esc_attr__('Title','asl_locator') ?></label>
                                                            <input type="text" id="txt_title" name="data[title]"
                                                                class="form-control validate[required]">
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label for="txt_website">
                                                                <i class="me-1">
                                                                    <svg width="18" height="20">
                                                                        <use xlink:href="#i-globe"></use>
                                                                    </svg></i>
                                                                <?php echo esc_attr__('Website','asl_locator') ?></label>
                                                            <input type="text" id="txt_website" name="data[website]"
                                                                placeholder="http://example.com" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_description"><?php echo esc_attr__('Description','asl_locator') ?></label>
                                                            <textarea id="txt_description" name="data[description]"
                                                                rows="3"
                                                                placeholder="<?php echo esc_attr__('Enter Description','asl_locator') ?>"
                                                                class="input-medium form-control"></textarea>
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_description_2"><?php echo esc_attr__('Additional Description','asl_locator') ?></label>
                                                            <textarea id="txt_description_2" name="data[description_2]"
                                                                rows="3"
                                                                placeholder="<?php echo esc_attr__('Additional Description','asl_locator') ?>"
                                                                class="input-medium form-control"></textarea>
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label for="txt_phone"> <i class="me-1"><svg width="18"
                                                                        height="20">
                                                                        <use xlink:href="#i-phone"></use>
                                                                    </svg></i><?php echo esc_attr__('Phone','asl_locator') ?></label>
                                                            <input type="text" id="txt_phone" name="data[phone]"
                                                                class="form-control">

                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_fax"><?php echo esc_attr__('Fax','asl_locator') ?></label>
                                                            <input type="text" id="txt_fax" name="data[fax]"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 form-group mb-4">
                                                            <label for="txt_email"><i class="me-1">
                                                                    <svg width="18" height="19">
                                                                        <use xlink:href="#i-email"></use>
                                                                    </svg></i><?php echo esc_attr__('Email','asl_locator') ?></label>
                                                            <input type="text" id="txt_email" name="data[email]"
                                                                class="form-control validate[custom[email]]">
                                                        </div>
                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_street"><?php echo esc_attr__('Street','asl_locator') ?></label>
                                                            <input type="text" id="txt_street" name="data[street]"
                                                                class="form-control">
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_city"><?php echo esc_attr__('City','asl_locator') ?></label>
                                                            <input type="text" id="txt_city" name="data[city]"
                                                                class="form-control validate[required]">
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_state"><?php echo esc_attr__('State','asl_locator') ?></label>
                                                            <input type="text" id="txt_state" name="data[state]"
                                                                class="form-control">
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_postal_code"><?php echo esc_attr__('Postal Code','asl_locator') ?></label>
                                                            <input type="text" id="txt_postal_code"
                                                                name="data[postal_code]" class="form-control">
                                                        </div>

                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="txt_country"><?php echo esc_attr__('Country','asl_locator') ?></label>
                                                            <select id="txt_country" style="width:100%"
                                                                name="data[country]"
                                                                class="custom-select validate[required]">
                                                                <option value="">
                                                                    <?php echo esc_attr__('Select Country','asl_locator') ?>
                                                                </option>
                                                                <?php foreach($countries as $country): ?>
                                                                <option value="<?php echo esc_attr($country->id) ?>">
                                                                    <?php echo esc_attr($country->country) ?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div id="map_canvas" class="map_canvas"></div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group mb-3">
                                                                                <label
                                                                                    for="asl_txt_lat"><?php echo esc_attr__('Latitude','asl_locator') ?></label>
                                                                                <input type="text" id="asl_txt_lat"
                                                                                    name="data[lat]" value="0.0"
                                                                                    readonly="true"
                                                                                    class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group mb-3">
                                                                                <label
                                                                                    for="asl_txt_lng"><?php echo esc_attr__('Longitude','asl_locator') ?></label>
                                                                                <input type="text" id="asl_txt_lng"
                                                                                    name="data[lng]" value="0.0"
                                                                                    readonly="true"
                                                                                    class="form-control">
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group">
                                                                        <a id="lnk-edit-coord"
                                                                            class="btn py-2 px-3 btn-warning"><?php echo esc_attr__('Change Coordinates','asl_locator') ?></a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="dump-message"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="sl-other-details" class="tab-pane p-0 mb-4">
                                                    <div class="row">
                                                        <div class="col-12 mb-4">
                                                            <h2 class="asl-tab-content-title">
                                                                <?php echo esc_attr__('Additional Configuration','asl_locator') ?>
                                                            </h2>
                                                            <p class="asl-tab-content-text">
                                                                <?php echo esc_attr__('Configure markers, categories, and other store settings','asl_locator') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 form-group mb-4 ">
                                                            <div class="form-group asl-locked-box">
                                                                <label
                                                                    for="ddl-asl-markers"><?php echo esc_attr__('Marker','asl_locator') ?></label>
                                                                <div class="input-group">
                                                                    <select id="ddl-asl-markers">
                                                                        <?php foreach($markers as $m):?>
                                                                        <option value="<?php echo esc_attr($m->id) ?>"
                                                                            data-imagesrc="<?php echo ASL_UPLOAD_URL.'icon/'.$m->icon;?>"
                                                                            data-description="&nbsp;">
                                                                            <?php echo esc_attr($m->marker_name);?>
                                                                        </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <button type="button"
                                                                        class="btn btn-success text-white rounded px-4 sl-marker-btn-newstyle"
                                                                        data-bs-toggle="smodal"
                                                                        data-bs-target="#addmarkermodel"> <i
                                                                            class="me-2"><svg width="22" height="22">
                                                                                <use xlink:href="#i-upload"></use>
                                                                            </svg></i><?php echo esc_attr__('New Marker','asl_locator') ?></button>
                                                                </div>
                                                                <div class="asl-locked-inner">
                                                                    <h6><?php echo esc_attr__('Upgrade Plugin','asl_locator') ?>
                                                                    </h6>
                                                                    <a target="_blank"
                                                                        href="https://codecanyon.net/item/agile-store-locator-google-maps-for-wordpress/16973546"><?php echo esc_attr__('Lifetime License - $59','asl_locator') ?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 form-group mb-4  ">
                                                            <div >
                                                                <label
                                                                    for="ddl-asl-logos"><?php echo esc_attr__('Logo','asl_locator') ?></label>
                                                                <div class="input-group">
                                                                    <div id="ddl-asl-logos"></div>
                                                                    <button type="button"
                                                                        class="btn btn-success text-white rounded px-4 sl-logo-btn-newstyle"
                                                                        data-bs-toggle="smodal"
                                                                        data-bs-target="#addimagemodel">
                                                                        <i class="me-2"><svg width="22" height="22">
                                                                                <use xlink:href="#i-upload"></use>
                                                                            </svg></i><?php echo esc_attr__('New Logo','asl_locator') ?></button>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 form-group mb-4">
                                                            <label
                                                                for="ddl_categories"><?php echo esc_attr__('Category','asl_locator') ?></label>
                                                            <select name="ddl_categories" id="ddl_categories" multiple
                                                                class="chosen-select-width form-control">
                                                                <?php foreach($category as $catego): ?>
                                                                <?php if ($catego->parent_id) continue; ?>
                                                                <option value="<?php echo esc_attr($catego->id) ?>">
                                                                    <?php echo esc_attr($catego->category_name) ?>
                                                                </option>
                                                                <?php foreach($category as $sub_catego): ?>
                                                                <?php if ($catego->id != $sub_catego->parent_id) continue; ?>
                                                                <option value="<?php echo esc_attr($sub_catego->id) ?>">
                                                                    <?php echo esc_attr($catego->category_name) ?> >
                                                                    <?php echo esc_attr($sub_catego->category_name) ?>
                                                                </option>
                                                                <?php endforeach ?>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </div>
                                                        <?php

                                  //  Get all control
                                  $ddl_controls = \AgileStoreLocator\Model\Attribute::get_controls();

                                  foreach($ddl_controls as $control_key => $control) {

                                    //  Get control values
                                    $ddl_values = \AgileStoreLocator\Model\Attribute::get_list($control_key, $lang);
                                ?>
                                                        <div class="col-md-6 sl-complx form-group mb-3 d-none">
                                                            <div class="form-group sl-chosen">
                                                                <label
                                                                    for="ddl_<?php echo esc_attr($control_key) ?>"><?php echo esc_attr($control['label'], 'asl_locator') ?></label>
                                                                <select
                                                                    data-ph="<?php echo esc_attr($control['label'], 'asl_locator') ?>"
                                                                    name="data[<?php echo esc_attr($control['field']) ?>]"
                                                                    id="ddl_<?php echo esc_attr($control_key) ?>"
                                                                    multiple
                                                                    class="asl-chosen chosen-select-width form-control">
                                                                    <?php foreach($ddl_values as $ddl_item): ?>
                                                                    <option
                                                                        value="<?php echo esc_attr($ddl_item->id) ?>">
                                                                        <?php echo esc_attr($ddl_item->name) ?></option>
                                                                    <?php endforeach ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <?php
                                }
                                ?>
                                                        <div class="col-md-6 sl-complx form-group mb-3">
                                                            <label
                                                                for="txt-ordering"><?php echo esc_attr__('Priority Order','asl_locator') ?></label>
                                                            <input type="number" id="txt-ordering" name="data[ordr]"
                                                                placeholder="0" class="form-control validate[integer]">
                                                            <small
                                                                class="form-text text-muted"><?php echo esc_attr__('Descending Order for the list, higher number on top.','asl_locator') ?></small>
                                                        </div>
                                                        <?php 

                                // Organize fields into sections based on their types
                                foreach ($fields as $fieldName => $fieldData) {
                                    
                                  $field = new \AgileStoreLocator\Form\CustomField($fieldData);

                                  echo '<div class="col-md-6 form-group mb-4">';
                                  echo $field->render('asl-custom');
                                  echo '</div>'; 
                                }
                                ?>
                                                        <div class="col-md-6 form-group mb-4 align-items-center">
                                                            <label
                                                                for="sl-disabled"><?php echo esc_attr__('Disabled','asl_locator') ?></label>
                                                            <div class="a-swith a-swith-alone">
                                                                <input id="sl-disabled" name="data[is_disabled]"
                                                                    class="cmn-toggle cmn-toggle-round" type="checkbox">
                                                                <label for="sl-disabled"></label>
                                                                <span><?php echo esc_attr__('No','asl_locator') ?></span>
                                                                <span><?php echo esc_attr__('Yes','asl_locator') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="sl-stores-timings" class="tab-pane p-0 mb-4">
                                                    <div class="row">
                                                        <div class="col-12 mb-4">
                                                            <h2 class="asl-tab-content-title">
                                                                <?php echo esc_attr__('Operating Hours','asl_locator') ?>
                                                            </h2>
                                                            <p class="asl-tab-content-text">
                                                                <?php echo esc_attr__('Set the stores opening and closing times for each day','asl_locator') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div
                                                                class="d-md-flex justify-content-between align-items-center">
                                                                <h3><?php echo esc_attr__('Store Schedule','asl_locator') ?>
                                                                </h3>
                                                                <a id="asl-time-cp" class="btn "
                                                                    title="<?php echo esc_attr__('Copy/Paste Monday Timing','asl_locator') ?>"><i
                                                                        class="me-2"><svg width="16" height="17">
                                                                            <use xlink:href="#i-plus">
                                                                            </use>
                                                                        </svg></i><?php echo esc_attr__('Same Everyday','asl_locator') ?></a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="table-responsive">
                                                                <table class="table  asl-time-details">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td colspan="1"><span
                                                                                    class="lbl-day"><?php echo esc_attr__('Monday','asl_locator') ?></span>
                                                                            </td>
                                                                            <td colspan="3">
                                                                                <div class="asl-all-day-times"
                                                                                    data-day="mon">

                                                                                    <div class="form-group">
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="9:30 AM"
                                                                                                class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]"
                                                                                                placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="6:30 PM"
                                                                                                class="form-control asl-end-time asltimepicker validate[required]"
                                                                                                placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <span
                                                                                            class="add-k-delete glyp-trash text-danger">
                                                                                            <svg width="16" height="16">
                                                                                                <use
                                                                                                    xlink:href="#i-trash">
                                                                                                </use>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="asl-closed-lbl">
                                                                                        <div class="a-swith">
                                                                                            <input id="cmn-toggle-0"
                                                                                                class="cmn-toggle cmn-toggle-round"
                                                                                                type="checkbox"
                                                                                                checked="checked">
                                                                                            <label
                                                                                                for="cmn-toggle-0"></label>
                                                                                            <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                                                            <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="add-k-add glyp-add float-end text-primary">
                                                                                    <svg width="16" height="16">
                                                                                        <use xlink:href="#i-plus">
                                                                                        </use>
                                                                                    </svg>
                                                                                </span>
                                                                            </td>

                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1"><span
                                                                                    class="lbl-day"><?php echo esc_attr__('Tuesday','asl_locator') ?></span>
                                                                            </td>
                                                                            <td colspan="3">
                                                                                <div class="asl-all-day-times"
                                                                                    data-day="tue">
                                                                                    <div class="form-group">
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="9:30 AM"
                                                                                                class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]"
                                                                                                placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="6:30 PM"
                                                                                                class="form-control asl-end-time asltimepicker validate[required]"
                                                                                                placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <span
                                                                                            class="add-k-delete glyp-trash text-danger">
                                                                                            <svg width="16" height="16">
                                                                                                <use
                                                                                                    xlink:href="#i-trash">
                                                                                                </use>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="asl-closed-lbl">
                                                                                        <div class="a-swith">
                                                                                            <input id="cmn-toggle-1"
                                                                                                class="cmn-toggle cmn-toggle-round"
                                                                                                type="checkbox"
                                                                                                checked="checked">
                                                                                            <label
                                                                                                for="cmn-toggle-1"></label>
                                                                                            <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                                                            <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="add-k-add glyp-add float-end text-primary">
                                                                                    <svg width="16" height="16">
                                                                                        <use xlink:href="#i-plus">
                                                                                        </use>
                                                                                    </svg>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1"><span
                                                                                    class="lbl-day"><?php echo esc_attr__('Wednesday','asl_locator') ?></span>
                                                                            </td>
                                                                            <td colspan="3">
                                                                                <div class="asl-all-day-times"
                                                                                    data-day="wed">
                                                                                    <div class="form-group">
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="9:30 AM"
                                                                                                class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]"
                                                                                                placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="6:30 PM"
                                                                                                class="form-control asl-end-time asltimepicker validate[required]"
                                                                                                placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <span
                                                                                            class="add-k-delete glyp-trash text-danger">
                                                                                            <svg width="16" height="16">
                                                                                                <use
                                                                                                    xlink:href="#i-trash">
                                                                                                </use>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="asl-closed-lbl">
                                                                                        <div class="a-swith">
                                                                                            <input id="cmn-toggle-2"
                                                                                                class="cmn-toggle cmn-toggle-round"
                                                                                                type="checkbox"
                                                                                                checked="checked">
                                                                                            <label
                                                                                                for="cmn-toggle-2"></label>
                                                                                            <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                                                            <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="add-k-add glyp-add float-end text-primary">
                                                                                    <svg width="16" height="16">
                                                                                        <use xlink:href="#i-plus">
                                                                                        </use>
                                                                                    </svg>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1"><span
                                                                                    class="lbl-day"><?php echo esc_attr__('Thursday','asl_locator') ?></span>
                                                                            </td>
                                                                            <td colspan="3">
                                                                                <div class="asl-all-day-times"
                                                                                    data-day="thu">
                                                                                    <div class="form-group">
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="9:30 AM"
                                                                                                class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]"
                                                                                                placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="6:30 PM"
                                                                                                class="form-control asl-end-time asltimepicker validate[required]"
                                                                                                placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <span
                                                                                            class="add-k-delete glyp-trash text-danger">
                                                                                            <svg width="16" height="16">
                                                                                                <use
                                                                                                    xlink:href="#i-trash">
                                                                                                </use>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </div>

                                                                                    <div class="asl-closed-lbl">
                                                                                        <div class="a-swith">
                                                                                            <input id="cmn-toggle-3"
                                                                                                class="cmn-toggle cmn-toggle-round"
                                                                                                type="checkbox"
                                                                                                checked="checked">
                                                                                            <label
                                                                                                for="cmn-toggle-3"></label>
                                                                                            <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                                                            <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="add-k-add glyp-add float-end text-primary">
                                                                                    <svg width="16" height="16">
                                                                                        <use xlink:href="#i-plus">
                                                                                        </use>
                                                                                    </svg>
                                                                                </span>
                                                                            </td>

                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1"><span
                                                                                    class="lbl-day"><?php echo esc_attr__('Friday','asl_locator') ?></span>
                                                                            </td>
                                                                            <td colspan="3">
                                                                                <div class="asl-all-day-times"
                                                                                    data-day="fri">
                                                                                    <div class="form-group">
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="9:30 AM"
                                                                                                class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]"
                                                                                                placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="6:30 PM"
                                                                                                class="form-control asl-end-time asltimepicker validate[required]"
                                                                                                placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <span
                                                                                            class="add-k-delete glyp-trash text-danger">
                                                                                            <svg width="16" height="16">
                                                                                                <use
                                                                                                    xlink:href="#i-trash">
                                                                                                </use>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="asl-closed-lbl">
                                                                                        <div class="a-swith">
                                                                                            <input id="cmn-toggle-4"
                                                                                                class="cmn-toggle cmn-toggle-round"
                                                                                                type="checkbox"
                                                                                                checked="checked">
                                                                                            <label
                                                                                                for="cmn-toggle-4"></label>
                                                                                            <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                                                            <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="add-k-add glyp-add float-end text-primary">
                                                                                    <svg width="16" height="16">
                                                                                        <use xlink:href="#i-plus">
                                                                                        </use>
                                                                                    </svg>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1"><span
                                                                                    class="lbl-day"><?php echo esc_attr__('Saturday','asl_locator') ?></span>
                                                                            </td>
                                                                            <td colspan="3">
                                                                                <div class="asl-all-day-times"
                                                                                    data-day="sat">
                                                                                    <div class="form-group">
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="9:30 AM"
                                                                                                class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]"
                                                                                                placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="6:30 PM"
                                                                                                class="form-control asl-end-time asltimepicker validate[required]"
                                                                                                placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <span
                                                                                            class="add-k-delete glyp-trash text-danger">
                                                                                            <svg width="16" height="16">
                                                                                                <use
                                                                                                    xlink:href="#i-trash">
                                                                                                </use>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="asl-closed-lbl">
                                                                                        <div class="a-swith">
                                                                                            <input id="cmn-toggle-5"
                                                                                                class="cmn-toggle cmn-toggle-round"
                                                                                                type="checkbox"
                                                                                                checked="checked">
                                                                                            <label
                                                                                                for="cmn-toggle-5"></label>
                                                                                            <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                                                            <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="add-k-add glyp-add float-end text-primary">
                                                                                    <svg width="16" height="16">
                                                                                        <use xlink:href="#i-plus">
                                                                                        </use>
                                                                                    </svg>
                                                                                </span>
                                                                            </td>

                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="1"><span
                                                                                    class="lbl-day"><?php echo esc_attr__('Sunday','asl_locator') ?></span>
                                                                            </td>
                                                                            <td colspan="3">
                                                                                <div class="asl-all-day-times"
                                                                                    data-day="sun">
                                                                                    <div class="form-group">
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="9:30 AM"
                                                                                                class="form-control asl-start-time asltimepicker validate[required,funcCall[ASLmatchTime]]"
                                                                                                placeholder="<?php echo esc_attr__('Start Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <div
                                                                                            class="input-group bootstrap-asltimepicker">
                                                                                            <input type="text"
                                                                                                value="6:30 PM"
                                                                                                class="form-control asl-end-time asltimepicker validate[required]"
                                                                                                placeholder="<?php echo esc_attr__('End Time','asl_locator') ?>">
                                                                                            <span
                                                                                                class="input-group-append add-on"><span
                                                                                                    class="input-group-text"><svg
                                                                                                        width="20"
                                                                                                        height="18">
                                                                                                        <use
                                                                                                            xlink:href="#i-clock">
                                                                                                        </use>
                                                                                                    </svg></span></span>
                                                                                        </div>
                                                                                        <span
                                                                                            class="add-k-delete glyp-trash text-danger">
                                                                                            <svg width="16" height="16">
                                                                                                <use
                                                                                                    xlink:href="#i-trash">
                                                                                                </use>
                                                                                            </svg>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div class="asl-closed-lbl">
                                                                                        <div class="a-swith">
                                                                                            <input id="cmn-toggle-6"
                                                                                                class="cmn-toggle cmn-toggle-round"
                                                                                                type="checkbox"
                                                                                                checked="checked">
                                                                                            <label
                                                                                                for="cmn-toggle-6"></label>
                                                                                            <span><?php echo esc_attr__('Closed','asl_locator') ?></span>
                                                                                            <span><?php echo esc_attr__('Open 24 Hour','asl_locator') ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span
                                                                                    class="add-k-add glyp-add float-end text-primary">
                                                                                    <svg width="16" height="16">
                                                                                        <use xlink:href="#i-plus">
                                                                                        </use>
                                                                                    </svg>
                                                                                </span>
                                                                            </td>

                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if( class_exists('ASL_WC_Instance') && defined('ASL_WC_PLUGIN') ): ?>
                                                <div id="sl-woocommerce" class="tab-pane p-0">
                                                    <?php ASLWC\Admin\StoreSetting::storeEditForm(null); ?>
                                                </div>
                                                <?php endif; ?>

                                                <?php if(class_exists('ASL_GRR_Instance')): ?>
                                                <div id="sl-grr" class="tab-pane p-0">
                                                    <div class="col-md-6 form-group mb-4">
                                                        <label
                                                            for="txt_placed_id"><?php echo esc_attr__('Google Placed ID','asl_locator') ?></label>
                                                        <input type="text" id="txt_placed_id" name="grr[placed_id]"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <?php endif ?>


                                                <div class="row border-top px-0 pb-0">
                                                    <div class="col-12 pe-0">
                                                        <button type="button"
                                                            class="float-right btn btn-success text-white me-0"
                                                            data-loading-text="<?php echo esc_attr__('Saving Store...','asl_locator') ?>"
                                                            data-completed-text="<?php echo esc_attr__('Store Saved','asl_locator') ?>"
                                                            id="btn-asl-add"><?php echo esc_attr__('Add Store','asl_locator') ?></button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modals	-->
    <div class="smodal fade" id="addimagemodel" role="dialog">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <form id="frm-upload-logo" name="frm-upload-logo">
                    <div class="smodal-header">
                        <h5 class="smodal-title"><?php echo esc_attr__('Upload Logo','asl_locator') ?></h5>
                        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="smodal-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="txt_logo-name"><?php echo esc_attr__('Name','asl_locator') ?></label>
                                <input type="text" id="txt_logo-name" name="data[logo_name]"
                                    placeholder="<?php echo esc_attr__('Logo Name','asl_locator') ?>"
                                    class="form-control">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <?php 
                                    $logo_meta = 'add_img';
                                    echo $this->asl_logo_uploader( $logo_meta,'' ); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="progress hideelement progress_bar_" style="display:none">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                        aria-valuemax="100" style="width:0%;">
                                        <span style="position:relative" class="sr-only">0% Complete</span>
                                    </div>
                                </div>
                            </div>
                            <ul></ul>
                            <div class="col-12">
                                <p id="message_upload" class="alert alert-warning hide"></p>
                            </div>
                        </div>
                    </div>

                    <div class="smodal-footer">
                        <button type="button"
                            data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"
                            class="btn new_upload_logo btn-success"><?php echo esc_attr__('Upload','asl_locator') ?></button>
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="smodal"><?php echo esc_attr__('Close','asl_locator') ?></button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <!-- Add Marker -->
    <div class="smodal fade" id="addmarkermodel" role="dialog">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <form id="frm-upload-marker" name="frm-upload-marker">
                    <div class="smodal-header">
                        <h5 class="smodal-title"><?php echo esc_attr__('Upload Marker','asl_locator') ?></h5>
                        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="smodal-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label
                                    for="txt_marker-name"><?php echo esc_attr__('Marker Name','asl_locator') ?></label>
                                <input type="text" id="txt_marker-name" name="data[marker_name]" class="form-control">
                            </div>
                            <div class="col-md-12 form-group mb-3" id="drop-zone-2">
                                <div class="input-group">
                                    <input name="files" type="file" class="form-control"
                                        accept=".jpg,.png,.jpeg,.gif,.JPG" id="file-logo-2">
                                    <span class="input-group-text"><?php echo esc_attr__('Icon','asl_locator') ?></span>
                                    <!-- <div class="custom-file">
                                        <div class="input-group-prepend">
                                        </div>
                                        <label class="custom-file-label"
                                            for="file-logo-2"><?php echo esc_attr__('File Path...','asl_locator') ?></label>
                                    </div> -->
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="progress hideelement progress_bar_" style="display:none">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                        aria-valuemax="100" style="width:0%;">
                                        <span style="position:relative" class="sr-only">0% Complete</span>
                                    </div>
                                </div>
                            </div>
                            <ul></ul>
                            <div class="col-12">
                                <p id="message_upload_1" class="alert alert-warning hide"></p>
                            </div>
                        </div>
                    </div>
                    <div class="smodal-footer">
                        <button type="button"
                            data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"
                            class="btn btn-start btn-primary"><?php echo esc_attr__('Upload','asl_locator') ?></button>
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="smodal"><?php echo esc_attr__('Close','asl_locator') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
var asl_configs = <?php echo json_encode($all_configs); ?>;
var ASL_Instance = {
    url: '<?php echo ASL_UPLOAD_URL ?>',
    plugin_url: '<?php echo ASL_URL_PATH; ?>'
};
var asl_logos = <?php echo json_encode($logos); ?>;

window.addEventListener("load", function() {
    asl_engine.pages.add_store();
    console.log(`File: add_store.php, Line: 1183`);
});
</script>