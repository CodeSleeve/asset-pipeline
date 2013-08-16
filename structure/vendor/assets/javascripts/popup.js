/**
 * @author Kelt Dockins <kelt@dockins.org
 * @about This allows me to create dynamic popup windows to sites like Facebook sharer.php
 *
 * @example <a href="javascript:popup('http://www.facebook.com/sharer.php?u=http://www.foobar.com/some/page.html')><i class="icon-facebook-sign"></i></a>
 * @example <script>  var p = new popup('test.html', {width: 130, resizeable: 'yes', shown : false}); p.show(); </script>
 *   
 * @param  string url         the url you want to show in your popup window
 * @param  object params_hash override the _defaultParams_
 * @return popup object
 */

var popup = function (url, params_hash) {
	var obj = this;

	// use popup statically
	if (obj._name_ !== 'popup') {
		return new popup(url, params_hash);
	}

	obj.url = url;
	obj.parameters = (typeof params_hash === "object") ? params_hash : {};
	obj.shown = (typeof obj.parameters['shown'] === 'undefined') ? true : obj.parameters['shown'];

	obj.show = function() {
		var comma = "";
		var parameters = "";
		for (var index in obj._defaultParams_)
		{
			parameters += comma + index + "=";
			parameters += (typeof obj.parameters[index] === "undefined") ? obj._defaultParams_[index] : obj.parameters[index];
			comma = ",";
		}
		obj.window = window.open(obj.url,'popUpWindow', parameters);
	};

	obj.initialize = function() {
		if (this.shown) {
			this.show();
		}
	};

	obj.initialize();

	return obj;
}

popup.prototype._name_ = 'popup';

popup.prototype._defaultParams_ = {
	height : 340,
	width : 600,
	left : 10,
	top : 10,
	resizable : 'no',
	scrollbars : 'no',
	menubar : 'no',
	location : 'no',
	diretories : 'no',
	status : 'yes'
};
