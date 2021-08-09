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
  var { peers } = get_json('/inc/app/extdata.php?data=get_peers');

  for (var i = 0; i < peers.length; i++) {
    var lastheard = new Date(peers[i].LastHeardTime);
    var connect = new Date(peers[i].ConnectTime);
    var callsign = peers[i].Callsign.replace(/\s\s+/g, '-');
    var rows = '';

    //<td class="nowrap"><a target="_blank" href="https://aprs.fi/${callsign}">${callsign}</a></td>
    rows += `
    <tr class="peers">
      <th class="nowrap">${callsign}</a></th>
      <td class="nowrap">${DateTimeFormat(lastheard)}</td>
      <td class="nowrap">${timeSince(connect)}</td>
      <td class="nowrap">${peers[i].Protocol}</td>
      <td class="nowrap">${peers[i].LinkedModule}</td>
      <td class="nowrap">${peers[i].IP}</td>
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
