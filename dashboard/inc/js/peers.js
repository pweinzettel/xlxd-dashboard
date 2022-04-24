$(document).ready(function () {
  $('.hol').show();
  updateTable();
  $('.hol').hide();
  updates = (typeof updates === 'undefined') ? 30 : updates;
  var updatems = updates * 1000;

  setInterval(function () {//setInterval() method execute on every interval until called clearInterval()
      updateTable();
      $('.filter').keyup();
  }, updatems);
});

function updateTable() {
  var peers = get_json('/inc/app/extdata.php?data=get_peers');

  var rows = '';
  for (const i in peers) {
    var lastheard = Epoch2DT(peers[i].last_heard);
    var connect_time = Epoch2DT(peers[i].connect_time);
    var callsign = peers[i].callsign.replace(/\s\s+/g, '-');

    rows += `
    <tr class="peers">
      <th class="nowrap">${callsign}</a></th>
      <td class="nowrap">${lastheard}</td>
      <td class="nowrap">${connect_time}</td>
      <td class="nowrap">${peers[i].protocol}</td>
      <td class="nowrap">${peers[i].linked_module}</td>
      <td class="nowrap">${peers[i].ip}</td>
    </tr>`;
  }
  $('#peerstbody').html(rows);
  var now = new Date();
  $('#lastupdate').text('Last update ' + TimeFormat(new Date(now.getTime() + now.getTimezoneOffset() * 60000)));
}

$(".filter").on('keyup', function () {
  var filter = $('#filter').val().toLowerCase();
  $('.peers').each(function (index) {
    plain = $(this).text().toLowerCase();
    if (plain.includes(filter)
    ) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
});
