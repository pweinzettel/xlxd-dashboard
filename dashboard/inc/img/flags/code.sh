#/bin/bash
rm flags.css flags.js
for i in $(ls *.png); do
    F=$(basename ${i} .png)
    N=$(grep ^${F}, code.csv | awk -F, {'print$NF'})
    if [ -n "${N}" ]; then
        N="-${N}"
    else
        N="-flag"
    fi
    echo ".${F}${N}::before { content: url('/static/img/flags/${F}.png') } " >> flags.css
    echo ",'flags ${F}${N}'" >> flags.js
done