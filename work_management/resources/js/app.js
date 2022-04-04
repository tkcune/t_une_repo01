const { findMobile } = require('./work_js/ptcmrd');
const { clipboard } = require('./work_js/ptcmcb');
const { TreeAction } = require('./work_js/ptcmta');
const { HierarchyBar } = require('./work_js/ptcmhb');

require('./bootstrap');

if(document.getElementById('receive_combobox')){
    document.getElementById('receive_combobox').addEventListener('change', function(){
        if(document.getElementById('receive_combobox').value === '1'){
            document.getElementById('recieving_port_number').value = '995';
            document.getElementById('recieving_server').value = "pop3.muumuu-mail.com";
        }else if(document.getElementById('receive_combobox').value === '2'){
            document.getElementById('recieving_port_number').value = '993';
            document.getElementById('recieving_server').value = 'imap4.muumuu-mail.com';
        }
    });
}

if(document.getElementById('bs_question')){
    let bs_question = document.getElementById('bs_question');
    let bs_question_baloon = document.getElementById('bs_question_baloon');
    bs_question.addEventListener('click', function(){
        if(bs_question_baloon.className == 'bs_baloon'){
            bs_question_baloon.className = 'bs_not_baloon';
        }else{
            bs_question_baloon.className = 'bs_baloon';
        }
    });
}