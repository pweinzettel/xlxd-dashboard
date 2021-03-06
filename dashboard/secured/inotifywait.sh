#!/bin/bash

# control de pid para que no corra en simultaneo...
# deberia ejecutarse un php para poder leer las opciones y tomar los parametros como lo hace la web

XML="/var/log/xlxd.xml"
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
cd ${DIR}/..

function run_proc() {
    php ${DIR}/inotifywait.php
}

function file_write() {
    md5sum -c ${XML}.md5 >/dev/null 2>&1; EC=$?
    test ${EC} -eq 0 && return;
    md5sum ${XML} > ${XML}.md5
    run_proc
}

while inotifywait -q -e close_write /var/log/xlxd.xml >/dev/null 2>&1; do file_write; done
