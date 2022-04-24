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
  var nodes = get_json('/inc/app/extdata.php?data=get_repeaters_nodes');
  var flag = '';
  var rows = '';
  for (const i in nodes) {
    var last_heard = Epoch2DT(nodes[i].last_heard);
    var connect_time = Epoch2DT(nodes[i].connect_time);

    var callsign = nodes[i].callsign.replace(/\s\s+/g, '-');
    var band = band_suffix(callsign.split('-')[1]);
    var { cname, cflag } = get_flag(callsign);

    rows += `
    <tr class="nodes">
      <td class="nowrap"><img src="/inc/img/flags/${cflag.toLowerCase()}.png" alt="${cname}" title="${cname}"></td>
      <th class="nowrap"><a target="_blank" href="https://aprs.fi/${callsign}">${callsign}</a></th>
      <td class="nowrap">${band}</td>
      <td class="nowrap">${last_heard}</td>
      <td class="nowrap">${connect_time}</td>
      <td class="nowrap">${nodes[i].protocol}</td>
      <td class="nowrap">${nodes[i].linked_module}</td>
      <td class="nowrap">${nodes[i].ip}</td>
    </tr>`;
  }
  $('#nodestbody').html(rows);
  var now = new Date();
  $('#lastupdate').text('Last update ' + TimeFormat(new Date(now.getTime() + now.getTimezoneOffset() * 60000)));
}

$(".filter").on('keyup', function () {
  var filter = $('#filter').val().toLowerCase();
  $('.nodes').each(function (index) {
    plain = $(this).text().toLowerCase();
    if (plain.includes(filter)
    ) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
});
