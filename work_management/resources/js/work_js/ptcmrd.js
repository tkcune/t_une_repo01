//@var object モバイル端末かパソコンかを検出する 
let findMobile = {};

findMobile = (() => {
    //@var string デバイスの名前(pc,smartphone,tablet)
    let deviceName;
    //@var boolean モバイルデバイスか(タッチ操作可能か、画面回転可能か)、どうか
    let isMobileDevice = false;
    //@var boolean user-agent文字にMobileが入っているか
    let isMobile = /Mobile/i.test(navigator.userAgent);
    //@var boolean タッチ操作可能か
    let hasTouchscreen = false;
    
    //タッチ可能かを検出する
    if ("maxTouchPoints" in navigator) {
        hasTouchscreen = navigator.maxTouchPoints > 0;
        
        //タッチ可能かを検出する
    } else if ("msMaxTouchPoints" in navigator) {
        hasTouchscreen = navigator.msMaxTouchPoints > 0;
    } else {
        //回転可能か調べる
        if ('orientation' in window) {
            hasTouchscreen = true;
        }
    }
    //mobileの文字があるか、タッチ可能かのどちらかで、trueにする
    if(isMobile || hasTouchscreen){
        isMobileDevice = true;
    }
    //モバイル端末ではない場合、パソコン
    if(isMobileDevice === false){
        deviceName = 'pc';
    }else{
        //モバイル端末の場合
        //横幅が420以下かつ縦幅が920以下の場合smartphone
        if(window.screen.width <= 420 && window.screen.height <= 920){
            deviceName = 'smartphone';
        }else {
            //それ以外はtablet
            deviceName = 'tablet';
        }
    }
    //@var string デバイスの名前
    let device = localStorage.getItem('device');
    localStorage.setItem('device', deviceName);
    if(device === null && device === undefined && (deviceName === 'smartphone' || deviceName === 'tablet')){
        window.location = document.location.origin + '/pa0001/responsible/set';
    }else if(device === 'pc' && (deviceName === 'smartphone' || deviceName === 'tablet')){
        window.location = document.location.origin + '/pa0001/responsible/set';
    }else if((device === 'smartphone' || device === 'tablet') && deviceName === 'pc'){
        window.location = document.location.origin + '/pa0001/responsible/reset';
    }
    return {
        deviceName: deviceName
    }
})();

export {findMobile};