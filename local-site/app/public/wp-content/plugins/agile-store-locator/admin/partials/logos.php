<!-- Container -->
<div class="asl-p-cont asl-new-bg">
<div class="hide">
  <svg xmlns="http://www.w3.org/2000/svg">
    <symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
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
            <symbol id="i-info" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                <path d="M16 14 L16 23 M16 8 L16 10" />
                <circle cx="16" cy="16" r="14" />
            </symbol>
  </svg>
</div>
  <div class="container sl-manage-logo-page">
    <div class="row asl-inner-cont">
      <div class="col-md-12">
        <div class="card p-0 mb-4">
          <div class="card-title">
            <div>
              <h3>
                <?php echo esc_attr__('Manage Logos','asl_locator') ?>
              </h3>
              <p class="card-text">
                <?php echo esc_attr__('Upload and manage store logos.','asl_locator') ?>
              </p>
            </div>
            <a target="_blank" class="btn btn-outline-light" href="https://agilestorelocator.com/wiki/manage-store-logos/"><?php echo esc_attr__('Guide', 'asl-wc') ?> 
                            <i class="mr-2"><svg style="margin-bottom:2px;" width="14" height="14"><use xlink:href="#i-info"></use></svg></i>
                        </a>
                    </div>
          <div class="card-body p-4">

            <div class="">
            <?php if(!is_writable(ASL_UPLOAD_DIR.'Logo')): ?>
            <h6  class="alert alert-danger" style="font-size: 14px"><?php echo ASL_UPLOAD_DIR.'Logo' ?> <= <?php echo esc_attr__('Directory is not writable, Logo Image upload will fail, make directory writable.','asl_locator') ?></h6>
            <?php endif; ?>
            <div class="row pb-3">
              <div class="col-md-12 ">
                <button type="button" id="btn-asl-delete-all" class="btn btn-danger text-white mrg-r-10"><i><svg width="13" height="13"><use xlink:href="#i-trash"></use></svg></i><?php echo esc_attr__('Delete Selected','asl_locator') ?></button>
                <button type="button" id="btn-asl-new-c" class="btn btn-success text-white mrg-r-10"><i><svg  style="margin-top:-3px;" width="13" height="13"><use xlink:href="#i-plus"></use></svg></i><?php echo esc_attr__('New Logo','asl_locator') ?></button>
              </div>
            </div>
            <div class="table-responsive">
              <table id="tbl_logos" class="table ">
                  <thead>
                    <tr>
                      <th align="center">
                        <input type="text" class="form-control sml" data-id="id" disabled="disabled" style="opacity: 0"/>
                      </th>
                      <th align="center"><input type="text" class="form-control" data-id="id"  placeholder="<?php echo esc_attr__('Search ID','asl_locator') ?>"  /></th>
                      <th align="center"><input type="text" class="form-control" data-id="name"  placeholder="<?php echo esc_attr__('Search Name','asl_locator') ?>"  /></th>
                      <th align="center">
                        <input type="text" class="form-control sml" data-id="id" disabled="disabled" style="opacity: 0"/>
                      </th>
                      <th align="center">&nbsp;</th>
                    </tr>
                    <tr>
                      <th align="center"><a class="select-all"><?php echo esc_attr__('Select All','asl_locator') ?></a></th>
                      <th align="center"><?php echo esc_attr__('Logo ID','asl_locator') ?></th>
                      <th align="center"><?php echo esc_attr__('Name','asl_locator') ?></th>
                      <th align="center"><?php echo esc_attr__('Image','asl_locator') ?></th>
                      <th class="text-center"><?php echo esc_attr__('Action','asl_locator') ?>&nbsp;</th>
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
<div class="smodal fade frm-update" id="asl-update-modal" role="dialog">
    <div class="smodal-dialog" role="document">
      <div class="smodal-content">
        <div class="smodal-header">
          <h5 class="smodal-title"><?php echo esc_attr__('Update Logo','asl_locator') ?></h5>
          <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="smodal-body">
          <form id="frm-updatelogo" name="frm-updatelogo">
          <div class="row">
            <div class="col-md-12 form-group mb-3">
                  <label class="control-label"><?php echo esc_attr__('Logo ID','asl_locator') ?></label>
                  <input type="text" readonly="readonly" class="form-control"  name="data[logo_id]" id="update_logo_id_input">
            </div>
            <div class="col-md-12 form-group mb-3">
                  <label for="txt_name"  class="control-label"><?php echo esc_attr__('Name','asl_locator') ?></label>
                  <input type="text" class="form-control" class="form-control name-field validate[required]" name="data[logo_name]" id="update_logo_name">
            </div>
            <div class="col-md-12 form-group mb-3" id="updatelogo_image">
               <img  src="" id="update_logo_icon" alt="" data-id="same"/>
               <button type="button" class="btn btn-secondary" id="change_image"><?php echo esc_attr__('Change','asl_locator') ?></button>
            </div>
            <div class="col-md-12 form-group" style="display:none" id="updatelogo_editimage">
                <div class="input-group" id="drop-zone">
                  <div class="custom-file">
                    <?php 
                      $logo_meta = 'replace_logo';
                      echo $this->asl_logo_uploader( $logo_meta,'' ); 
                    ?>
                  </div>
                </div>
                <div class="form-group">
                  <div class="progress hideelement" style="display:none" id="progress_bar_">
                      <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                          <span style="position:relative" class="sr-only">0% <?php echo esc_attr__('Complete','asl_locator') ?></span>
                      </div>
                  </div>
                </div>
                <ul></ul>
            </div>
            <p id="message_update"></p>
          </div>
          
          </form> 
        </div>
        <div class="smodal-footer">
          <button class="btn btn-success btn-start mrg-r-15" id="btn-asl-update-logos"   type="button" data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>"><?php echo esc_attr__('Update Logo','asl_locator') ?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="smodal"><?php echo esc_attr__('Cancel','asl_locator') ?></button>
        </div>
      </div>
    </div>
