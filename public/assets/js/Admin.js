import {AjaxButton} from './Library/AjaxButton.js';
import {AjaxForm} from "./Library/AjaxForm.js";

let selector;

if ((selector = document.querySelector('#features-container'))) {
    (new AjaxButton(selector, '.evt-delete-feature', '/admin/api/feature/delete/')).addEvents();
}

if ((selector = document.querySelector('#changelog-container'))) {
    (new AjaxButton(selector, '.evt-delete-change', '/admin/api/changelog/delete/')).addEvents();
}

if ((selector = document.querySelector('#user-applications-container'))) {
    (new AjaxButton(selector, '.evt-accept', '/admin/api/user/approve/')).addEvents();
}

if ((selector = document.querySelector('#user-applications-container'))) {
    (new AjaxButton(selector, '.evt-refuse', '/admin/api/user/refuse/')).addEvents();
}