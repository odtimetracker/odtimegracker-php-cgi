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
var odTimeTracker = {
	/**
	 * @type Object Holds projects.
	 */
	projects: {},

	/**
	 * Add given projects to the cache.
	 *
	 * @param {Object} aProjects
	 * @returns {void}
	 */
	cacheProjects: function(aProjects) {
		for (var projectId in aProjects) {
			if (aProjects.hasOwnProperty(projectId)) {
				odTimeTracker.projects[projectId] = aProjects[projectId];
			}
		}
	}, // end cacheProjects(aProjects)

	/**
	 * Helper method that clears start activity form and closes its dropdown.
	 * 
	 * @returns {void}
	 */
	clearAndCloseStartActivityForm: function() {
		jQuery('#newActivity_Name').val('');
		jQuery('#newActivity_ProjectId').val('');
		jQuery('#newActivity_Tags').val('');
		jQuery('#newActivity_Description').val('');
		jQuery('#addActivityDropdown').dropdown('toggle');
	}, // end clearAndCloseStartActivityForm()

	/**
	 * Create HTML for an activity.
	 * 
	 * @param {Object} aActivity
	 * @param {HtmlElement} aParent Parent element.
	 * @param {Boolean} aSkipRunning
	 * @returns {void}
	 */
	createActivityHtml: function(aActivity, aParent, aSkipRunning) {
console.log(aActivity);		
		if (aSkipRunning === true && (aActivity.Stopped === null || aActivity.Stopped === '')) {
			return;
		}

		var html = '' +
			'<div id="activity_' + aActivity.ActivityId + '" class="row activity-card">' +
				'<div class="col-md-12">' +
					'<div class="col-md-8 left-part">' +
						'<h3>{ACTIVITY_NAME}</h3>' +
						'<div class="activity-meta">' +
							'<span role="presentation" class="dropdown">' +
								'<button id="{PROJECT_MENU_ID}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="btn btn-default btn-xs dropdown-toggle">' +
									'<span>{PROJECT_NAME} <span class="caret"></span>' + 
								'</button>' +
								'<ul aria-labelledby="{PROJECT_MENU_ID}" class="dropdown-menu">' +
									'<li role="presentation"><a href="#">Změnit projekt</a></li>' +
									'<li role="separator" class="divider"></li>' +
									'<li role="presentation"><a href="#">Upravit projekt</a></li>' +
									'<li role="separator" class="divider"></li>' +
									'<li role="presentation"><a href="#">Filtr...</a></li>' +
								'</ul>' +
							'</span>{ACTIVITY_TAGS}' +
						'</div>' +
						'<p class="description">{ACTIVITY_DESC}</p>' +
					'</div>' +
					'<div class="col-md-4 right-part">' +
						'<h4>{ACTIVITY_DURATION}</h4>' +
						'<span>{ACTIVITY_DATES}</span>' +
					'</div>' +
				'</div>' +
			'</div>';

		// Activity name
		var activityName = aActivity.Name;
		if (aActivity.Stopped === '' || aActivity.Stopped === null) {
			activityName += ' <small><span class="label label-warning">Aktivní</span></small>';
		}
		html = html.replace('{ACTIVITY_NAME}', activityName);

		// Activity description
		var activityDesc = aActivity.Description;
		if (activityDesc === '' || activityDesc === null) {
			activityDesc = '<small>Tato aktivita nemá popis...</small>';
		}
		html = html.replace('{ACTIVITY_DESC}', activityDesc);

		// Project
		html = html.replace('{PROJECT_NAME}', odTimeTracker.projects[aActivity.ProjectId].Name);
		html = html.replace('{PROJECT_MENU_ID}', 'projectMenu_' + aActivity.ActivityId);

		// Tags
		var tagsHtml = '';
		var tags = aActivity.Tags.split(',');
		for (var i=0; i<tags.length; i++) {
			tagsHtml += odTimeTracker.createTagHtml(
				tags[i],
				'tagMenu_' + aActivity.ActivityId + '_' + i
			) + ((i + 1 < tags.length) ? ' ' : '');
		}
		html = html.replace('{ACTIVITY_TAGS}', (tagsHtml === '') ? '' : ' ' + tagsHtml);


		// Duration
		html = html.replace('{ACTIVITY_DURATION}', aActivity.DurationFormatted);

		// Activity created/stopped dates
		var datesHtml = aActivity.StartedFormatted + ' - ';

		if (aActivity.Stopped === null || aActivity.Stopped === '') {
			console.log('is not stopped');
			datesHtml += '<button type="button" class="btn btn-danger btn-xs" onclick="odTimeTracker.stopActivity(event);">Zastavit</button>';
		}
		else if (
			aActivity.IsWithinOneDay === true && 
			(aActivity.Stopped !== null || aActivity.Stopped !== '')
		) {
			console.log('is within one day');
			datesHtml += aActivity.StoppedFormatted.split(' ')[1];
		}
		else {
			console.log('is not within one day');
			datesHtml += aActivity.StoppedFormatted;
		}

		html = html.replace('{ACTIVITY_DATES}', datesHtml);

		// Append HTML to the parent
		jQuery(html).appendTo(aParent);
	}, // createActivityHtml(aActivity, aParent, aSkipRunning)

	/**
	 * Create HTML for heading to section with activities list.
	 * 
	 * @param {String} aId
	 * @param {String} aLabel
	 * @param {String} aDescription
	 * @param {HtmlElement} aParent Parent element.
	 * @returns {void}
	 */
	createActivitiesHeaderHtml: function(aId, aLabel, aDescription, aParent) {
		jQuery(
			'<div id="' + aId + '" class="page-header row">' +
				'<h2>' + aLabel + '<small>' + aDescription + '</small></h2>' +
			'</div>'
		).appendTo(aParent);
	}, // end createActivitiesHeaderHtml

	/**
	 * Helper method that returns HTML for single activity tag.
	 *
	 * @return {String}
	 */
	createTagHtml: function(aTagName, aTagMenuId) {
		var html = '' +
			'<span role="presentation" class="dropdown">' +
				'<button id="{TAG_MENU_ID}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="btn btn-info btn-xs dropdown-toggle">' +
					'{TAG_NAME} <span class="caret"></span>' +
				'</button>' +
				'<ul aria-labelledby="{TAG_MENU_ID}" class="dropdown-menu">' +
					'<li role="presentation"><a href="#">Upravit tag</a></li>' +
					'<li role="separator" class="divider"></li>' +
					'<li role="presentation"><a href="#">Odstranit projekt</a></li>' +
					'<li role="separator" class="divider"></li>' +
					'<li role="presentation"><a href="#">Filtr...</a></li>' +
				'</ul>' +
			'</span>';
		html = html.replace('{TAG_NAME}', aTagName);
		html = html.replace('{TAG_MENU_ID}', aTagMenuId);
		return html;
	}, // end createTagHtml(aTagName, aTagMenuId)

	/**
	 * Retrieve URL for given method.
	 * 
	 * @param {String} aMethod
	 * @returns {String}
	 */
	getDataUrl: function(aMethod) {
		var url = 'http://odtimetracker.local/json/' + aMethod;
		console.log('odTimeTracker.getDataUrl', url);
		return url;	
	}, // end getDataUrl()

	/**
	 * Returns given date as string formatted `Y-m-d'.
	 *
	 * @param {Date} aDate
	 * @returns {String}
	 */
	formatDateYmd: function(aDate) {
		var month = aDate.getMonth() + 1;
		var date = aDate.getDate();
		return aDate.getFullYear() + '-' +
			((month < 10) ? '0' + month : month) + '-' +
			((date < 10) ? '0' + date : date);
	}, // end formatDateYmd(aDate)

	/**
	 * Returns given date as string formatted by RFC3339.
	 *
	 * @param {Date} aDate
	 * @returns {String}
	 */
	formatDateRFC3339: function(aDate) {
		console.log('XXX Implement `odTimeTracker.formatDateRFC3339`!');
	}, // end formatDateRFC3339(aDate)

	/**
	 * Load activities.
	 *
	 * @param {String} aDateFrom
	 * @param {String} aDateTo
	 * @returns {void}
	 */
	loadActivities: function(aDateFrom, aDateTo) {
		console.log('odTimeTracker.loadActivities', aDateFrom, aDateTo);
		var reqUrl = odTimeTracker.getDataUrl('selectActivities');
		var reqData = { dateFrom: aDateFrom, dateTo: aDateTo };
		console.log(reqData);
		jQuery.ajax({ dataType: 'json', url: reqUrl, data: reqData }).
		done(function (data, status, req) {
			console.log('odTimeTracker.loadActivities().done', data/*, status, req*/);

			if ('errorMessage' in data) {
				console.log('Error:', data.errorMessage);
			}

			if (!('activities' in data)) {
				console.log('Error:', 'No `activities` found in given data!');
				return;
			}

			if (!Array.isArray(data.activities)) {
				console.log('Error:', 'Property `data.activities` is not an array!');
				return;
			}

			if ('projects' in data) {
				odTimeTracker.cacheProjects(data.projects);
			}

			var headerId = 'activitiesHeader_activities' + aDateFrom.replace('-', '');
			var headerTitle = 'Aktivity ze dne ' + aDateFrom + '.';
			var content = document.getElementById('content');

			odTimeTracker.createActivitiesHeaderHtml(headerId, aDateFrom, headerTitle, content);

			if (data.activities.length === 0) {
				var html = '' + 
					'<div class="row">' + 
						'<div class="col-md-12">' + 
							'<p class="description">' +
								'V daný den nebyly uloženy žádné aktivity...' +
							'</div>' +
						'</div>' +
					'</div>';
				jQuery(html).appendTo('#' + headerId);
				return;
			}
			
			// Print all activities
			for (
				var i = 0;
				i < data.activities.length;
				odTimeTracker.createActivityHtml(data.activities[i++], content, true)
			);
		}).
		fail(function (req, status, err) {
			console.log('odTimeTracker.loadActivities().fail', req, status, err);
		});
	}, // end loadActivities(aDateFrom, aDateTo)

	/**
	 * Load activities that were started today.
	 *
	 * @returns {void}
	 */
	loadTodayActivities: function() {
		var day1 = new Date();
		var day2 = new Date();
		day2.setDate(day2.getDate() + 1);

		odTimeTracker.loadActivities(
			odTimeTracker.formatDateYmd(day1),
			odTimeTracker.formatDateYmd(day2)
		);
	}, // end loadTodayActivities()

	/**
	 * Load activities that were started yesterday.
	 *
	 * @returns {void}
	 */
	loadYesterdayActivities: function() {
		var day1 = new Date();
		day1.setDate(day1.getDate() - 1);
		var day2 = new Date();
		day2.setDate(day2.getDate());

		odTimeTracker.loadActivities(
			odTimeTracker.formatDateYmd(day1),
			odTimeTracker.formatDateYmd(day2)
		);
	}, // end loadYesterdayActivities()

	/**
	 * Select currently running activity.
	 * 
	 * @returns {void}
	 */
	selectRunningActivity: function() {
		console.log('odTimeTracker.selectRunningActivity');
		jQuery.ajax({
			dataType: 'json',
			url: odTimeTracker.getDataUrl('selectRunningActivity')
		}).
		done(function (data, status, req) {
			console.log('odTimeTracker.selectRunningActivity().done', data/*, status, req*/);

			if ('errorMessage' in data) {
				console.log('Error:', data.errorMessage);
			}

			if (!('runningActivity' in data)) {
				console.log('There is no running activity!');
				return;
			}

			// TODO Toto je tu pouze docasne - kdyz neni aktivita, tak 
			//      by `runningActivity` vubec nemela v JSONu byt!
			if (data.runningActivity === null) {
				console.log('There is no running activity!');
				return;
			}

			if ('projects' in data) {
				odTimeTracker.cacheProjects(data.projects);
			}

			var content = document.getElementById('content');
			odTimeTracker.createActivitiesHeaderHtml(
				'activitiesHeader_runningActivity',
				'Aktuální aktivita',
				'Aktivita, na které nyní pracujete.',
				content
			);
			odTimeTracker.createActivityHtml(
				data.runningActivity, 
				content, 
				false
			);
		}).
		fail(function (req, status, err) {
			console.log('odTimeTracker.selectRunningActivity().fail'/*, req, status, err*/);
		});
	}, // end selectRunningActivity()

	/**
	 * Submit start activity form.
	 *
	 * @param {HTMLFormElementPrototype} aForm
	 * @returns {void}
	 */
	submitStartActivityForm: function(aForm) {
		console.log('odTimeTracker.submitStartActivityForm');
		console.log(aForm);
		var dataArr = $(aForm).serializeArray();
		console.log(dataArr);
		
		jQuery.ajax({
			dataType: 'json',
			'type': 'POST',
			url: odTimeTracker.getDataUrl('startActivity'),
			data: dataArr
		}).
		done(function (data, status, req) {
			console.log('odTimeTracker.submitStartActivityForm().done', data/*, status, req*/);
			// ...
			odTimeTracker.clearAndCloseStartActivityForm();
		}).
		fail(function (req, status, err) {
			console.log('odTimeTracker.submitStartActivityForm().fail'/*, req, status, err*/);
			odTimeTracker.clearAndCloseStartActivityForm();
		});
	}, // end submitStartActivityForm(aForm)
	
	/**
	 * Stop activity.
	 */
	stopActivity: function(aEvent) {
		console.log('odTimeTracker.stopActivity');

		jQuery.ajax({
			dataType: 'json',
			url: odTimeTracker.getDataUrl('stopRunningActivity')
		}).
		done(function (data, status, req) {
			console.log('odTimeTracker.stopActivity().done', data/*, status, req*/);
			// ...
			// TODO This will be replaced by reloading just displayed data!
			location.reload();
			//odTimeTracker.clearAndCloseStartActivityForm();
		}).
		fail(function (req, status, err) {
			console.log('odTimeTracker.stopActivity().fail'/*, req, status, err*/);	
		});
	}, // end stopActivity(aEvent)

	// ======================================================================
	// Below are event handlers

	/**
	 * On application load.
	 * 
	 * @param {jQuery.Event} aEvent
	 * @returns {void}
	 */
	onLoad: function(aEvent) {
		console.log('odTimeTracker.onLoad');

		// Load currently running activity
		odTimeTracker.selectRunningActivity();

		// Load today activities
		odTimeTracker.loadTodayActivities();

		// Load yesterday activities
		odTimeTracker.loadYesterdayActivities();

		// Load day before yesterday activities
		var day1 = new Date();
		day1.setDate(day1.getDate() - 2);
		var day2 = new Date();
		day2.setDate(day2.getDate() - 1);
		
		odTimeTracker.loadActivities(
			odTimeTracker.formatDateYmd(day1),
			odTimeTracker.formatDateYmd(day2)
		);

		// Load day before+before yesterday activities
		var day3 = new Date();
		day3.setDate(day3.getDate() - 3);
		var day4 = new Date();
		day4.setDate(day4.getDate() - 2);
		
		odTimeTracker.loadActivities(
			odTimeTracker.formatDateYmd(day3),
			odTimeTracker.formatDateYmd(day4)
		);

		// Load day before+before yesterday activities
		var day5 = new Date();
		day5.setDate(day5.getDate() - 4);
		var day6 = new Date();
		day6.setDate(day6.getDate() - 3);
		
		odTimeTracker.loadActivities(
			odTimeTracker.formatDateYmd(day5),
			odTimeTracker.formatDateYmd(day6)
		);

		// Event handlers for start activity form
		/*jQuery('#startActivityForm').on('reset', function (event) {
			event.preventDefault();
			odTimeTracker.clearAndCloseStartActivityForm();
		});
		jQuery('#startActivityForm').on('submit', function (event) {
			event.preventDefault();
			odTimeTracker.submitStartActivityForm(this);
		});*/
	}, // end onLoad(aEvent)

	/**
	 * On application load.
	 * 
	 * @param {jQuery.Event} aEvent
	 * @returns {void}
	 */
	onUnload: function(aEvent) {
		console.log('odTimeTracker.onUnload');
		// ...
	} // end onUnload(aEvent)

}; // End of odTimeTracker
