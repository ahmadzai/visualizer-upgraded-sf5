'use strict';

import $ from 'jquery'
import 'bootstrap-multiselect';
import MainFilter from './filter/MainFilter';
import FilterListener from './filter/FilterListener';



$(document).ready(function () {

    // intitalize filter
    new MainFilter();
    let listener = new FilterListener();

    let filterData = listener.listenMain();

});