</div>
<!-- asl-cont end-->

<!-- Add New -->
<div class="smodal fade" id="asl-add-modal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="smodal-dialog" role="document">
      <div class="smodal-content">
        <form id="frm-addlogo" name="frm-addlogo">
        <div class="smodal-header">
          <h5 class="smodal-title"><?php echo esc_attr__('Upload Logo','asl_locator') ?></h5>
          <button type="button" class="close" data-bs-dismiss="smodal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="smodal-body">
          <div class="row no-gutters">
            <div class="col-md-12 form-group mb-3">
              <label for="txt_logo-name"><?php echo esc_attr__('Name','asl_locator') ?></label>
              <input type="text" id="txt_logo-name" name="data[logo_name]" placeholder="<?php echo esc_attr__('Logo Name','asl_locator') ?>" class="form-control">
            </div>
            <div class="col-md-12 form-group mb-3" id="drop-zone">
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
                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                  <span style="position:relative" class="sr-only">0% <?php echo esc_attr__('Complete','asl_locator') ?></span>
                </div>
              </div>
            </div>
            <ul></ul>
            <div class="col-12"><p id="message_upload" class="alert alert-warning hide"></p></div>
          </div>
        </div>
        <div class="smodal-footer">
          <button type="button" data-loading-text="<?php echo esc_attr__('Submitting ...','asl_locator') ?>" class="btn btn-start new_upload_logo btn-success"><?php echo esc_attr__('Upload','asl_locator') ?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="smodal"><?php echo esc_attr__('Close','asl_locator') ?></button>
        </div>
        </form>
      </div>
    </div>
</div>

</div>

<!-- SCRIPTS -->
<script type="text/javascript">
var ASL_Instance = {
	url: '<?php echo ASL_UPLOAD_URL ?>'
};

window.addEventListener("load", function() {
asl_engine.pages.manage_logos();
});
</script>
