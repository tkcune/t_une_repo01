import { findMobile } from './ptcmrd';

//@var object ツールチップのクラス
let customToolTip = {};

if(findMobile.deviceName !== 'pc'){
    customToolTip = (() => {
        //<custom-tooltip></custom-tooltip>の宣言
        class CustomToolTip extends HTMLElement {
            //ツールチップのdom生成
            constructor() {
                super();
                this.updateRendering();
            }
            //domの生成と描画
            updateRendering() {
                //@var dom ？マーク
                let span = this.createSelect();
                //custom-tooltipタグに追加する
                this.appendChild(span);
                //ツールチップの表示の基準とする
                this.style.position = 'relative';
                //@var dom ツールチップのdom
                let tooltipSpan = this.createTooltip();
                tooltipSpan = this.appendChild(tooltipSpan);
                //ツールチップの表示が画面よりはみ出ていたら、修正する
                this.changePosition(span, tooltipSpan);
                //？マークのクリック処理
                span.addEventListener('click', () => {
                    //非表示なら表示
                    if(tooltipSpan.style.visibility === 'hidden'){
                        tooltipSpan.style.visibility = 'visible';
                    }
                });
                //表示したツールチップを非表示にする
                function clearToolTip(){
                    if(this.tooltipSpan.style.visibility === 'visible'){
                        this.tooltipSpan.style.visibility = 'hidden';
                    }
                }
                //？マーク以外をクリックしたら、ツールチップを非表示にする
                window.addEventListener('click', {tooltipSpan: tooltipSpan, handleEvent: clearToolTip}, true);
            }
            //？マークの生成
            //@return dom ？マーク
            createSelect(){
                //@var dom ？マーク
                let span = document.createElement('span');
                span.innerText = '?';
                span.style.color = 'red';
                span.style.borderBottom = '1px solid blue';
                span.style.margin = '2px';
                return span;
            }
            //ツールチップのdomの生成
            //@var dom ツールチップのdom
            createTooltip(){
                //@var dom ツールチップのdom
                let tooltipSpan = document.createElement('span');
                tooltipSpan.style.position = 'absolute';
                tooltipSpan.style.width = '10rem';
                tooltipSpan.style.top = '1rem';
                tooltipSpan.style.left = '1rem';
                //改行を実現する
                tooltipSpan.style.whiteSpace = 'pre-line !important';
                tooltipSpan.style.backgroundColor = 'white';
                tooltipSpan.style.visibility = 'hidden';
                tooltipSpan.style.zIndex = '500';
                tooltipSpan.style.border = '1px solid';
                tooltipSpan.style.fontWeight = 'bold';
                tooltipSpan.innerText = this.getAttribute('title');
                if(this.getAttribute('title').length >= 100){
                    tooltipSpan.style.width = '20rem';
                }
                return tooltipSpan;
            }
            //ツールチップが画面からはみ出していたら、位置を変更する
            //@param dom span ？マーク
            //@param dom tooltip_span ツールチップのdom
            //@return dom ツールチップのdom
            changePosition(span, tooltipSpan){
                //@var int spanの表示位置
                let px = window.pageXOffset + span.getBoundingClientRect().left;
                //@vat int 画面の長さ と spanとツールチップの長さの差
                let diffWidth = document.body.clientWidth - (px + tooltipSpan.clientWidth);
                //横にはみ出していたら、横にずらす
                if(diffWidth < 0){
                    tooltipSpan.style.left = (diffWidth - 8) + 'px';
                }
                //@var int spanの表示位置
                let py = window.pageYOffset + span.getBoundingClientRect().top;
                //@var int 画面の高さ と spanとツールチップの高さの差
                let diffHight = document.body.clientHeight - (py + tooltipSpan.clientHeight);
                //縦にはみ出していたら、下げる
                if(diffHight < 0){
                    tooltipSpan.style.top = (-tooltipSpan.clientHeight - 8) + 'px';
                }
                return tooltipSpan;
            }
        }
        //custom-tooltipを使えるように設定する
        customElements.define('custom-tooltip', CustomToolTip);
    })();
}
export {customToolTip}