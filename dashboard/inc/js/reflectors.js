$(document).ready(function () {
  var ref = get_json('/inc/app/extdata.php?data=get_reflector_list');

  var timestamp = ref.timestamp;

  const cols = ['name', 'lastip', 'dashboardurl', 'uptime', 'lastcontact', 'country', 'comment'];

  var reflector = [];
  var status = '';
  var bgcol = '';
  var uptime = '';

  for (var i = 0; i < ref.reflectors.length; i++) {
    if (parseInt(ref.reflectors[i].lastcontact) + 1800 > timestamp) {
      status = '<i class="fas fa-thumbs-up text-success"></i>';
      uptime = secUpSince(parseInt(ref.reflectors[i]['uptime']) + (Math.round(new Date().getTime() / 1000) - ref.reflectors[i].lastcontact));
      bgcol = '';
    } else {
      status = '<i class="fas fa-thumbs-down text-danger"></i>';
      uptime = '---'
      bgcol = 'text-muted';
    }
    $('#reftbody').append(`<tr class="reflector ${bgcol}">
    <th><a target="_blank" href="${ref.reflectors[i]['dashboardurl']}">${ref.reflectors[i]['name']}</a></th>
    <td>${ref.reflectors[i]['country']}</td>
    <td>${status}</td>
    <td>${uptime}</td>
    <td>${ref.reflectors[i]['comment']}</td>
    </tr>`);
  }

  $('.hol').hide();
});

$(".filter").on('keyup', function () {
  var filter = $('#filter').val().toLowerCase();
  $('.reflector').each(function (index) {
    plain = $(this).text().toLowerCase();
    if (plain.includes(filter)
    ) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
});
