import '../css/app.css';
import 'jquery';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import 'flatpickr';
import { French } from 'flatpickr/dist/l10n/fr.js';
import "./elements/blog/Comment.js"
import "./elements/Alert"

require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
require('flatpickr/dist/themes/dark.css');

const $ = require('jquery');

// Flatpickr extension
flatpickr(".flatpickr", {
    altInput: true,
    altFormat: "d/m/Y",
    dateFormat: "Y-m-d",
    locale: French
})
