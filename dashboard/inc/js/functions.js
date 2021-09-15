function get_xml($url) {
  var res = new XMLHttpRequest();
  res.open("GET", $url, false);
  res.send(null);
  if (res.status != 200) return false;

  parser = new DOMParser();
  xmltext = res.responseText.replaceAll('&', '#!#');
  xmlDoc = parser.parseFromString(xmltext, "text/xml");

  return xmlDoc;
}

function get_json($url) {
  var res = new XMLHttpRequest();
  res.open("GET", $url, false);
  res.send(null);
  if (res.status != 200) return false;
  return JSON.parse(res.responseText);
}

function DateTimeFormat(date) {
  return date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString('en-GB');
}
function DateFormat(date) {
  return date.toLocaleDateString('en-GB');
}
function TimeFormat(date) {
  return date.toLocaleTimeString('en-GB');
}

function timeSince(date) {
  var seconds = Math.floor((new Date() - date) / 1000);
  return secUpSince(seconds);
}

function secUpSince(seconds) {
  var ret = '';
  var interval = '';

  interval = seconds / 86400;
  if (interval > 1) {
    ret += Math.floor(interval) + " days ";
  }

  ret += new Date(seconds * 1000).toISOString().substr(11, 8);

  return ret;
}

var flags = [];
function get_flag(call) {
  if (flags.length < 1) {
    flags = get_json('/inc/app/extdata.php?data=get_countries');
  }
  for (var flag of flags) {
    for (var st of flag.calls) {
      if ( call.startsWith(st) ) {
        return { cname: flag.name, cflag: flag.flag };
      }
    }
  }

  return 'ok';
}

function band_suffix(sufix) {
  switch (sufix) {
    case 'A' : return '23cm';
    case 'B' : return '70cm';
    case 'C' : return '2m';
    case 'D' : return 'Dongle';
    case 'G' : return 'Internet-Gateway';
    
    default:
      return '['+sufix+']';
  }
}