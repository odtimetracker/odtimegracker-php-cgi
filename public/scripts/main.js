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
   * @type Object Holds options for SummerNote WYSIWYG editor.
   */
  summernoteOpts: {
    height: 110,
    minHeight: 80,
    maxHeight: 200,
    focus: false
  },

	/**
	 * Add given projects to the cache.
	 * @param {Object} aProjects
	 * @returns {odTimeTracker}
	 */
	cacheProjects: function(aProjects) {
		for (var projectId in aProjects) {
			if (aProjects.hasOwnProperty(projectId)) {
				odTimeTracker.projects[projectId] = aProjects[projectId];
			}
		}
		return odTimeTracker;
	}, // end cacheProjects(aProjects)

  /**
   * @deprecated
   * @returns {odTimeTracker}
   */
  clearAndCloseStartActivityForm: function() {
		console.log('odTimeTracker.clearAndCloseStartActivityForm');
    console.log('> DEPRECATED! Use `odTimeTracker.activityFormModalClear` instead.')
    return odTimeTracker.activityFormModalClear();
  }, // end clearAndCloseStartActivityForm()

	/**
	 * Displays message to the user.
	 * @param {String} msg
	 * @param {String} type ['error','info','success']
	 * @returns {odTimeTracker}
	 * @todo Make user messages closable!
	 */
	displayUserMessage: function(msg, type) {
		//console.log('odTimeTracker.displayUserMessage', msg, type);
		type = type.toLowerCase()
		switch (type) {
			case 'error':
			case 'info':
			case 'success':
				// These are correct
				break;
			default:
				type = 'info';
				break;
		}
		jQuery('<div>')
			.addClass('row')
			.addClass('alert')
			.addClass('alert-' + type)
			.css('margin-bottom', '0')
			.css('padding', '20px 45px 20px 45px')
			.attr('role', 'alert')
			.html(msg)
			.appendTo('#content');
		return odTimeTracker;
	}, // end displayUserMessage(aMessage, aType)

	/**
	 * @param {String} aName
	 * @returns {Number|Boolean} Returns `FALSE` if project is not found.
	 */
	getCachedProjectByName: function(aName) {
		//console.log('odTimeTracker.getCachedProjectByName');
		for (var project in odTimeTracker.projects) {
			if (project.Name.toLowerCase().trim() === aName.toLowerCase().trim()) {
				return project.ProjectId;
			}
		}
		return false;
	}, // end getCachedProjectByName(aName)

	/**
	 * Create HTML for an activity.
	 * 
	 * @param {Object} aActivity
	 * @param {HtmlElement} aParent Parent element.
	 * @param {Boolean} aSkipRunning
	 * @returns {odTimeTracker}
	 */
	createActivityHtml: function(aActivity, aParent, aSkipRunning) {
		//console.log('odTimeTracker.createActivityHtml', aActivity, aParent, aSkipRunning);
		if (aSkipRunning === true && (aActivity.Stopped === null || aActivity.Stopped === '')) {
			return odTimeTracker;
		}

		var html = '' +
			'<div id="activity_{ACTIVITY_ID}" class="row activity-card">' +
				'<div class="col-md-12">' +
					'<div class="col-md-10 left-part">' +
						'<h3>{ACTIVITY_NAME} <small>{ACTIVITY_DATES}</small></h3>' +
						'<div class="activity-meta btn-toolbar" role="toolbar" aria-label="...">' +
							'<div class="btn-group btn-group-xs" role="group" aria-label="Projekt přiřazený aktivitě">' +
								'<span class="label label-info" aria-expanded="false">{PROJECT_NAME}</span>' +
							'</div>' +
							'<div class="btn-group btn-group-xs activity-tags-group" role="group" aria-label="Tagy přiřazené aktivitě">' +
								'{ACTIVITY_TAGS}' +
							'</div>' +
						'</div>' +
						'<div class="description">{ACTIVITY_DESC}</div>' +// hidden
					'</div>' +
					'<div class="col-md-2 right-part">' +
						'<h4 class="duration">{ACTIVITY_DURATION}</h4>' +
						'<span class="btn-group btn-group-xs" role="group" aria-label="Ovládací prvky aktivity">' +
							'<span class="btn btn-default btn-renew-activity" title="Znovu zahájit"><span class="glyphicon glyphicon-repeat"></span></span>' +
							'<span class="btn btn-default btn-edit-activity" title="Editovat"><span class="glyphicon glyphicon-pencil"></span></span>' +
							'<span class="btn btn-default btn-trash-activity" title="Přesunout do koše"><span class="glyphicon glyphicon-trash"></span></span>' +
						'</span>' +
					'</div>' +
				'</div>' +
			'</div>';

		// ActivityId
		html = html.replace('{ACTIVITY_ID}', aActivity.ActivityId);

		// Activity name
		var activityName = aActivity.Name;
		if (aActivity.Stopped === '' || aActivity.Stopped === null) {
			activityName += ' <small><span class="label label-warning">Aktivní</span></small>';
		}
		html = html.replace('{ACTIVITY_NAME}', activityName);

		// Activity description
		var activityDesc = aActivity.Description;
		if (activityDesc === '' || activityDesc === null) {
		//	activityDesc = '<small>Tato aktivita nemá popis...</small>';
			activityDesc = '';
		}
		html = html.replace('{ACTIVITY_DESC}', activityDesc);

		// Project
		html = html.replace('{PROJECT_NAME}', odTimeTracker.projects[aActivity.ProjectId].Name);
		html = html.replace('{PROJECT_MENU_ID}', 'projectMenu_' + aActivity.ActivityId);

		// Tags
		var tagsHtml = '';
		var tags = aActivity.Tags.split(',');
		for (var i=0; i<tags.length; i++) {
			tagsHtml += odTimeTracker.createTagHtml(tags[i], 'tagMenu_' + aActivity.ActivityId + '_' + i);
		}
		tagsHtml += '';
		html = html.replace('{ACTIVITY_TAGS}', tagsHtml);

		// Duration
		html = html.replace('{ACTIVITY_DURATION}', aActivity.DurationFormatted);

		// Activity created/stopped dates
		var datesHtml = aActivity.StartedFormatted.split(' ')[1] + ' - ';

		if (aActivity.Stopped === null || aActivity.Stopped === '') {
			//console.log('is not stopped');
			datesHtml += '<button type="button" class="btn btn-danger btn-xs" onclick="odTimeTracker.stopActivity(event);">Zastavit</button>';
		}
		else if (
			aActivity.IsWithinOneDay === true && 
			(aActivity.Stopped !== null || aActivity.Stopped !== '')
		) {
			//console.log('is within one day');
			datesHtml += aActivity.StoppedFormatted.split(' ')[1];
		}
		else {
			//console.log('is not within one day');
			datesHtml += aActivity.StoppedFormatted;
		}

		html = html.replace('{ACTIVITY_DATES}', datesHtml);

		// Append HTML to the parent
		jQuery(html).appendTo(aParent);

    jQuery('#activity_' + aActivity.ActivityId + ' .btn-edit-activity').click(function() {
      odTimeTracker.activityFormModalShow({ mode: 'edit', activity: aActivity });
    });

		return odTimeTracker;
	}, // createActivityHtml(aActivity, aParent, aSkipRunning)

	/**
	 * Clears activity form.
	 * @returns {odTimeTracker}
	 */
	activityFormModalClear: function() {
    jQuery('#formActivity_ActivityId').val('');
    jQuery('#formActivity_ProjectId').val('');
    jQuery('#formActivity_Name').val('');
    jQuery('#formActivity_ProjectName').val('');
    jQuery('#formActivity_Tags').val('');
    jQuery('#formActivity_Description').val('');
    return odTimeTracker;
	}, // end activityFormModalClear()

	/**
	 * Clears project form.
	 * @returns {odTimeTracker}
	 */
  projectFormModalClear: function() {
    jQuery('#formProject_ProjectId').val('');
    jQuery('#formProject_Name').val('');
    jQuery('#formProject_Tags').val('');
    jQuery('#formProject_Description').val('');
    return odTimeTracker;
  }, // end projectFormModalClear()

  /**
   * Shows activity form.
   * @param {object} aOptions
   * @returns {void}
   */
  activityFormModalShow: function(aOptions) {
    if (!('mode' in aOptions)) {
      aOptions.mode = 'add';
    }

    if (aOptions.mode === 'edit' && !('activity' in aOptions)) {
      console.log('There is passed no activity to edit. Switching to "add" mode.')
      aOptions.mode = 'add';
    }

    if (!('ActivityId' in aOptions.activity)) {
      console.log('There is passed no activity to edit. Switching to "add" mode.')
      aOptions.mode = 'add';
    }

    if (aOptions.mode === 'add') {
      odTimeTracker.activityFormModalClear();
    } else {
      var project = odTimeTracker.projects[aOptions.activity.ProjectId];
      jQuery('#formActivity_ActivityId').val(aOptions.activity.ActivityId);
      jQuery('#formActivity_ProjectId').val(('ProjectId' in project) ? project.Name : '');
      jQuery('#formActivity_Name').val(aOptions.activity.Name);
      jQuery('#formActivity_ProjectName').val(('Name' in project) ? project.Name : '');
      jQuery('#formActivity_Tags').val(aOptions.activity.Tags);
      jQuery('#formActivity_Description').val(aOptions.activity.Description);
    }

    jQuery('#editActivityModal').modal('show');
  }, // end activityFormModalShow(aOptions)

  /**
   * Hides activity form.
   * @returns {void}
   */
  activityFormModalHide: function() {
    jQuery('#editActivityModal').modal('hide');
  }, // end activityFormModalHide(aOptions)

  /**
   * Shows project form.
   * @param {Object} aOptions
   * @returns {void}
   */
  projectFormModalShow: function(aOptions) {
    if (!('mode' in aOptions)) {
      aOptions.mode = 'add';
    }

    if (aOptions.mode === 'edit' && !('project' in aOptions)) {
      console.log('There is passed no project to edit. Switching to "add" mode.')
      aOptions.mode = 'add';
    }

    if (!('ActivityId' in aOptions.activity)) {
      console.log('There is passed no project to edit. Switching to "add" mode.')
      aOptions.mode = 'add';
    }

    if (aOptions.mode === 'add') {
      odTimeTracker.projectFormModalClear();
    } else {
      var project = odTimeTracker.projects[aOptions.activity.ProjectId];
      jQuery('#formProject_ProjectId').val(('ProjectId' in project) ? project.Name : '');
      jQuery('#formProject_Name').val(aOptions.project.Name);
      jQuery('#formProject_Tags').val(''/*aOptions.project.Tags*/);
      jQuery('#formProject_Description').val(aOptions.project.Description);
    }

    jQuery('#editProjectModal').modal('show');
  }, // end projectFormModalShow(aOptions)

  /**
   * Hides project form.
   * @returns {void}
   */
  projectFormDialogHide: function() {
    jQuery('#editProjectModal').modal('hide');
  }, // end projectFormDialogHide(aOptions)

	/**
	 * Create HTML for heading to section with activities list.
	 * @param {String} aId
	 * @param {String} aLabel
	 * @param {String} aDescription
	 * @param {HtmlElement} aParent Parent element.
	 * @returns {odTimeTracker}
	 */
	createActivitiesHeaderHtml: function(aId, aLabel, aDescription, aParent) {
		//console.log('odTimeTracker.createActivitiesHeaderHtml', aId, aLabel, aDescription, aParent);
		jQuery(
			'<div id="' + aId + '" class="page-header row">' +
				'<h2>' + aLabel + '<small>' + aDescription + '</small></h2>' +
			'</div>'
		).appendTo(aParent);
		return odTimeTracker;
	}, // end createActivitiesHeaderHtml

	/**
	 * Helper method that returns HTML for single activity tag.
	 *
	 * @param {String} aTagName
	 * @param {String} aTagMenuId
	 * @return {String}
	 */
	createTagHtml: function(aTagName, aTagMenuId) {
		return '<span class="label label-default" aria-expanded="false">' + aTagName + '</span>';
	}, // end createTagHtml(aTagName, aTagMenuId)

	/**
	 * Retrieve URL for given method.
	 * 
	 * @param {String} aMethod
	 * @returns {String}
	 * @todo URL definitively should not be hard-coded!
	 */
	getDataUrl: function(aMethod) {
		var url = 'http://odtimetracker.local/json/' + aMethod;
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
	 * @param {Date} aDate
	 * @returns {String}
	 */
	formatDateRFC3339: function(aDate) {
		console.log('odTimeTracker.formatDateRFC3339', aDate);
		console.log('>', 'XXX Implement `odTimeTracker.formatDateRFC3339`!');
	}, // end formatDateRFC3339(aDate)

	/**
	 * Load activities.
	 * @param {object} aParams
	 * @returns {odTimeTracker}
	 */
	loadActivities: function(aParams) {
		//console.log('odTimeTracker.loadActivities', aParams);

		if (!('dateFrom' in aParams)) {
			//aParams.dateFrom = false;
			aParams.dateFrom = new Date();
			aParams.dateFrom.setDate(aParams.dateFrom.getDate() - 1);
		}

		if (!('dateTo' in aParams)) {
			//aParams.dateTo = false;
			aParams.dateTo = new Date();
		}

		if (!('limit' in aParams)) {
			aParams.limit = false;
		}

		if (!('projects' in aParams)) {
			aParams.projects = false;
		}

		if (!('tags' in aParams)) {
			aParams.tags = false;
		}

		aParams.dateFrom = odTimeTracker.formatDateYmd(aParams.dateFrom);
		aParams.dateTo = odTimeTracker.formatDateYmd(aParams.dateTo);

		jQuery
			.ajax({
				dataType: 'json',
				url: odTimeTracker.getDataUrl('selectActivities'),
				data: aParams
			})
			.done(function (aData) {
				odTimeTracker
					.processServerResponseCommon(aData)
					.processServerResponseLoadActivities(aData, aParams);
			});

		return odTimeTracker;
	}, // end loadActivities(aParams)

	/**
	 * Load activities for given count of days back from today minus `aBack`.
	 * @param {Integer} aBack
	 * @param {Integer} aCount
	 * @returns {odTimeTracker}
	 */
	loadActivitiesDaysBack: function(aBack, aCount) {
		//console.log('odTimeTracker.loadActivitiesDaysBack', aBack, aCount);
		var params = { dateFrom: new Date(), dateTo: new Date() };
		params.dateFrom.setDate(params.dateFrom.getDate() - (aBack + aCount));
		params.dateTo.setDate(params.dateTo.getDate() - aBack);
		//console.log('>', params);
		return odTimeTracker.loadActivities(params);
	}, // end loadActivitiesDaysBack(aBack, aCount)

	/**
	 * Load activities that were started today.
	 * @deprecated
	 * @returns {odTimeTracker}
	 */
	loadTodayActivities: function() {
		console.log('odTimeTracker.loadTodayActivities');
    console.log('> DEPRECATED! Use `odTimeTracker.loadActivitiesDaysBack(0, 1)` instead.')
		return odTimeTracker.loadActivitiesDaysBack(0, 1);
	}, // end loadTodayActivities()

	/**
	 * Load activities that were started yesterday.
	 * @deprecated
	 * @returns {odTimeTracker}
	 */
	loadYesterdayActivities: function() {
		console.log('odTimeTracker.loadYesterdayActivities');
    console.log('> DEPRECATED! Use `odTimeTracker.loadActivitiesDaysBack(1, 1)` instead.')
		return odTimeTracker.loadActivitiesDaysBack(1, 1);
	}, // end loadYesterdayActivities()

	/**
	 * Load activities that were started day before yesterday.
	 * @deprecated
	 * @returns {odTimeTracker}
	 */
	loadDayBeforeYesterdayActivities: function() {
		console.log('odTimeTracker.loadDayBeforeYesterdayActivities');
    console.log('> DEPRECATED! Use `odTimeTracker.loadActivitiesDaysBack(2, 1)` instead.')
		return odTimeTracker.loadActivitiesDaysBack(2, 1);
	}, // end loadYesterdayActivities()

	/**
	 * @param {Object} aData
	 * @returns {odTimeTracker}
	 * @todo All messages should be in JSON in single array! This should also
	 *       allow more user messages at once...
	 */
	processServerResponseCommon: function(aData) {
		//console.log('odTimeTracker.processServerResponseCommon', aData);
		if ('errorMessage' in aData) {
			odTimeTracker.displayUserMessage(aData.errorMessage, 'error');
		}

		if ('message' in aData) {
			odTimeTracker.displayUserMessage(aData.message, 'info');
		}

		if ('projects' in aData) {
			odTimeTracker.cacheProjects(aData.projects);
		}

		return odTimeTracker;
	}, // end processServerResponse(aData)

	/**
	 * @param {Object} aData
	 * @param {Object} aParams
	 * @returns {odTimeTracker}
   * @todo Use `odTimeTracker.displayUserMessage('...', 'error');`?
   * @todo Maybe be would better just message "No activities found..."
	 */
	processServerResponseLoadActivities: function(aData, aParams) {
		console.log('odTimeTracker.processServerResponseLoadActivities', aData, aParams);
		if (!('activities' in aData)) {
			console.log('>', 'ERROR', 'No `activities` found in given data!');
			return odTimeTracker;
		}

		if (!Array.isArray(aData.activities)) {
			console.log('>', 'ERROR', 'Property `data.activities` is not an array!');
			return odTimeTracker;
		}

		var headerId = 'activitiesHeader_activities' + aParams.dateFrom.replace('-', '');
		var headerTitle = 'Aktivity ze dne ' + aParams.dateFrom + '.';
		var content = document.getElementById('content');

		odTimeTracker.createActivitiesHeaderHtml(headerId, aParams.dateFrom, headerTitle, content);

		if (aData.activities.length === 0) {
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
			i < aData.activities.length;
			odTimeTracker.createActivityHtml(aData.activities[i++], content, true)
		);
		return odTimeTracker;
	}, // end processServerResponseLoadActivities(aData, aParams)

	/**
	 * @param {Object} aData
	 * @param {DOMElement} aContent
	 * @returns {odTimeTracker}
	 * @todo Remove `content` parameter!
	 */
	processServerResponseRunningActivity: function(aData, aContent) {
		//console.log('odTimeTracker.initRunningActivityArea', aData, aContent);
		if (!('runningActivity' in aData)) {
			return odTimeTracker.initRunningActivityAreaForm();
		}
		// Note: This is only for to be sure. But still when is there
		// no running activity there should be no `runningActivity`
		// property in the data.
		if (aData.runningActivity === null) {
			return odTimeTracker.initRunningActivityAreaForm();
		}
		return odTimeTracker.createActivityHtml(
			aData.runningActivity,
			aContent,
			false
		);
	}, // end processServerResponseRunningActivity(data)

	/**
	 * Select currently running activity.
	 * @deprecated
	 * @returns {void}
	 */
	selectRunningActivity: function() {
		console.log('odTimeTracker.selectRunningActivity');
    console.log('> DEPRECATED!')
		jQuery.ajax({
			dataType: 'json',
			url: odTimeTracker.getDataUrl('selectRunningActivity')
		}).
		done(function (data) {
			//console.log('odTimeTracker.selectRunningActivity().done', data/*, status, req*/);

			if ('errorMessage' in data) {
				console.log('Error:', data.errorMessage);
				return;
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
		fail(function (/*req, status, err*/) {
			console.log('odTimeTracker.selectRunningActivity().fail'/*, req, status, err*/);
		});
	}, // end selectRunningActivity()

	/**
	 * Submit start activity form.
	 * @param {HTMLFormElementPrototype} aForm
	 * @returns {void}
	 */
	submitStartActivityForm: function(aForm) {
		console.log('odTimeTracker.submitStartActivityForm');
		console.log(aForm);
		var dataArr = jQuery(aForm).serializeArray();
		console.log(dataArr);
		// TODO project.Name => project.ProjectId ...
		
		jQuery.ajax({
			dataType: 'json',
			'type': 'POST',
			url: odTimeTracker.getDataUrl('startActivity'),
			data: dataArr
		}).
		done(function (data) {
			// TODO Show message in small pop-up notification area.

			if ('runningActivity' in data) {
				// TODO Just update HTML using JavaScript.
				location.reload();
			}

			odTimeTracker.clearAndCloseStartActivityForm();
		});
	}, // end submitStartActivityForm(aForm)
	
	/**
	 * Stop activity.
	 * @returns {void}
	 */
	stopActivity: function() {
		jQuery.ajax({
			dataType: 'json',
			url: odTimeTracker.getDataUrl('stopRunningActivity')
		}).
		done(function (data) {
      // TODO Reload just when activity was really stopped!
			// TODO This will be replaced by reloading just displayed data!
			location.reload();
		});
	}, // end stopActivity(aEvent)

	/**
	 * Init projects autocomplete.
	 * @returns {odTimeTracker}
	 */
	initProjectsAutocomplete: function() {
		console.log('odTimeTracker.initprojectsAutocomplete');
		jQuery.ajax({
			dataType: 'json',
			url: odTimeTracker.getDataUrl('selectProjects')
		}).
		done(function (data) {
      odTimeTracker
        .processServerResponseCommon()
        ._initProjectsAutocomplete();
		});
    return odTimeTracker;
	}, // end initProjectsAutocomplete()

	/**
   * @private
	 * @returns {odTimeTracker}
	 */
	_initProjectsAutocomplete: function() {
		console.log('odTimeTracker.initProjectsAutocompleteFinalize');
		var ac = jQuery('#newActivity_ProjectId').autocomplete({
			source: function(req, add) {
				var suggestions = [];
				for (var projectId in odTimeTracker.projects) {
					var project = odTimeTracker.projects[projectId];
					if (req.term.toLowerCase().indexOf(project.Name.toLowerCase()) === 0) {
						suggestions.push(project);
					}
				}
				add(suggestions);
			},
			select: function(event, ui) {
				event.preventDefault();
				//create formatted friend
				var friend = ui.item.Name;
				var span = jQuery('<span>').text(friend);
        var a = jQuery("<a>")
						.addClass('remove')
            .attr({ href: 'javascript:', title: 'Remove ' + friend })
            .text('x')
            .appendTo(span);

        span.insertBefore('#newActivity_ProjectId');

				jQuery('.my-autocomplete').hide();
				jQuery('#newActivity_ProjectId').hide();
      },
      change: function(event, ui) {
				console.log('change', ui);
				event.preventDefault();
      },
      close: function(event, ui) {
				console.log('close', ui);
				event.preventDefault();
      }
		});
		ac.autocomplete('instance')._renderMenu = function(ul, items) {
			var that = this;
			$.each(items, function(index, item) {
				that._renderItemData(ul, item);
			});
			jQuery(ul).addClass('list-group');//'list-group-item'
			jQuery(ul).css('padding', '0 0 0 0');
			jQuery(ul).find('li:odd').addClass('odd');
		};
		ac.autocomplete('instance')._renderItem = function(ul, item) {
			return jQuery('<li>')
				.attr('data-ProjectId', item.ProjectId)
				.addClass('list-group-item')
				.append(
					'<h4 class="list-group-item-heading">' + item.Name + '</h4>' +
					'<p class="list-group-item-text">' +
						(item.Description ? item.Description : '') +
					'</p>'
				)
				.appendTo(ul);
		};
		ac.autocomplete('instance')._resizeMenu = function() {
			this.menu.element.outerWidth(jQuery('#newActivity_ProjectId').width() + 25);
			jQuery(this.menu.element).outerHeight(450);
			jQuery(this.menu.element).css('overflow', 'auto');
			//jQuery(this.menu.element).css('min-height', '480px');
			//jQuery(this.menu.element).css('overflow', 'auto');
			jQuery(this.menu.element).addClass('my-autocomplete');
		};

		jQuery('#newActivity_ProjectId').click(function() {
			ac.autocomplete('search', jQuery('#newActivity_ProjectId').val());
		});

    return odTimeTracker;
	}, // end _initProjectsAutocomplete()

	/**
	 * Initializes "Running activity" item.
	 * @returns {odTimeTracker}
	 */
	initRunningActivityArea: function() {
		//console.log('odTimeTracker.initRunningActivityArea');
		jQuery.ajax({
			dataType: 'json',
			url: odTimeTracker.getDataUrl('selectRunningActivity')
		}).
		done(function (data) {
			var content = document.getElementById('content');
			odTimeTracker
					.createActivitiesHeaderHtml(
						'activitiesHeader_runningActivity',
						'Aktuální aktivita',
						'Aktivita, na které nyní pracujete.',
						content
					)
					.processServerResponseCommon(data)
					.processServerResponseRunningActivity(data, content);
		});
		return odTimeTracker;
	}, // end initRunningActivityArea()

	/**
	 * Initializes "Start new activity" form.
	 * @returns {odTimeTracker}
	 */
	initRunningActivityAreaForm: function() {
		console.log('odTimeTracker.initRunningActivityAreaForm');
    console.log('>', 'TODO Finish this!')
		// ...
		return odTimeTracker;
	}, // end initRunningActivityArea()

  /**
   * Initializes WYSIWYG areas in our forms.
   * @returns {odTimeTracker}
   */
  initWysiwygAreas: function() {
    // Ensures that SummerNote will be initialized when opening forms.
    $('#editActivityModal').on('show.bs.modal', function(aEvent) {
      //console.log('[#editActivityModal].on("show.bs.modal")', aEvent);
      var button = jQuery(aEvent.relatedTarget); // Button that triggered the modal
      var mode = button.data('mode'); // Mode should be "add"
      var desc = '';

      if (mode === 'add') {
        odTimeTracker.activityFormModalClear();
      } else {
        desc = jQuery('#formActivity_Description').val();
      }

      jQuery('#formActivity_Description')
          .summernote(odTimeTracker.summernoteOpts)
          .code(desc);
    });
    $('#editProjectModal').on('show.bs.modal', function(aEvent) {
      //console.log('[#editProjectModal].on("show.bs.modal")', aEvent);
      var button = jQuery(aEvent.relatedTarget); // Button that triggered the modal
      var mode = button.data('mode'); // Mode should be "add"
      var desc = '';

      if (mode === 'add') {
        odTimeTracker.projectFormModalClear();
      } else {
        desc = jQuery('#formProject_Description').val();
      }

      jQuery('#formProject_Description')
          .summernote(odTimeTracker.summernoteOpts)
          .code(desc);
    });
    // Destroy SummerNote immediately after forms are closed
    $('#editActivityModal').on('hidden.bs.modal', function(aEvent) {
      //console.log('[#editActivityModal].on("hidden.bs.modal")', aEvent);
      $('#formActivity_Description').destroy();
    });
    $('#editProjectModal').on('hidden.bs.modal', function(aEvent) {
      //console.log('[#editProjectModal].on("hidden.bs.modal")', aEvent);
      jQuery('#formProject_Description').destroy();
    });
    return odTimeTracker;
  } // end initWysiwygAreas()
}; // End of odTimeTracker

// ============================================================================
// Initialize odTimeTracker
jQuery(window).load(function () {
	odTimeTracker
    .initWysiwygAreas()
    .initRunningActivityArea()
    //.loadActivities({ limit: 15 });
    //.loadActivitiesDaysBack(0, 5);
    .loadTodayActivities()
    .loadYesterdayActivities()
    .loadDayBeforeYesterdayActivities();
});