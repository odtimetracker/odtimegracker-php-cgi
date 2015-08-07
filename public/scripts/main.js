/**
 * odtimetracker-php-cgi
 *
 * @license Mozilla Public License 2.0 https://www.mozilla.org/MPL/2.0/
 * @author Ondřej Doněk, <ondrejd@gmail.com>
 */

/**
 * Our application.
 * 
 * @var {Object} 
 */
var odTimeTracker = {};
(function() {
	/**
	 * @type Object Holds projects.
	 */
	this.projects = {};

	/**
	 * Retrieve URL for given method.
	 * 
	 * @param {String} aMethod
	 * @returns {String}
	 */
	this.getDataUrl = function(aMethod) {
		var url = 'http://odtimetracker.local/json/' + aMethod;
		console.log('odTimeTracker.getDataUrl', url);
		return url;	
	};

	/**
	 * Create HTML for an activity.
	 * 
	 * @param {Object} aActivity
	 * @param {HtmlElement|String} aParent Parent element.
	 * @returns {void}
	 */
	this.createActivityHtml = function(aActivity, aParent) {
		var cont = document.createElement('div');
		cont.setAttribute('id', 'activity_' + aActivity.ActivityId);
		cont.classList.add('row');
		cont.classList.add('activity-card');

		var innerCont = document.createElement('div');
		innerCont.classList.add('col-md-12');

		var leftCont = document.createElement('div');
		leftCont.classList.add('col-md-8');
		leftCont.classList.add('left-part');

		// Activity name
		var h3 = document.createElement('h3');
		h3.appendChild(document.createTextNode(aActivity.Name));
		
		if (aActivity.Stopped === '' || aActivity.Stopped === null) {
			var h3Small = document.createElement('small');
			var h3SmallSpan = document.createElement('span');
			h3SmallSpan.classList.add('label');
			h3SmallSpan.classList.add('label-warning');
			h3SmallSpan.appendChild(document.createTextNode('Aktivní'));
			h3Small.appendChild(h3SmallSpan);
		}

		leftCont.appendChild(h3)

		var metaDiv = document.createElement('div');

		// Project
		var projectName = odTimeTracker.projects[aActivity.ProjectId].Name;
		var pdSpan = document.createElement('span');
		pdSpan.setAttribute('role', 'presentation');
		pdSpan.classList.add('dropdown');
		var pdBtn = document.createElement('button');
		pdBtn.setAttribute('id', 'projectMenu_' + aActivity.ActivityId);
		pdBtn.setAttribute('type', 'button');
		pdBtn.setAttribute('data-toggle', 'dropdown');
		pdBtn.setAttribute('aria-haspopup', 'true');
		pdBtn.setAttribute('aria-expanded', 'true');
		pdBtn.classList.add('btn');
		pdBtn.classList.add('btn-default');
		pdBtn.classList.add('btn-xs');
		pdBtn.classList.add('dropdown-toggle');
		pdBtn.appendChild(document.createTextNode(projectName + ' '));
		var pdBtnSpan = document.createElement('span');
		pdBtnSpan.classList.add('caret');
		pdBtn.appendChild(pdBtnSpan);
		pdSpan.appendChild(pdBtn);
		var pdUl = document.createElement('ul');
		pdUl.setAttribute('aria-labelledby', 'projectMenu_' + aActivity.ActivityId);
		pdUl.classList.add('dropdown-menu');
		// Menuitem 1
		var pdUlLi1 = document.createElement('li');
		pdUlLi1.setAttribute('role', 'presentation');
		var pdUlLi1A = document.createElement('a');
		pdUlLi1A.setAttribute('href', '#');
		pdUlLi1A.appendChild(document.createTextNode('Změnit projekt'));
		pdUlLi1.appendChild(pdUlLi1A);
		pdUl.appendChild(pdUlLi1);
		// Separator
		var pdUlLi2 = document.createElement('li');
		pdUlLi2.setAttribute('role', 'separator');
		pdUlLi2.classList.add('divider');
		pdUl.appendChild(pdUlLi2);
		// Menuitem 2
		var pdUlLi3 = document.createElement('li');
		pdUlLi3.setAttribute('role', 'presentation');
		var pdUlLi3A = document.createElement('a');
		pdUlLi3A.setAttribute('href', '#');
		pdUlLi3A.appendChild(document.createTextNode('Upravit projekt'));
		pdUlLi3.appendChild(pdUlLi3A);
		pdUl.appendChild(pdUlLi3);
		// Separator
		var pdUlLi4 = document.createElement('li');
		pdUlLi4.setAttribute('role', 'separator');
		pdUlLi4.classList.add('divider');
		pdUl.appendChild(pdUlLi4);
		// Menuitem 3
		var pdUlLi5 = document.createElement('li');
		pdUlLi5.setAttribute('role', 'presentation');
		var pdUlLi5A = document.createElement('a');
		pdUlLi5A.setAttribute('href', '#');
		pdUlLi5A.appendChild(document.createTextNode('Filter...'));
		pdUlLi5.appendChild(pdUlLi5A);
		pdUl.appendChild(pdUlLi5);
		pdSpan.appendChild(pdUl);
		metaDiv.appendChild(pdSpan);
		metaDiv.appendChild(document.createTextNode(' '));

		// Tags
		var tags = aActivity.Tags.split(',');
		for (var i=0; i<tags.length; i++) {
			var tagSpanId = 'tagMenu_' + aActivity.ActivityId + '_' + i;
			var tagSpan = document.createElement('span');
			tagSpan.setAttribute('role', 'presentation');
			tagSpan.classList.add('dropdown');
			var tagBtn = document.createElement('button');
			tagBtn.setAttribute('id', tagSpanId);
			tagBtn.setAttribute('type', 'button');
			tagBtn.setAttribute('data-toggle', 'dropdown');
			tagBtn.setAttribute('aria-haspopup', 'true');
			tagBtn.setAttribute('aria-expanded', 'true');
			tagBtn.classList.add('btn');
			tagBtn.classList.add('btn-info');
			tagBtn.classList.add('btn-xs');
			tagBtn.classList.add('dropdown-toggle');
			tagBtn.appendChild(document.createTextNode(tags[i] + ' '));
			var tagBtnSpan = document.createElement('span');
			tagBtnSpan.classList.add('caret');
			tagBtn.appendChild(tagBtnSpan);
			tagSpan.appendChild(tagBtn);
			var tagUl = document.createElement('ul');
			tagUl.setAttribute('aria-labelledby', tagSpanId);
			tagUl.classList.add('dropdown-menu');
			// Menuitem 1
			var tagUlLi1 = document.createElement('li');
			tagUlLi1.setAttribute('role', 'presentation');
			var tagUlLi1A = document.createElement('a');
			tagUlLi1A.setAttribute('href', '#');
			tagUlLi1A.appendChild(document.createTextNode('Změnit projekt'));
			tagUlLi1.appendChild(tagUlLi1A);
			tagUl.appendChild(tagUlLi1);
			// Separator
			var tagUlLi2 = document.createElement('li');
			tagUlLi2.setAttribute('role', 'separator');
			tagUlLi2.classList.add('divider');
			tagUl.appendChild(tagUlLi2);
			// Menuitem 2
			var tagUlLi3 = document.createElement('li');
			tagUlLi3.setAttribute('role', 'presentation');
			var tagUlLi3A = document.createElement('a');
			tagUlLi3A.setAttribute('href', '#');
			tagUlLi3A.appendChild(document.createTextNode('Upravit projekt'));
			tagUlLi3.appendChild(tagUlLi3A);
			tagUl.appendChild(tagUlLi3);
			// Separator
			var tagUlLi4 = document.createElement('li');
			tagUlLi4.setAttribute('role', 'separator');
			tagUlLi4.classList.add('divider');
			tagUl.appendChild(tagUlLi4);
			// Menuitem 3
			var tagUlLi5 = document.createElement('li');
			tagUlLi5.setAttribute('role', 'presentation');
			var tagUlLi5A = document.createElement('a');
			tagUlLi5A.setAttribute('href', '#');
			tagUlLi5A.appendChild(document.createTextNode('Filter...'));
			tagUlLi5.appendChild(tagUlLi5A);
			tagUl.appendChild(tagUlLi5);
			tagSpan.appendChild(tagUl);
			metaDiv.appendChild(tagSpan);

			if (i + 1 < tags.length) {
				metaDiv.appendChild(document.createTextNode(' '));
			}
		}

		leftCont.appendChild(metaDiv);

		// Activity description
		var descPara = document.createElement('p');
		descPara.classList.add('description');
		var descParaSmall = document.createElement('small');
		var descParaSmallText = aActivity.Description 
		if (aActivity.Description === '' || aActivity.Description === null) {
			descParaSmallText = 'Tato aktivita nemá popis...';
		}
		descParaSmall.appendChild(document.createTextNode(descParaSmallText));
		descPara.appendChild(descParaSmall);
		leftCont.appendChild(descPara);
		
		innerCont.appendChild(leftCont);

		var rightCont = document.createElement('div');
		rightCont.classList.add('col-md-4');
		rightCont.classList.add('right-part');

		// Duration
		var h4 = document.createElement('h4');
		h4.appendChild(document.createTextNode(aActivity.DurationFormatted));
		rightCont.appendChild(h4);

		var datesSpan = document.createElement('span');
		datesSpan.appendChild(document.createTextNode(aActivity.StartedFormatted));
		datesSpan.appendChild(document.createTextNode(' - '));

		if (
			aActivity.IsWithinOneDay === true && 
			(aActivity.Stopped !== null || aActivity.Stopped !== '')
		) {
			var toStr = aActivity.StoppedFormatted.split(' ')[1];
			datesSpan.appendChild(document.createTextNode(toStr));
		}
		else if (aActivity.Stopped === null || aActivity.Stopped === '') {
			var stopBtn = document.createElement('button');
			stopBtn.setAttribute('type', 'button');
			stopBtn.classList.add('btn');
			stopBtn.classList.add('btn-danger');
			stopBtn.classList.add('btn-xs');
			stopBtn.appendChild(document.createTextNode('Zastavit'));
			datesSpan.appendChild(stopBtn);
		}
		else {
			datesSpan.appendChild(document.createTextNode(aActivity.StoppedFormatted));
		}

		rightCont.appendChild(datesSpan);
		innerCont.appendChild(rightCont);

		cont.appendChild(innerCont);

		// TODO Check if `aParent` is `HTMLElement` or what!
		aParent.appendChild(cont);
	};

	/**
	 * Called when loading JSON with activities is finished.
	 * 
	 * @param {Object} aData
	 * @param {String} aStatus
	 * @param {jQuery.XHR} aRequest
	 * @returns {void} 
	 */
	this.loadingActivitiesDone = function(aData, aStatus, aRequest) {
		if ('errorMessage' in aData) {
			console.log('Error:', aData.errorMessage);
		}

		if (!('activities' in aData)) {
			console.log('Error:', 'No `activities` found in given data!');
			return;
		}

		if (!Array.isArray(aData.activities)) {
			console.log('Error:', 'Property `data.activities` is not an array!');
			return;
		}

		odTimeTracker.projects = aData.projects;

		var content = document.getElementById('content');

		for (
			var i = 0; 
			i < aData.activities.length; 
			odTimeTracker.createActivityHtml(aData.activities[i++], content)
		);
	};

	/**
	 * Called when loading JSON with activities failed.
	 * 
	 * @param {jQuery.XHR} aRequest
	 * @param {String} aStatus
	 * @param {Object} aError
	 * @returns {void} 
	 */
	this.loadingActivitiesFail = function(aRequest, aStatus, aError) {
		console.log('odTimeTracker.loadingActivitiesFail', aRequest, aStatus, aError);
	};

	/**
	 * Load activities.
	 * 
	 * @returns {void}
	 */
	this.loadActivities = function() {
		console.log('odTimeTracker.loadActivities');
		$.ajax({
			dataType: 'json',
			url: odTimeTracker.getDataUrl('selectActivities'),
			data: {
				dateFrom: '2015-06-03',
				dateTo: '2015-08-06'
			}
		}).
		done(odTimeTracker.loadingActivitiesDone).
		fail(odTimeTracker.loadingActivitiesFail);
	};

	// ======================================================================
	// Below are event handlers

	// ....

	/**
	 * On application load.
	 * 
	 * @param {jQuery.Event} aEvent
	 * @returns {void}
	 */
	this.onLoad = function(aEvent) {
		console.log('odTimeTracker.onLoad');
		odTimeTracker.loadActivities();
	};

	/**
	 * On application load.
	 * 
	 * @param {jQuery.Event} aEvent
	 * @returns {void}
	 */
	this.onUnload = function(aEvent) {
		console.log('odTimeTracker.onUnload');
		// ...
	};

}).apply(odTimeTracker)
