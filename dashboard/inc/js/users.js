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
    var { users } = get_json('/inc/app/extdata.php?data=get_users_modules');
    const cols = ['Callsign', 'LastHeardTime', 'On_module', 'Via_node', 'Via_peer'];
    var flag = '';
    var rows = '';
    onmodule = [];
    for (var i = 0; i < users.length; i++) {
        var callsign = users[i].Callsign.split('/')[0].trim();
        var suffix = users[i].Callsign.split('/')[1].trim();
        var { cname, cflag } = get_flag(callsign);
        var lastheard = new Date(users[i].LastHeardTime);
        var module = users[i].On_module;
        var via_node = users[i].Via_node.replace(/\s\s+/g, ' ');
        rows += `
        <tr class="users">
          <td class="nowrap"><img src="/inc/img/flags/${cflag.toLowerCase()}.png" alt="${cname}" title="${cname}"></td>
          <th class="nowrap"><a target="_blank" href="https://www.qrz.com/db/${callsign}">${callsign}</a></th>
          <td class="nowrap">${suffix}</td>
          <td class="nowrap"><a target="_blank" href="https://aprs.fi/${callsign}"><i class="fas fa-satellite"></i></a></td>
          <td class="nowrap">${via_node}</td>
          <td class="nowrap">${DateTimeFormat(lastheard)}</td>
          <td class="nowrap">${module}</td>
        </tr>`;
    }
    $('#userstbody').html(rows);
    var now = new Date();
    $('#lastupdate').text('Last update ' + TimeFormat(new Date(now.getTime() + now.getTimezoneOffset() * 60000)));
}

$(".filter").on('keyup', function () {
    var filter = $('#filter').val().toLowerCase();
    $('.users').each(function (index) {
        plain = $(this).text().toLowerCase();
        if (plain.includes(filter)
        ) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});
