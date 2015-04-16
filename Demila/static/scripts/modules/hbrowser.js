define(function (require, exports, module){
	try{
		var tmpU = navigator.userAgent;
		var yBrowser={
			versions:function(){
				return {
					trident: tmpU.indexOf('Trident') > -1,
					presto: tmpU.indexOf('Presto') > -1,
					webKit: tmpU.indexOf('AppleWebKit') > -1,
					gecko: tmpU.indexOf('Gecko') > -1 && tmpU.indexOf('KHTML') == -1,
					mobile: !!tmpU.match(/AppleWebKit.*Mobile.*/) || !!tmpU.match(/Mobile/),
					ios: tmpU.indexOf('ios') > -1,
					android: tmpU.indexOf('Android') > -1,
					linux: tmpU.indexOf('Linux') > -1,
					iPhone: tmpU.indexOf('iPhone') > -1,
					mac: tmpU.indexOf('Mac') > -1,
					iPad: tmpU.indexOf('iPad') > -1,
					safari: tmpU.indexOf('Safari') > -1,
					maxthon: tmpU.indexOf('Maxthon') > -1,
					isIE: (document.all) ? true : false,
					isIE6: (document.all) ? true : false && ([/MSIE (\d)\.0/i.exec(navigator.userAgent)][0][1] == 6),
					isIE7: (document.all) ? true : false && ([/MSIE (\d)\.0/i.exec(navigator.userAgent)][0][1] == 7)
				};
			}()
		}
	}catch(e){
	};
	return yBrowser;
});