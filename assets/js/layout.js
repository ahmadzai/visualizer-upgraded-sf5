'use strict';

import $ from 'jquery';
// import the required CCS
// select 2 to be replaced by bootstrap, that's why imported first
import 'admin-lte/bower_components/select2/dist/css/select2.min.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'font-awesome/css/font-awesome.css';
import 'admin-lte/bower_components/Ionicons/css/ionicons.min.css';
import 'bootstrap-multiselect/dist/css/bootstrap-multiselect.css';
import 'admin-lte/dist/css/AdminLTE.min.css';
import 'admin-lte/dist/css/skins/skin-blue.min.css';
import 'admin-lte/dist/css/skins/skin-yellow-light.min.css';
import 'admin-lte/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css';
import 'admin-lte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';
import 'datatables.net-bs/css/dataTables.bootstrap.min.css';
import 'datatables.net-buttons-dt/css/buttons.dataTables.min.css';
import 'admin-lte/plugins/iCheck/square/blue.css';
import '../css/style.css';
import 'sweetalert2/dist/sweetalert2.min.css';

import 'bootstrap';
import 'bootstrap/js/popover';
import 'admin-lte/bower_components/jquery-slimscroll/jquery.slimscroll.min';
import 'admin-lte/bower_components/select2/dist/js/select2.min';
import 'admin-lte/bower_components/bootstrap-datepicker/js/bootstrap-datepicker';
import 'admin-lte/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min';
import 'admin-lte/dist/js/adminlte.min';
import 'datatables.net';
import 'datatables.net-bs/js/dataTables.bootstrap.min';
import 'datatables.net-buttons/js/buttons.html5.js';

$(function () {

    // enable tooltip for the at the application level
    $('[data-toggle="tooltip"]').tooltip({ boundary: 'window' });

    // enable bootstrap popover, if there's one in the page
    $("[data-toggle=popover]").each(function(i, obj) {

        $(this).popover({
            boundary: 'window',
            html: true,
            content: function() {
                let id = $(this).attr('id');
                return $('#popover-content-' + id).html();
            }
        });

    });
    // initialize select2
    $('.select2').select2();

    // enable datatable
    let table = $('.dash-dataTable');
    let tableSetting = table.data('setting');
    if(tableSetting === null || tableSetting === undefined) {
        tableSetting = {}
    }
    table.DataTable(tableSetting);

    // enable date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd'
    });

    // enable color picker
    $('.color-picker').colorpicker();

    // enable modal window show in the upload page
    $('#modal-default').modal('show');

    // ------------------ Fix the Tree Menu -----------------------------------
    var checkElement = $('.menu-open .treeview-menu');
    if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
        //Get the parent menu
        var parent = checkElement.parents('ul').first();
        //Close all open menus within the parent
        var ul = parent.find('ul:visible').slideUp(500);
        //Remove the menu-open class from the parent
        ul.removeClass('menu-open');
        //Get the parent li
        var parent_li = checkElement.parent("li");

        //Open the target menu and add the menu-open class
        checkElement.slideDown(500, function () {
            //Add the class active to the parent li
            checkElement.addClass('menu-open');
            //parent.find('li.active').removeClass('active');
            parent_li.addClass('active');
            //Fix the layout in case the sidebar stretches over the height of the window
            //_this.layout.fix();
        });
    }
    // ------------------ end of tree menu --------------------------------------

});