'use strict';

import $ from 'jquery';

// import the required CCS
// select 2 to be replaced by bootstrap, that's why imported first
import 'admin-lte/plugins/iCheck/icheck.min';

$(function () {

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });

});