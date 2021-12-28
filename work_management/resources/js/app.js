const { clipboard } = require('./work_js/ptcmcb');
const { TreeAction } = require('./work_js/ptcmta');

require('./bootstrap');

document.getElementById('receive_combobox').addEventListener('change', function(){
    if(document.getElementById('receive_combobox').value === '1'){
        document.getElementById('recieving_port_number').value = '995';
    }else if(document.getElementById('receive_combobox').value === '2'){
        document.getElementById('recieving_port_number').value = '993';
    }
});