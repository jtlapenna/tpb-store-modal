<!-- Container -->
<div class="asl-p-cont asl-new-bg">
<div class="hide">
  <svg xmlns="http://www.w3.org/2000/svg">
    <symbol id="i-plus" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <title><?php echo esc_attr__('Add','asl_locator') ?></title>
        <path d="M16 2 L16 30 M2 16 L30 16" />
    </symbol>
    <symbol id="i-trash" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <title><?php echo esc_attr__('Trash','asl_locator') ?></title>
        <path d="M28 6 L6 6 8 30 24 30 26 6 4 6 M16 12 L16 24 M21 12 L20 24 M11 12 L12 24 M12 6 L13 2 19 2 20 6" />
    </symbol>
    <symbol id="i-edit" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <title><?php echo esc_attr__('Edit','asl_locator') ?></title>
        <path d="M30 7 L25 2 5 22 3 29 10 27 Z M21 6 L26 11 Z M5 22 L10 27 Z" />
    </symbol>
    <svg id="i-alert" viewBox="0 0 32 32" width="13" height="13" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <title><?php echo esc_attr__('Warning','asl_locator') ?></title>
        <path d="M16 3 L30 29 2 29 Z M16 11 L16 19 M16 23 L16 25" />
    </svg>
  </svg>
</div>
  <div class="container">
    <div class="row asl-inner-cont">
      <div class="col-md-12 asl-lock-box">
        <div class="card p-0 mb-4">
          <h3 class="card-title"><?php echo esc_attr__('Manage Brands','asl_locator') ?><?php echo \AgileStoreLocator\Helper::getLangControl(); ?></h3>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12 ralign" style="margin-bottom: 15px">
                <button type="button" id="btn-asl-delete-all" class="btn disabled btn-danger mrg-r-10"><i><svg width="13" height="13"><use xlink:href="#i-trash"></use></svg></i><?php echo esc_attr__('Delete Selected','asl_locator') ?></button>
                <button type="button" id="btn-asl-new-attr" class="btn disabled btn-success mrg-r-10"><i><svg width="13" height="13"><use xlink:href="#i-plus"></use></svg></i><?php echo esc_attr__('New Brand','asl_locator') ?></button>
              </div>
            </div>
          	<table id="tbl_attribute" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th align="center">&nbsp;</th>
                    <th align="center"><input type="text" class="form-control" data-id="id"  placeholder="<?php echo esc_attr__('Search ID','asl_locator') ?>"  /></th>
                    <th align="center"><input type="text" class="form-control" data-id="name"  placeholder="<?php echo esc_attr__('Search Name','asl_locator') ?>"  /></th>
                    <th align="center">&nbsp;</th>
                    <th align="center">&nbsp;</th>
                    <th align="center">&nbsp;</th>
                  </tr>
                  <tr>
                    <th align="center"><a class="select-all"><?php echo esc_attr__('Select All','asl_locator') ?></a></th>
                    <th align="center"><?php echo esc_attr__('ID','asl_locator') ?></th>
                    <th align="center"><?php echo esc_attr__('Name','asl_locator') ?></th>
                    <th align="center"><?php echo esc_attr__('Order','asl_locator') ?></th>
                    <th align="center"><?php echo esc_attr__('Created On','asl_locator') ?></th>
                    <th align="center"><?php echo esc_attr__('Action','asl_locator') ?>&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </div>
        </div>
          <div class="asl-lock-inner">
                <svg width="70" height="100" viewBox="0 0 90 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M82.5 52.5H75V30C75 13.5 61.5 0 45 0C28.5 0 15 13.5 15 30V52.5H7.5C3.75 52.5 0 56.25 0 60V112.5C0 116.25 3.75 120 7.5 120H82.5C86.25 120 90 116.25 90 112.5V60C90 56.25 86.25 52.5 82.5 52.5ZM52.5 105H37.5L40.5 88.5C36.75 87 33.75 82.5 33.75 78.75C33.75 72.75 39 67.5 45 67.5C51 67.5 56.25 72.75 56.25 78.75C56.25 83.25 54 87 49.5 88.5L52.5 105ZM60 52.5H30V30C30 21.75 36.75 15 45 15C53.25 15 60 21.75 60 30V52.5Z" fill="white"/></svg>
                <h6><?php echo esc_attr__('Upgrade Plugin To View Your Analytics','asl_locator') ?></h6>
                <a href="https://codecanyon.net/item/agile-store-locator-google-maps-for-wordpress/16973546"><?php echo esc_attr__('Lifetime License - $59','asl_locator') ?></a>
              </div>
      </div>
    </div>
  </div>
</div>    	
<!-- asl-cont end-->