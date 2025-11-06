<!-- Container -->
<div class="asl-p-cont asl-new-bg">
    <div class="hide">
        <svg xmlns="http://www.w3.org/2000/svg">
            <symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Add','asl_locator') ?></title>
                <path d="M16 2 L16 30 M2 16 L30 16" />
            </symbol>
             <symbol id="i-trash" viewBox="0 0 32 32" fill="none" stroke="currentcolor" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="2">>
                <title><?php echo esc_attr__('Trash','asl_locator') ?></title>
                <path
                    d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
            </symbol>
            <symbol id="i-edit" fill="currentColor" viewBox="0 0 17 17">
                <title><?php echo esc_attr__('Edit','asl_locator') ?></title>
                <path
                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                <path fill-rule="evenodd"
                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
            </symbol>
            <svg id="i-alert" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor"
                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <title><?php echo esc_attr__('Warning','asl_locator') ?></title>
                <path d="M16 3 L30 29 2 29 Z M16 11 L16 19 M16 23 L16 25" />
            </svg>
            <symbol id="i-info" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
            </symbol>
        </svg>
    </div>
    <div class="container sl-manage-cat-page">
        <div class="row asl-inner-cont">
            <div class="col-md-12">
                <div class="card p-0 mb-4">
                    <div class="card-title">
                        <div>                            
                            <h3>
                                <?php echo esc_attr__('Manage Categories','asl_locator') ?><?php echo \AgileStoreLocator\Helper::getLangControl(); ?>
                            </h3>
                            <p class="card-text">
                                <?php echo esc_attr__('Make categories and group stores.', 'asl_locator') ?>
                            </p>
                        </div>
                        <a target="_blank" class="btn btn-outline-light" href="https://agilestorelocator.com/wiki/manage-categories/"><?php echo esc_attr__('Guide', 'asl-wc') ?> 
                            <i class="mr-2"><svg style="margin-bottom:2px;" width="14" height="14"><use xlink:href="#i-info"></use></svg></i>
                        </a>  
                    </div>
                    <div class="card-body p-4">
                        <div class="">
                                    <?php if(!is_writable(ASL_UPLOAD_DIR.'svg')): ?>
                                    <h3 class="alert alert-danger" style="font-size: 14px">
                                    <?php echo esc_attr__('Directory is not writable, Category Image Upload will Fail, Make directory writable.','asl_locator') ?>:<br> 
                                    <?php echo ASL_UPLOAD_DIR.'svg' ?>
                                    </h3>
                                    <?php endif; ?>
                                    <div class="row pb-3">
                                        <div class="col-md-12 ">
                                            <button type="button" id="btn-asl-delete-all"
                                                class="btn btn-danger text-white mrg-r-10"><i><svg width="13"
                                                        height="13">
                                                        <use xlink:href="#i-trash"></use>
                                                    </svg></i><?php echo esc_attr__('Delete Selected','asl_locator') ?></button>
                                            <button type="button" id="btn-asl-new-c"
                                                class="btn btn-success text-white"><i><svg style="margin-top:-3px;"  width="13" height="13">
                                                        <use xlink:href="#i-plus"></use>
                                                    </svg></i><?php echo esc_attr__('New Category','asl_locator') ?></button>
                                        </div>
                                    </div>
                                    <!-- <div class="alert alert-info" role="alert">
                                        <?php echo esc_attr__('Upload SVG File for Categories if you are using Template 2. To sort categories by the Order ID, please use shortcode attribute cat_sort="order", example: [ASL_STORELOCATOR cat_sort="ordr"]','asl_locator') ?>
                                    </div> -->
                                    <div class="table-responsive">
                                        <table id="tbl_categories" class="table ">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><input type="text" class="form-control" data-id="id"
                                                            disabled="disabled" style="opacity: 0"
                                                            placeholder="<?php echo esc_attr__('Search ID','asl_locator') ?>" />
                                                    </th>
                                                    <th scope="col" ><input type="text" class="form-control"
                                                            data-id="id"
                                                            placeholder="<?php echo esc_attr__('Search ID','asl_locator') ?>" />
                                                    </th>
                                                    <th scope="col" class="sl-dt-wd-250"><input type="text" class="form-control"
                                                            data-id="category_name"
                                                            placeholder="<?php echo esc_attr__('Search Name','asl_locator') ?>" />
                                                    </th>
                                                    <th scope="col"><input type="text" class="form-control"
                                                            data-id="ordr"
                                                            placeholder="<?php echo esc_attr__('Order ID','asl_locator') ?>" />
                                                    </th>
                                                    <th scope="col"><input class="sl-dt-wd-100"style="opacity: 0"/></th>
                                                    <th scope="col"><input class="sl-dt-wd-100"style="opacity: 0"/></th>
                                                    <th scope="col">&nbsp;</th>
                                                    <th scope="col">&nbsp;</th>
                                                </tr>
                                                <tr>
                                                    <th scope="col"><a
                                                            class="select-all"><?php echo esc_attr__('Select All','asl_locator') ?></a>
                                                    </th>
                                                    <th scope="col" class="text-start">
                                                        <?php echo esc_attr__('Category ID','asl_locator') ?></th>
                                                    <th scope="col"><?php echo esc_attr__('Name','asl_locator') ?>
                                                    </th>
                                                    <th scope="col"><?php echo esc_attr__('Parent','asl_locator') ?>
                                                    </th>
                                                    <th scope="col"><?php echo esc_attr__('Order ID','asl_locator') ?></th>
                                                    <th scope="col">
                                                        <?php echo esc_attr__('Icon','asl_locator') ?></th>
                                                    <th scope="col">
                                                        <?php echo esc_attr__('Created On','asl_locator') ?></th>
                                                    <th class="text-center" scope="col">
                                                        <?php echo esc_attr__('Action','asl_locator') ?>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                        </div>

                        <div class="dump-message asl-dumper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>






    <!-- Edit Alert -->
    <div class="smodal fade" id="asl-update-modal" role="dialog">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <form id="frm-updatecategory" name="frm-updatecategory">
                    <div class="smodal-header">
                        <h5 class="smodal-title"><?php echo esc_attr__('Update Category','asl_locator') ?></h5>
                        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="smodal-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label
                                    class="control-label"><?php echo esc_attr__('Category ID','asl_locator') ?></label>
                                <input type="text" class="form-control" readonly="readonly" name="data[category_id]"
                                    id="update_category_id_input">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="txt_name"
                                    class="control-label"><?php echo esc_attr__('Name','asl_locator') ?></label>
                                <input type="text" class="form-control validate[required]" name="data[category_name]"
                                    id="update_category_name">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="update_parent_id"
                                    class="control-label"><?php echo esc_attr__('Parent','asl_locator') ?></label>
                                <select name="data[parent_id]" id="update_parent_id"
                                    class="form-control validate[required]"></select>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="update_category_ordr"
                                    class="control-label"><?php echo esc_attr__('Order','asl_locator') ?></label>
                                <input type="number" class="form-control validate[required]" name="data[ordr]"
                                    id="update_category_ordr">
                            </div>
                            <div class="col-md-12 form-group mb-3" id="updatecategory_image">
                                <img src="" id="update_category_icon" alt="" data-id="same"
                                    style="max-width: 80px;max-height: 80px" />
                                <button type="button" class="btn btn-secondary"
                                    id="change_image"><?php echo esc_attr__('Change','asl_locator') ?></button>
                            </div>

                            <div class="col-md-12 form-group mb-3" style="display:none" id="updatecategory_editimage">
                                <div class="input-group" id="drop-zone">
                                    <input type="file" class="form-control"
                                    name="files"
                                    id="file-img-1" />
                                    <span
                                    class="input-group-text"><?php echo esc_attr__('Image','asl_locator') ?></span>
                                    <!-- <div class="input-group-prepend">
                                        style="width:98%;opacity:0;position:absolute;top:0;left:0" 
                                    </div>
                                    <div class="custom-file">
                                        <label class="custom-file-label"
                                            for="file-img-1"><?php echo esc_attr__('File Path...','asl_locator') ?></label>
                                    </div> -->
                                </div>

                                <div class="form-group">
                                    <div class="progress hideelement" style="display:none" id="progress_bar_">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="60"
                                            aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                                            <span style="position:relative" class="sr-only">0% Complete</span>
                                        </div>
                                    </div>
                                </div>
                                <ul></ul>
                            </div>
                            <p id="message_update"></p>
                        </div>
                        <div class="smodal-footer">
                            <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-update-categories"
                                type="button"
                                data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"><?php echo esc_attr__('Update Category','asl_locator') ?></button>
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="smodal"><?php echo esc_attr__('Close','asl_locator') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- asl-cont end-->

    <div class="smodal fade" id="asl-add-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="smodal-dialog" role="document">
            <div class="smodal-content">
                <form id="frm-addcategory" name="frm-addcategory">
                    <div class="smodal-header">
                        <h5 class="smodal-title"><?php echo esc_attr__('Add New Category','asl_locator') ?></h5>
                        <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="smodal-body">
                        <div class="alert alert-primary mt-3 mb-3 d-flex align-items-center justify-content-between" role="alert">
                            <span><?php echo esc_html__('Icon required to add with the category', 'asl_locator'); ?></span>
                            <a href="<?php echo esc_url(ASL_UPLOAD_URL . 'icon/default.png'); ?>" 
                            download 
                            target="_blank" 
                            class="badge bg-success text-decoration-none">
                            <?php echo esc_html__('Download', 'asl_locator'); ?>
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="txt_name"
                                    class="control-label"><?php echo esc_attr__('Name','asl_locator') ?></label>
                                <input type="text" class="form-control" class="form-control validate[required]"
                                    name="data[category_name]">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="parent_id"
                                    class="control-label"><?php echo esc_attr__('Parent','asl_locator') ?></label>
                                <select name="data[parent_id]" id="parent_id" class="form-control"></select>
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <label for="add_category_ordr"
                                    class="control-label"><?php echo esc_attr__('Order','asl_locator') ?></label>
                                <input type="number" class="form-control validate[required]" name="data[ordr]"
                                    id="add_category_ordr">
                            </div>
                            <div class="col-md-12 form-group mb-3">
                                <div class="input-group" id="drop-zone-1">
                                    <input type="file" class="form-control"
                                    name="files"
                                    id="file-img-2" />
                                    <span
                                    class="input-group-text"><?php echo esc_attr__('Icon','asl_locator') ?></span>
                                    <!-- <div class="input-group-prepend">
                                        style="width:98%;opacity:0;position:absolute;top:0;left:0" 
                                    </div>
                                    <div class="custom-file">
                                        <label class="custom-file-label"
                                            for="file-img-2"><?php echo esc_attr__('File Path...','asl_locator') ?></label>
                                    </div> -->
                                </div>
                            </div>

                            <div class="col-md-12 form-group mb-3">
                                <div class="progress hideelement" style="display:none" id="progress_bar">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                        aria-valuemax="100" style="width:0%;">
                                        <span style="position:relative" class="sr-only">0% Complete</span>
                                    </div>
                                </div>
                            </div>
                            <ul></ul>
                            <p id="message_upload" class="alert alert-warning hide"></p>
                        </div>
                    </div>
                    <div class="smodal-footer">
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary btn-start mrg-r-15" id="btn-asl-add-categories"
                                    type="button"
                                    data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"><?php echo esc_attr__('Add Category','asl_locator') ?></button>
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="smodal"><?php echo esc_attr__('Cancel','asl_locator') ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>








<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
    manage_stores_url: '<?php echo admin_url().'admin.php?page=manage-agile-store' ?>&categories=',
    url: '<?php echo ASL_UPLOAD_URL ?>'
};

window.addEventListener("load", function() {
    asl_engine.pages.manage_categories();
});
</script>