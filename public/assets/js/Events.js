import {Navigation} from "./Library/Navigation.js";
import {AjaxForm} from "./Library/AjaxForm.js";

let selector;

if((selector = document.querySelector('.evt-nav'))) {
    new Navigation(selector);
}

if((selector = document.querySelector('.ajax-form'))) {
    (new AjaxForm(selector)).addEvents();
}