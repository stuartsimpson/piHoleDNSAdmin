var tableApi;

var APIstring = 'api_localDNS.php';

function refreshData() {
  tableApi.ajax.url(APIstring).load();
}

function handleAjaxError(xhr, textStatus, error) {
  if (textStatus === 'timeout') {
    alert('The server took too long to send the data.');
  } else if (xhr.responseText.indexOf('Connection refused') >= 0) {
    alert('An error occured while loading the data: Connection refused. Is FTL running?');
  } else {
    alert('An unknown error occured while loading the data.\n' + xhr.responseText);
  }
  $('#network-entries_processing').hide();
  tableApi.clear();
  tableApi.draw();
}

function saveDNSEntry() {
  $('#saveIcon').css('color', 'red');
  $.post(
    '/admin/api_localDNS.php',
    {
      ipAddress: $('#ipAddress').val(),
      fqdn: $('#fqdn').val(),
      name: $('#name').val(),
      service: 'saveDNSEntry'
    },
    (result) => {
      console.log(result);
      refreshData();
      $('#ipAddress').val('');
      $('#fqdn').val('');
      $('#name').val('');
      $('#saveIcon').css('color', 'green');
    }
  );
}

function deleteDNSEntry(event) {
  $.post('/admin/api_localDNS.php', { service: 'deleteDNSEntry', fqdn: event.currentTarget.id }, (result) => {
    console.log(result);
    refreshData();
  });
}

function restartDNS() {
  $('#restartIcon').css('color', 'red');
  $.post('/admin/api_localDNS.php', { service: 'restartDNS' }, (result) => {
    console.log(result);
    $('#restartIcon').css('color', 'green');
  });
}

function mixColors(ratio, rgb1, rgb2) {
  return [(1.0 - ratio) * rgb1[0] + ratio * rgb2[0], (1.0 - ratio) * rgb1[1] + ratio * rgb2[1], (1.0 - ratio) * rgb1[2] + ratio * rgb2[2]];
}

$('#saveDNSEntry').click(saveDNSEntry);
$('#restartDNS').click(restartDNS);

$(document).ready(function () {
  tableApi = $('#localDNSEntries').DataTable({
    rowCallback: function (row, data, index) {
      var color, mark;
      // Set determined background color
      $(row).css('background-color', color);
      $('td:eq(7)', row).html(mark);
    },

    dom: "<'row'<'col-sm-12'f>>" + "<'row'<'col-sm-4'l><'col-sm-8'p>>" + "<'row'<'col-sm-12'<'table-responsive'tr>>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    ajax: { url: APIstring, error: handleAjaxError, dataSrc: 'network' },
    autoWidth: false,
    processing: true,
    order: [[2, 'asc']],
    columns: [
      { data: 'ipAddress', type: 'ip-address', width: '30%', render: $.fn.dataTable.render.text() },
      { data: 'fqdn', width: '40%', render: $.fn.dataTable.render.text() },
      { data: 'name', width: '30%', render: $.fn.dataTable.render.text() },
      {
        mRender: (data, type, row) => {
          return `<button id="${data.fqdn}" onclick="deleteDNSEntry()" type="button" class="dnsDelete btn btn-default" name="${data.fqdn}"><i class="fa fa-trash"></i></button>`;
        }
      }
    ],
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, 'All']
    ],
    stateSave: true,
    stateSaveCallback: function (settings, data) {
      // Store current state in client's local storage area
      localStorage.setItem('localDNS_table', JSON.stringify(data));
    },
    stateLoadCallback: function (settings) {
      // Receive previous state from client's local storage area
      var data = localStorage.getItem('localDNS_table');
      // Return if not available
      if (data === null) {
        return null;
      }
      data = JSON.parse(data);
      // Always start on the first page
      data['start'] = 0;
      // Always start with empty search field
      data['search']['search'] = '';
      // Apply loaded state to table
      return data;
    },
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent: ''
      }
    ]
  });
});

$('#localDNSEntries').on('draw.dt', () => {
  $('button.dnsDelete').click(deleteDNSEntry);
});
