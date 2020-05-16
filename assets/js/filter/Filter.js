'use strict';
import $ from 'jquery';
import 'bootstrap-multiselect';
import Routing from '../common/Routing';

class Filter {

    static resetFilter(filterName, data = []) {
        $('#'+filterName).multiselect('dataprovider', data);
    }

    // common ajax function to load the dynamic items
    static ajaxRequest(url, postData, target) {
        $.ajax({
            url: Routing.generate(url),
            data: postData,
            type: 'POST',
            success: function (data) {
                 $('#' + target).multiselect('dataprovider', JSON.parse(data));
            }
        });
    }


}

export default Filter;