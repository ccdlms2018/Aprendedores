/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 274);
/******/ })
/************************************************************************/
/******/ ({

/***/ 274:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(275);


/***/ }),

/***/ 275:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


function PopulatePspSingleShortcode() {

	jQuery('.psp-loading').show();

	data = { action: 'psp_get_projects' };

	jQuery.post(ajaxurl, data, function (response) {

		response = response.slice(0, -1);

		jQuery('#psp-single-project-list').html(response);
		jQuery('.psp-loading').hide();
	});
}

function InsertPspProject() {

	pspId = jQuery('#psp-single-project-id').val();
	pspStyle = jQuery('input[name="psp-display-style"]:checked').val();

	if (pspStyle == 'full') {

		pspOverview = jQuery('#psp-single-overview').val();
		if (pspOverview.length) {
			pspOverviewAtt = 'overview="' + pspOverview + '"';
		}

		pspMilestones = jQuery('#psp-single-milestones').val();
		if (pspMilestones.length) {
			pspMilestonesAtt = 'milestones="' + pspMilestones + '"';
		}

		pspPhases = jQuery('#psp-single-phases').val();
		if (pspPhases.length) {
			pspPhasesAtt = 'phases="' + pspPhases + '"';
		}

		pspTasks = jQuery('#psp-single-tasks').val();
		if (pspTasks.length) {
			pspTasksAtt = 'tasks="' + pspTasks + '"';
		}

		pspProgress = jQuery('#psp-single-progress').val();
		if (pspProgress.length) {
			pspProgressAtt = 'progress="' + pspProgress + '"';
		}

		shortcode = '[project_status id="' + pspId + '" ' + pspProgressAtt + ' ' + pspOverviewAtt + ' ' + pspMilestonesAtt + ' ' + pspPhasesAtt + ' ' + pspTasksAtt + ']';
	} else {

		pspPart = jQuery('#psp-part-display').val();

		if (pspPart == 'overview') {

			shortcode = '[project_status_part id="' + pspId + '" display="overview"]';
		} else if (pspPart == 'documents') {

			shortcode = '[project_status_part id="' + pspId + '" display="documents"]';
		} else if (pspPart == 'progress') {

			pspPartStyle = jQuery('#psp-part-overview-progress-select').val();
			shortcode = '[project_status_part id="' + pspId + '" display="progress" style="' + pspPartStyle + '"]';
		} else if (pspPart == 'phases') {

			pspPartStyle = jQuery('#psp-part-phases-select').val();
			shortcode = '[project_status_part id="' + pspId + '" display="phases" style="' + pspPartStyle + '"]';
		} else if (pspPart == 'tasks') {

			pspPartStyle = jQuery('#psp-part-tasks-select').val();
			shortcode = '[project_status_part id="' + pspId + '" display="tasks" style="' + pspPartStyle + '"]';
		}
	}

	tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);

	tb_remove();return false;
}

function InsertPspProjectList() {

	pspListTax = jQuery('#psp-project-taxonomy').val();
	pspListStatus = jQuery('#psp-project-status').val();
	pspUserAccess = jQuery('#psp-user-access').val();
	pspCount = jQuery('#psp-project-count').val();
	pspSort = jQuery('#psp-project-sort').val();

	if (pspUserAccess == 'on') {
		pspAccess = 'user';
	} else {
		pspAccess = 'all';
	}

	shortcode = '[project_list type="' + pspListTax + '" status="' + pspListStatus + '" access="' + pspAccess + '" count="' + pspCount + '" sort="' + pspSort + '" ]';

	tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);

	tb_remove();return false;
}

/***/ })

/******/ });