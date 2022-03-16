//@var object モバイル端末かパソコンかを検出する 
let findMobile = {};

findMobile = (() => {
    //@var string デバイスの名前(pc,smartphone,tablet)
    let device_name;
    //@var boolean モバイルデバイスか(タッチ操作可能か、画面回転可能か)、どうか
    let is_mobile_device = false;
    //@var boolean user-agent文字にMobileが入っているか
    let is_mobile = /Mobile/i.test(navigator.userAgent);
    //@var boolean タッチ操作可能か
    let has_touchscreen = false;
    
    //タッチ可能かを検出する
    if ("maxTouchPoints" in navigator) {
        has_touchscreen = navigator.maxTouchPoints > 0;
        
        //タッチ可能かを検出する
    } else if ("msMaxTouchPoints" in navigator) {
        has_touchscreen = navigator.msMaxTouchPoints > 0;
    } else {
        //回転可能か調べる
        if ('orientation' in window) {
            has_touchscreen = true;
        }
    }
    //mobileの文字があるか、タッチ可能かのどちらかで、trueにする
    if(is_mobile || has_touchscreen){
        is_mobile_device = true;
    }
    //モバイル端末ではない場合、パソコン
    if(is_mobile_device === false){
        device_name = 'pc';
    }else{
        //モバイル端末の場合
        //横幅が420以下かつ縦幅が920以下の場合smartphone
        if(window.screen.width <= 420 && window.screen.height <= 920){
            device_name = 'smartphone';
        }else {
            //それ以外はtablet
            device_name = 'tablet';
        }
    }
    //@var string デバイスの名前
    let device = localStorage.getItem('device');
    device_name = 'smartphone';
    device = 'smartphone';
    localStorage.setItem('device', device_name);
    if(device === null && device === undefined && (device_name === 'smartphone' || device_name === 'tablet')){
        window.location = 'http://localhost:8000/pa0001/responsible/set';
    }else if(device === 'pc' && (device_name === 'smartphone' || device_name === 'tablet')){
        window.location = 'http://localhost:8000/pa0001/responsible/set';
    }else if(device === 'smartphone' && device_name === 'pc'){
        window.location = 'http://localhost:8000/pa0001/responsible/reset';
    }
    return {
        device_name: device_name
    }
})();

export {findMobile};