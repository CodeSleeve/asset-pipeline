/**
 * @author  Kelt Dockins <kelt@dockins.org>
 *
 * This is used when you have a few models you'd like to
 * place within your model
 *
 * It is to be used ...
 *
 * var model = new Backbone.CollectionModel();
 * model.set({report: report, periods: periods, candidate: candidate });
 *
 * 
 * then model.toJSON() would return ...
 * {
 * 	report: { report.toJSON() results }
 * 	periods: { periods.toJSON() results }
 * 	candidate: { candidate.toJSON() results }
 * }
 *
 * 
 */

Backbone.CollectionModel = Backbone.Model.extend({

	set : function(obj, value) {
		var _this = this;
		Backbone.Model.prototype.set.call(this, obj, value);

		for (var index in obj) {
			var o = obj[index];

			if (o instanceof Backbone.Model || o instanceof Backbone.Collection) {
				o.on('all', function(eventName) {
					_this.trigger(eventName);
					_this.trigger(index + ':' + eventName);
				});
			}
		}
	},

	toJSON : function(options) {
		var json = Backbone.Model.prototype.toJSON.call( this, options );
		for (var index in json) {
			if (json[index] instanceof Backbone.Model || json[index] instanceof Backbone.Collection) {
				json[index] = json[index].toJSON(options);
			}
		}
		return json;
	}

});