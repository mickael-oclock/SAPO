/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../scss/paper-dashboard.scss'

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
const $ = require('jquery');
require('bootstrap');
require ('moment');
require ('bootstrap-switch');
require ('datatables.net-bs4');
import './plugins/chartjs.min';
import './paper-dashboard';
console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
