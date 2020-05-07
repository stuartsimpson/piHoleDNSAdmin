<?php 
require "scripts/pi-hole/php/header.php";

// Generate CSRF token
if(empty($_SESSION['token'])) {
    $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
}
$token = $_SESSION['token'];
?>
<!-- Send PHP info to JS -->
<div id="token" hidden><?php echo $token ?></div>

<div class="row">
    <div class="col-md-12">
      <div class="box" id="network-details">
        <div class="box-header with-border">
          <h3 class="box-title">Local DNS Manager</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="localDNSEntries" class="display table table-striped table-bordered" cellspacing="0" width="100%">
              <thead>
                  <tr>
                      <th>IP address</th>
                      <th>Hostname</th>
                      <th>Alias</th>
                      <th></th>
                  </tr>
              </thead>
              <tfoot>
                  <tr>
                      <th>IP address</th>
                      <th>Hostname</th>
                      <th>Alias</th>
                      <th></th>
                  </tr>
              </tfoot>
          </table>
          <label>Add DNS Entry</label>
          <div class="form-group input-group">
            <form>
              <table width="100%">
                <tr style="text-align: center;">
                  <td width="25%">
                    <input id="ipAddress" name="ipAddress" type="text" class="form-control" placeholder="IP (example:192.168.1.200)">
                  </td>
                  <td width="30%">
                    <input id="fqdn" name="fqdn" type="text" class="form-control" placeholder="FQDN (example: hostName.example.com)">
                  </td>
                  <td width="25%">
                    <input id="name" name="name" type="text" class="form-control" placeholder="Add a domain (example: hostName)">
                  </td>
                  <td width="10%">
                    <button id="saveDNSEntry" type="button" class="btn btn-default" name="saveFile">Save <i id="saveIcon" class="fa fa-save"></i></button>
                  </td>
                  <td width="10%">
                    <button id="restartDNS" type="button" class="btn btn-default" name="restartDNS">Restart <i id="restartIcon" class="fa fa-sync"></i></button>
                  </td>
                </tr>
              </table>
            </form>
          </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </div>
</div>
<!-- /.row -->

<?php
    require "scripts/pi-hole/php/footer.php";
?>

<script src="scripts/vendor/moment.min.js"></script>
<script src="scripts/pi-hole/js/ip-address-sorting.js"></script>
<script src="scripts/pi-hole/js/localDNS.js"></script>
