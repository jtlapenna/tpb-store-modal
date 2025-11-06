<?php

$level_mode = \AgileStoreLocator\Helper::expertise_level();

//  simple level
if($level_mode == '1'): ?>
<style type="text/css">
.sl-complx {
    display: none !important;
}
</style>
<?php endif; ?>
<!-- Container -->
<div class="asl-p-cont asl-new-bg">
    <div class="hide">
        <svg xmlns="http://www.w3.org/2000/svg">
            <symbol id="i-export" viewBox="0 0 32 32" width="18" height="18" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" x2="12" y1="3" y2="15"></line>
            </symbol>
            <symbol id="i-import" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M28 22 L28 30 4 30 4 22 M16 4 L16 24 M8 16 L16 24 24 16" />
            </symbol>
            <symbol id="i-trash" viewBox="0 0 32 32" width="16" height="16" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Trash', 'asl_locator') ?></title>
                <path
                    d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
            </symbol>
            <symbol id="i-edit" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Edit', 'asl_locator') ?></title>
                <path d="M30 7 L25 2 5 22 3 29 10 27 Z M21 6 L26 11 Z M5 22 L10 27 Z" />
            </symbol>
            <symbol id="i-info" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M16 14 L16 23 M16 8 L16 10" />
                <circle cx="16" cy="16" r="14" />
            </symbol>
            <symbol id="i-upload" viewBox="0 0 32 32" width="18" height="18" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" x2="12" y1="15" y2="3"></line>
            </symbol>
            <symbol id="i-desktop" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M10 29 C10 29 10 24 16 24 22 24 22 29 22 29 L10 29 Z M2 6 L2 23 30 23 30 6 2 6 Z" />
            </symbol>
            <symbol id="i-reload" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M29 16 C29 22 24 29 16 29 8 29 3 22 3 16 3 10 8 3 16 3 21 3 25 6 27 9 M20 10 L27 9 28 2" />
            </symbol>
            <symbol id="i-gear" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                <path
                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                <path
                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
            </symbol>
            <symbol id="i-copy" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                <path d="M10 9H8"></path>
                <path d="M16 13H8"></path>
                <path d="M16 17H8"></path>
            </symbol>
            <symbol id="i-alert" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" x2="12" y1="8" y2="12"></line>
                <line x1="12" x2="12.01" y1="16" y2="16"></line>
            </symbol>
            <symbol id="i-geo-location" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-1">
                <path
                    d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                </path>
                <circle cx="12" cy="10" r="3"></circle>
            </symbol>
            <symbol id="i-page" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                <path d="M10 9H8"></path>
                <path d="M16 13H8"></path>
                <path d="M16 17H8"></path>
            </symbol>

        </svg>
    </div>
    <div class="container sl-import-store-page">
        <div class="row asl-inner-cont">
            <div class="col-md-12  ">
                <!-- <div class="col-md-12 asl-lock-box"> -->
                <div class="card p-0 mb-4 ">
                    <div class="card-title">
                        <div>
                            <h3>
                                <?php echo esc_attr__('Import Stores (Pro Version)') ?>
                            </h3>
                            <p class="card-text">
                            <?php echo esc_attr__('Import many stores at once with CSV file with geocoding API.', 'asl_locator') ?>
                            </p>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="dump-message asl-dumper"></div>
                        <div id="message_complete"></div>

                        <?php
                        $import_dir = ASL_PLUGIN_PATH . 'public/import';
                        ?>

                        <!-- Server environment checks -->
                        <div class="row g-3 mb-3">
                            <?php if ( ! extension_loaded('mbstring') ) : ?>
                            <div class="col-12">
                                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-start"
                                    role="alert">
                                    <div class="me-2">
                                        <strong><?php echo esc_html__('Heads up:', 'asl_locator'); ?></strong>
                                        <?php echo esc_html__('The PHP mbstring extension is not installed. Import will not work without it. Please ask your server admin to enable mbstring (or enable it in your control panel).', 'asl_locator'); ?>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                        aria-label="<?php echo esc_attr__('Close', 'asl_locator'); ?>"></button>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ( ! is_writable( $import_dir ) ) : ?>
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <div class="mb-1">
                                        <strong><?php echo esc_html__('Directory not writable:', 'asl_locator'); ?></strong>
                                        <code><?php echo esc_html( $import_dir ); ?></code>
                                    </div>
                                    <div class="small">
                                        <?php echo esc_html__('Excel/CSV import will fail until this directory is writable by the web server.', 'asl_locator'); ?>
                                        <?php echo esc_html__('Update permissions (e.g., 755/775) or adjust ownership to allow write access.', 'asl_locator'); ?>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="<?php echo esc_attr__('Close', 'asl_locator'); ?>"></button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="asl-google-api-key asl-import-stores-box mb-4 rounded border p-4">
                                    <h4 class="asl-box-title">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="mb-1 text-primary">
                                            <path d="m15.5 7.5 2.3 2.3a1 1 0 0 0 1.4 0l2.1-2.1a1 1 0 0 0 0-1.4L19 4">
                                            </path>
                                            <path d="m21 2-9.6 9.6"></path>
                                            <circle cx="7.5" cy="15.5" r="5.5"></circle>
                                        </svg>
                                        <?php echo esc_attr__('Google Maps API Configuration', 'asl_locator') ?>
                                    </h4>
                                    <div class="card-text mb-4">
                                        <?php echo esc_attr__('Validate your Google Server API key to ensure accurate coordinate fetching through Google Maps API. Proper validation is essential for successful imports.', 'asl_locator') ?>
                                    </div>
                                    <div class="input-group mt-2">
                                        <label for="txt_server_key"
                                            class="fw-mini-bold"><?php echo esc_attr__('Google Maps API Key', 'asl_locator') ?></label>
                                        <input type="text" id="txt_server_key" readonly="readonly"
                                            value="<?php echo esc_attr($api_key) ?>"
                                            class="form-control bg-white rounded">
                                        <a id="btn-validate-key"
                                            data-loading-text="<?php echo esc_attr__('Validating...', 'asl_locator') ?>"
                                            class="btn  py-2 px-3 rounded btn-primary"><?php echo esc_attr__('Validate Key', 'asl_locator') ?></a>
                                        <div class="input-group-append">
                                        </div>
                                    </div>
                                    <div class=" rounded bg-light mt-4 px-4 py-3">
                                        <p class="sl-quick-help mb-2"><i><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                                    <path d="M12 17h.01"></path>
                                                </svg></i>
                                            <?php echo esc_attr__('Quick Help', 'asl_locator') ?></p>

                                        <ul class="list-unstyled mb-1">
                                            <li class="mb-0">
                                                <p class="help-p">• <a target="_blank"
                                                        class="text-muted text-decoration-none"
                                                        href="https://agilestorelocator.com/wiki/what-is-google-server-key/"><?php echo esc_attr__('What is Google Server Key?','asl_locator') ?></a>
                                                </p>
                                            </li>
                                            <li class="mb-0">
                                                <p class="help-p">• <a target="_blank"
                                                        class="text-muted text-decoration-none"
                                                        href="https://agilestorelocator.com/wiki/can-import-stores-using-excel-sheet/"><?php echo esc_attr__('How to import a CSV file?','asl_locator') ?></a>
                                                </p>
                                            </li>
                                            <li class="mb-0">
                                                <p class="help-p">• <a target="_blank"
                                                        class="text-muted text-decoration-none"
                                                        href="https://agilestorelocator.com/wiki/google-server-api-key-troubleshooting/"><?php echo esc_attr__('Troubleshoot :: Google Server API key ','asl_locator') ?></a>
                                                </p>
                                            </li>
                                            <li class="mb-0">
                                                <p class="help-p">• <a target="_blank"
                                                        class="text-muted text-decoration-none"
                                                        href="https://agilestorelocator.com/wiki/error-0-rows-imported/"><?php echo esc_attr__('Troubleshoot :: Issues','asl_locator') ?></a>
                                                </p>
                                            </li>
                                            <li class="mb-0">
                                                <p class="help-p">• <a target="_blank"
                                                        class="text-muted text-decoration-none"
                                                        href="https://www.youtube.com/watch?v=-9Fvaw_epWA"><?php echo esc_attr__('Watch :: Import Stores Tutorial','asl_locator') ?></a>
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="d-none alert alert-primary mt-4 mb-0"><i class="mb-1"><svg width="16"
                                                height="16" class="text-dark">
                                                <use xlink:href=" #i-alert"></use>
                                            </svg></i><strong><?php echo esc_attr__('Pro Tips:', 'asl_locator') ?></strong><?php echo esc_attr__('The process may take a few minutes for large datasets.', 'asl_locator') ?>
                                    </p>

                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div
                                    class="sl-complx rounded border p-4 asl-fatch-coordinates asl-import-stores-box mb-4">
                                    <h4 class="asl-box-title">
                                        <i class="mr-2"><svg class="text-primary" width="22" height="22">
                                                <use xlink:href="#i-geo-location"></use>
                                            </svg></i>
                                        <?php echo esc_attr__('Coordinates Management', 'asl_locator') ?>
                                    </h4>
                                    <div class="card-text mb-3">
                                        <?php echo esc_attr__('Automatically fetch missing coordinates (Lat/Lng) for your stores using Google Geocoding API. Ensure your API key is validated before proceeding.', 'asl_locator') ?>
                                    </div>
                                    <p class="alert alert-warning d-none mt-4 mb-0"><i class="mb-1"><svg width="16"
                                                height="16" class="text-dark">
                                                <use xlink:href=" #i-alert"></use>
                                            </svg></i><strong><?php echo esc_attr__('Pro Tips:', 'asl_locator') ?></strong><?php echo esc_attr__('The process may take a few minutes for large datasets.', 'asl_locator') ?>
                                    </p>
                                    <div class="rounded  bg-light mt-3 p-4">
                                        <div class="row mb-2 justify-content-between">
                                            <div class="col-md-12">
                                                <h4 class="asl-box-title">
                                                    <?php echo esc_attr__('Missing Coordinates Detection', 'asl_locator') ?>
                                                </h4>
                                                <div class="card-text mb-3">
                                                    <?php echo esc_attr__('Scanning your uploaded files for stores missing latitude/longitude data', 'asl_locator') ?>
                                                </div>
                                            </div>
                                            <div class="d-none col-md-2 text-center text-md-end">
                                                <div class="text-warning fs-3 fw-bold">
                                                    <?php echo esc_attr__('24','asl_locator') ?></div>
                                                <div class="card-text">
                                                    <?php echo esc_attr__(' Stores Found','asl_locator') ?></div>
                                            </div>
                                        </div>
                                        <a data-loading-text="<?php echo esc_attr__('Fetching Coordinates...', 'asl_locator') ?>"
                                            id="btn-fetch-miss-coords"
                                            class="btn w-100 text-white btn-success asl-fetch-coordinate"><i
                                                class="mr-2"><svg width="22" height="22">
                                                    <use xlink:href="#i-geo-location"></use>
                                                </svg></i><?php echo esc_attr__('Fetch Missing Coordinates', 'asl_locator') ?></a>
                                    </div>
                                    <div class="row mt-4">
                                        <!-- Total Stores -->
                                        <div class="col-md-4 text-center mb-3 mb-md-0">
                                            <div class="rounded border p-2">
                                                <div class="text-primary fs-3 fw-bold">
                                                    <?php echo esc_html( $all_stats['stores'] ); ?>
                                                </div>
                                                <div class="card-text">
                                                    <?php echo esc_html__('Total Stores', 'asl_locator'); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- With Coordinates -->
                                        <div class="col-md-4 text-center mb-3 mb-md-0">
                                            <div class="rounded border p-2">
                                                <div class="text-success fs-3 fw-bold">
                                                    <?php echo esc_html( $all_stats['stores_with_coords'] ); ?>
                                                </div>
                                                <div class="card-text">
                                                    <?php echo esc_html__('With Coordinates', 'asl_locator'); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Missing Coordinates -->
                                        <div class="col-md-4 text-center mb-3 mb-md-0">
                                            <div class="rounded border p-2">
                                                <div class="text-danger fs-3 fw-bold">
                                                    <?php echo esc_html( $all_stats['stores_missing_coords'] + $all_stats['stores_invalid_coords'] ); ?>
                                                </div>
                                                <div class="card-text">
                                                    <?php echo esc_html__('Missing Coordinates', 'asl_locator'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="asl-import-stores-box rounded border p-4 my-0 asl-lock-box">

                                    <div class="asl-box-title mb-3">
                                        <i><svg width="20" height="20" class="mb-1 text-primary">
                                                <use xlink:href=" #i-page"></use>
                                            </svg></i>
                                        <?php echo esc_attr__('CSV File Management', 'asl_locator') ?>
                                    </div>
                                    <!-- <div class="card-text mb-3">
                                        <?php echo esc_attr__('Upload your CSV files for store import. Ensure your files follow the correct format.', 'asl_locator') ?>
                                    </div> -->
                                    <!-- <div class="card-text mb-3">
                                        <?php echo esc_attr__('Please upload your CSV file and then import it though the import button, please make sure to follow the given template and the columns should be in the right format as described in the documentation or simply use Template.csv format, please validate your API Key before import.', 'asl_locator') ?>
                                        <?php echo esc_attr__('Guide article: ', 'asl_locator') ?> <a target="_blank"
                                            href="https://agilestorelocator.com/wiki/can-import-stores-using-excel-sheet/"><b><?php echo esc_attr__('Import Stores Using Excel/CSV', 'asl_locator') ?></b></a>.
                                    </div> -->
                                    <p class="alert alert-primary mt-0 mb-4"><i class="mb-1"><svg width="16" height="16"
                                                class="text-dark">
                                                <use xlink:href=" #i-alert"></use>
                                            </svg></i><strong><?php echo esc_attr__('CSV Format Requirements', 'asl_locator') ?></strong><br><?php echo esc_attr__('This version supports CSV files with comma delimiters. For custom formats or technical support, contact our team at support@agilelogix.com', 'asl_locator') ?>
                                    </p>
                                    <div class="row border-top pt-4 mt-2 mb-3">
                                        <div class="col-md-6 mb-3">
                                            <div class="float-md-left">
                                                <div class="asl-mini-heading mb-2  text-black">
                                                    <?php echo esc_attr__('File Operations', 'asl_locator') ?></div>
                                                <button type="button"
                                                    class="btn px-4 py-2 mb-3 mb-md-0 btn-success text-white me-1"
                                                    data-bs-toggle="smodal"
                                                    data-bs-target="#import_store_file_emodel"><i><svg width="16"
                                                            height="16"">
                                                            <use xlink:href=" #i-upload"></use>
                                                        </svg></i><?php echo esc_attr__('Upload CSV', 'asl_locator') ?></button>
                                                <a class="btn  py-2 px-4 mb-3 mb-md-0 border btn-white me-1"
                                                    id="export_store_file_"><i><svg width="16" height="16">
                                                            <use xlink:href="#i-export"></use>
                                                        </svg></i><?php echo esc_attr__('Export All', 'asl_locator') ?></a>
                                                <a target="_blank" class="btn  py-2 px-4 mb-3 mb-md-0 btn-light"
                                                    href="<?php echo ASL_URL_PATH . 'public/export/template-import.csv' ?>"><i><svg
                                                            width="16" height="16">
                                                            <use xlink:href="#i-copy"></use>
                                                        </svg></i>Download Template</a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="float-md-right">
                                                <div class="asl-mini-heading mb-2 text-black">
                                                    <?php echo esc_attr__('Data Management', 'asl_locator') ?></div>
                                                <button type="button"
                                                    class="btn  py-2 px-4 mb-3 mb-md-0 btn-danger text-white me-1"
                                                    data-loading-text="<?php echo esc_attr__('Deleting...', 'asl_locator') ?>"
                                                    id="asl-delete-stores"><i><svg width="16" height="16">
                                                            <use xlink:href="#i-trash"></use>
                                                        </svg></i><?php echo esc_attr__('Delete All Stores', 'asl_locator') ?></button>
                                                <button type="button"
                                                    class="sl-complx btn  py-2 px-4 mb-3 mb-md-0 btn-light mr-2"
                                                    data-loading-text="<?php echo esc_attr__('Removing...', 'asl_locator') ?>"
                                                    id="asl-duplicate-remove"><i><svg width="16" height="16">
                                                            <use xlink:href="#i-trash"></use>
                                                        </svg></i><?php echo esc_attr__('Remove Duplicates', 'asl_locator') ?></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 border-top pt-4  mb-3">
                                            <div class="asl-box-title">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="mb-1 text-primary">
                                                    <path
                                                        d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                                                    </path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                <?php echo esc_attr__('Import & Export Settings', 'asl_locator') ?>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-lg-4 mb-3 mb-md-0">
                                                    <div class="asl-imprt-exprt-seting p-4">
                                                        <div
                                                            class="form-group  d-md-flex justify-content-between align-items-center sl-complx">
                                                            <label class="d-block" for="sl-duplicates-data">
                                                                <h5 class="fw-mini-bold text-black fs-6 mb-1">
                                                                    <?php echo esc_attr__('Avoid Duplication', 'asl_locator') ?>
                                                                </h5>
                                                                <?php echo esc_attr__('Skip duplicate entries', 'asl_locator') ?>
                                                            </label>
                                                            <select name="sl-duplicates" id="sl-duplicates-data"
                                                                class="custom-select m-250 form-control">
                                                                <option value="">
                                                                    <?php echo esc_attr__('None', 'asl_locator') ?>
                                                                </option>
                                                                <option value="email">
                                                                    <?php echo esc_attr__('Email', 'asl_locator') ?>
                                                                </option>
                                                                <option value="title">
                                                                    <?php echo esc_attr__('Title', 'asl_locator') ?>
                                                                </option>
                                                                <option value="phone">
                                                                    <?php echo esc_attr__('Phone', 'asl_locator') ?>
                                                                </option>
                                                                <option value="lat_lng">
                                                                    <?php echo esc_attr__('Coordinates', 'asl_locator') ?>
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!-- <p class="text-muted">
                                                            <small><?php echo esc_attr__('It may slow import process for a large CSV file with 5k+ rows.', 'asl_locator') ?></small>
                                                        </p> -->
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mb-3 mb-md-0">
                                                    <div class="asl-imprt-exprt-seting p-4">
                                                        <div
                                                            class="form-group d-flex justify-content-between align-items-center">
                                                            <label for="asl-export-ids">
                                                                <h5 class="fw-mini-bold text-black fs-6 mb-1">
                                                                    <?php echo esc_attr__('Export with Store IDs', 'asl_locator') ?>
                                                                </h5>
                                                                <?php echo esc_attr__('Include unique identifiers', 'asl_locator') ?>
                                                            </label>
                                                            <div class="a-swith a-swith-alone">
                                                                <input type="checkbox"
                                                                    class="cmn-toggle cmn-toggle-round"
                                                                    id="asl-export-ids" name="data[transit_layer]">
                                                                <label for="asl-export-ids"></label>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="form-group sl-complx">
                                                            <label class="switch" for="asl-export-ids"><input
                                                                    type="checkbox" class="custom-control-input"
                                                                    id="asl-export-ids"><span
                                                                    class="slider round"></span></label>
                                                            <label
                                                                for="asl-export-ids"><?php echo esc_attr__('Export with Store IDs (IDs are required for update)', 'asl_locator') ?></label>
                                                        </div> -->
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 mb-3 mb-md-0">
                                                    <div class="asl-imprt-exprt-seting p-4">
                                                        <div
                                                            class="form-group d-flex justify-content-between align-items-center">
                                                            <label for="asl-logo-images">
                                                                <h5 class="fw-mini-bold text-black fs-6 mb-1">
                                                                    <?php echo esc_attr__('Export Logo Paths', 'asl_locator') ?>
                                                                </h5>
                                                                <?php echo esc_attr__('Include image paths', 'asl_locator') ?>
                                                            </label>
                                                            <div class="a-swith a-swith-alone">
                                                                <input type="checkbox"
                                                                    class="cmn-toggle cmn-toggle-round"
                                                                    id="asl-logo-images" name="data[transit_layer]">
                                                                <label for="asl-logo-images"></label>
                                                            </div>
                                                        </div>
                                                        <!-- <div class="form-group sl-complx">
                                                        <label class="switch" for="asl-logo-images"><input
                                                                type="checkbox" class="custom-control-input"
                                                                id="asl-logo-images"><span
                                                                class="slider round"></span></label>
                                                        <label
                                                            for="asl-logo-images"><?php echo esc_attr__('Export with Logo Images Path', 'asl_locator') ?>
                                                    </div> -->
                                                    </div>

                                                </div>
                                            </div>
                                            <p class="alert alert-warning mt-4 mb-0"><i class="mb-1"><svg width="16"
                                                        height="16" class="text-dark">
                                                        <use xlink:href=" #i-alert"></use>
                                                    </svg></i><strong><?php echo esc_attr__('Performance Note:', 'asl_locator') ?></strong>
                                                <?php echo esc_attr__('Large CSV files (5k+ rows) may take longer to process with these options enabled.', 'asl_locator') ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mt-3 border-top">
                                        <div class="col-12 pt-4">
                                            <div class=" mb-3 d-flex align-items-center justify-content-between">
                                                <div class="asl-box-title">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                                        fill="currentColor" class="mb-1 me-1 text-primary"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z" />
                                                        <path
                                                            d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8m0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                                                    </svg>
                                                    <?php echo esc_attr__('CSV Files', 'asl_locator') ?>
                                                </div>
                                                <div class="sl-no-of-files">
                                                    <?php 
                                                    $dir   = ASL_PLUGIN_PATH . 'public/import/';
                                                    $files = @scandir($dir);

                                                    if ($files && is_array($files)) {
                                                        // Remove "." and ".."
                                                        $files = array_diff($files, array('.', '..'));

                                                        $file_count = count($files);

                                                        if ($file_count > 0) {
                                                            echo esc_html( $file_count . ' ' . _n('File', 'Files', $file_count, 'asl_locator') );
                                                        } else {
                                                            echo esc_html__('No Files', 'asl_locator');
                                                        }
                                                    } else {
                                                        echo esc_html__('No Files', 'asl_locator');
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="sl-stores-import-files" class="table">
                                                    <colgroup>
                                                        <col style="width: 32%;">
                                                        <col style="width: 17%;">
                                                        <col style="width: 17%;">
                                                        <col style="width: 17%;">
                                                    </colgroup>
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th align="center">
                                                                <?php echo esc_attr__('File Information', 'asl_locator') ?>
                                                            </th>
                                                            <th align="center">
                                                                <?php echo esc_attr__('Upload Date', 'asl_locator') ?>
                                                            </th>
                                                            <th align="center">
                                                                <?php echo esc_attr__('View File', 'asl_locator') ?>
                                                            </th>
                                                            <th align="center">
                                                                <?php echo esc_attr__('Import Data', 'asl_locator') ?>
                                                            </th>
                                                            <th align="center">
                                                                <?php echo esc_attr__('Delete File', 'asl_locator') ?>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                    $dir   = ASL_PLUGIN_PATH . 'public/import/';
                                                    $files = @scandir($dir);

                                                    if ($files && is_array($files)) {
                                                        // Remove "." and ".."
                                                        $files = array_values(array_diff($files, array('.', '..')));

                                                        if (!empty($files)) {
                                                            // Sort by modified time (latest first)
                                                            usort($files, function($a, $b) use ($dir) {
                                                                return filemtime($dir . '/' . $b) - filemtime($dir . '/' . $a);
                                                            });

                                                            foreach ($files as $file): 
                                                                $file_path = $dir . '/' . $file;
                                                                $file_url  = ASL_URL_PATH . 'public/import/' . $file;
                                                                $size_kb   = round(filesize($file_path) / 1024, 1) . ' KB';
                                                                $file_date = date('F d Y', filemtime($file_path));
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i>
                                                                        <svg width="20" height="20"
                                                                            class="text-primary">
                                                                            <use xlink:href="#i-page"></use>
                                                                        </svg>
                                                                    </i>
                                                                    <span class="sl-import-file-name">
                                                                        <?php echo esc_html($file); ?>
                                                                        <div class="sl-import-file-size">
                                                                            <?php echo esc_html($size_kb); ?></div>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td><?php echo esc_html($file_date); ?></td>
                                                            <td>
                                                                <a href="<?php echo esc_url($file_url); ?>"
                                                                    class="btn py-2 px-md-5 border btn-white" download>
                                                                    <i><svg width="16" height="16">
                                                                            <use xlink:href="#i-export"></use>
                                                                        </svg></i>
                                                                    <?php echo esc_html__('Download','asl_locator'); ?>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn py-2 px-md-5 btn-primary btn-asl-import_store"
                                                                    data-loading-text="<?php echo esc_attr__('Importing...','asl_locator'); ?>"
                                                                    data-id="<?php echo esc_attr($file); ?>">
                                                                    <i><svg width="16" height="16">
                                                                            <use xlink:href="#i-upload"></use>
                                                                        </svg></i>
                                                                    <?php echo esc_html__('Import','asl_locator'); ?>
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn py-2 px-md-5 btn-danger text-white btn-asl-delete_import_file"
                                                                    data-id="<?php echo esc_attr($file); ?>">
                                                                    <i><svg width="13" height="13">
                                                                            <use xlink:href="#i-trash"></use>
                                                                        </svg></i>
                                                                    <?php echo esc_html__('Delete','asl_locator'); ?>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php 
                                                            endforeach;
                                                        } else {
                                                            // No files after filtering
                                                            echo '<tr><td colspan="5" class="text-center text-muted py-4">'
                                                                . esc_html__('No files to import. Upload a CSV file.', 'asl_locator')
                                                                . '</td></tr>';
                                                        }
                                                    } else {
                                                        // scandir failed or directory missing/empty
                                                        echo '<tr><td colspan="5" class="text-center text-muted py-4">'
                                                            . esc_html__('No files to import. Upload a CSV file.', 'asl_locator')
                                                            . '</td></tr>';
                                                    }
                                                    ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="asl-lock-inner">
                                        <svg width="70" height="100" viewBox="0 0 90 120" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M82.5 52.5H75V30C75 13.5 61.5 0 45 0C28.5 0 15 13.5 15 30V52.5H7.5C3.75 52.5 0 56.25 0 60V112.5C0 116.25 3.75 120 7.5 120H82.5C86.25 120 90 116.25 90 112.5V60C90 56.25 86.25 52.5 82.5 52.5ZM52.5 105H37.5L40.5 88.5C36.75 87 33.75 82.5 33.75 78.75C33.75 72.75 39 67.5 45 67.5C51 67.5 56.25 72.75 56.25 78.75C56.25 83.25 54 87 49.5 88.5L52.5 105ZM60 52.5H30V30C30 21.75 36.75 15 45 15C53.25 15 60 21.75 60 30V52.5Z"
                                                fill="white" />
                                        </svg>
                                        <h6><?php echo esc_attr__('Upgrade Plugin To Get Import/Export Feature', 'asl_locator') ?>
                                        </h6>
                                        <a target="_blank"
                                            href="https://codecanyon.net/item/agile-store-locator-google-maps-for-wordpress/16973546"><?php echo esc_attr__('Lifetime License - $59', 'asl_locator') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>


    <div class="smodal fade" id="import_store_file_emodel" role="dialog">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <div class="smodal-header">
                    <h5 class="smodal-title"><?php echo esc_attr__('Upload CSV File', 'asl_locator') ?></h5>
                    <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="smodal-body">
                    <form id="import_store_file" name="import_store_file">
                        <div class="col-md-12 form-group mb-3">
                            <div class="input-group" id="drop-zone">
                                <input type="file" class="form-control  py-2 btn-default" accept=".csv"
                                    id="file-logo-1" />
                                <span for="file-logo-1"
                                    class="input-group-text"><?php echo esc_attr__('File Path', 'asl_locator') ?></span>
                                <!-- <div class="input-group-prepend">
                                    </div>
                                    style="width:98%;opacity:0;position:absolute;top:0;left:0" name="files"
                                    <div class="custom-file">
                                    <label class="custom-file-label"
                                        for="file-logo-1"><?php echo esc_attr__('File Path...', 'asl_locator') ?></label>
                                </div> -->
                            </div>
                        </div>
                        <div class="col-md-12 form-group mb-3">
                            <div class="progress hideelement" style="display:none" id="progress_bar_">
                                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                    aria-valuemax="100" style="width:0%;">
                                    <span style="position:relative" class="sr-only">0% Complete</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <ul></ul>
                        </div>
                        <p id="message_upload" class="alert alert-warning hide"></p>
                        <div class="col-md-12 form-group mb-3">
                            <button class="btn  py-2 btn-primary float-right btn-start" type="button"
                                data-loading-text="Submitting ..."><?php echo esc_attr__('Upload File', 'asl_locator') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
    admin: '<?php echo admin_url('admin-ajax.php') . '?action=asl_ajax_handler&sl-action=export_file&asl-nounce=' . wp_create_nonce('asl-nounce'); ?>',
    url: '<?php echo ASL_UPLOAD_URL ?>'
};

window.addEventListener("load", function() {
    asl_engine.pages.import_store();
});
</script>