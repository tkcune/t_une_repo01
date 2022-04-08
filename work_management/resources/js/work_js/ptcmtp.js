import { findMobile } from './ptcmrd';

//@var object ツールチップのクラス
let customToolTip = {};

if(findMobile.device_name !== 'pc'){
    customToolTip = (() => {
        //<custom-tooltip></custom-tooltip>の宣言
        class CustomToolTip extends HTMLElement {
            //ツールチップのdom生成
            constructor() {
                super();
                this._updateRendering();
            }
            //domの生成と描画
            _updateRendering() {
                //@var dom ？マーク
                let span = this._create_select();
                //custom-tooltipタグに追加する
                this.appendChild(span);
                //ツールチップの表示の基準とする
                this.style.position = 'relative';
                //@var dom ツールチップのdom
                let tooltip_span = this._create_tooltip();
                tooltip_span = this.appendChild(tooltip_span);
                //ツールチップの表示が画面よりはみ出ていたら、修正する
                this._change_position(span, tooltip_span);
                //？マークのクリック処理
                span.addEventListener('click', () => {
                    //非表示なら表示
                    if(tooltip_span.style.visibility === 'hidden'){
                        tooltip_span.style.visibility = 'visible';
                    }
                });
                //表示したツールチップを非表示にする
                function clearToolTip(){
                    if(this.tooltip_span.style.visibility === 'visible'){
                        this.tooltip_span.style.visibility = 'hidden';
                    }
                }
                //？マーク以外をクリックしたら、ツールチップを非表示にする
                window.addEventListener('click', {tooltip_span: tooltip_span, handleEvent: clearToolTip}, true);
            }
            //？マークの生成
            //@return dom ？マーク
            _create_select(){
                //@var dom ？マーク
                let span = document.createElement('span');
                span.innerText = '?';
                span.style.color = 'red';
                span.style.border = "1px solid blue";
                return span;
            }
            //ツールチップのdomの生成
            //@var dom ツールチップのdom
            _create_tooltip(){
                //@var dom ツールチップのdom
                let tooltip_span = document.createElement('span');
                tooltip_span.style.position = 'absolute';
                tooltip_span.style.width = '10rem';
                tooltip_span.style.top = '1rem';
                tooltip_span.style.left = '1rem';
                //改行を実現する
                tooltip_span.style.whiteSpace = 'pre-line !important';
                tooltip_span.style.backgroundColor = 'white';
                tooltip_span.style.visibility = 'hidden';
                tooltip_span.style.zIndex = '500';
                tooltip_span.innerText = this.getAttribute('title');
                return tooltip_span;
            }
            //ツールチップが画面からはみ出していたら、位置を変更する
            //@param dom span ？マーク
            //@param dom tooltip_span ツールチップのdom
            //@return dom ツールチップのdom
            _change_position(span, tooltip_span){
                //@var int spanの表示位置
                let px = window.pageXOffset + span.getBoundingClientRect().left;
                //@vat int 画面の長さ と spanとツールチップの長さの差
                let diff_width = document.body.clientWidth - (px + tooltip_span.clientWidth);
                //横にはみ出していたら、横にずらす
                if(diff_width < 0){
                    tooltip_span.style.left = (diff_width - 8) + 'px';
                }
                //@var int spanの表示位置
                let py = window.pageYOffset + span.getBoundingClientRect().top;
                //@var int 画面の高さ と spanとツールチップの高さの差
                let diff_hight = document.body.clientHeight - (py + tooltip_span.clientHeight);
                //縦にはみ出していたら、下げる
                if(diff_hight < 0){
                    tooltip_span.style.top = (-tooltip_span.clientHeight - 8) + 'px';
                }
                return tooltip_span;
            }
        }
        //custom-tooltipを使えるように設定する
        customElements.define('custom-tooltip', CustomToolTip);
    })();
}
export {customToolTip}