'use strict';

import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-bs/js/dataTables.bootstrap.min';
import 'datatables.net-buttons/js/buttons.html5.js';

class TableHtml {

    /**
     * Switch function, to call this function, args must have chartType.type key
     * @param args
     */
    static tableHtml(args) {
        let type = args.chartType.type;
        switch (type) {
            case "table":
                this.table(args);
                break;
            case "html":
                this.htmlInfo(args);
                break;
        }
    }
    /**
     * Container should always be an Id
     * @param args {renderTo, data, setting}
     */
    static table(args = {renderTo, data, setting : {}}) {
        this.htmlInfo(args);
        // Table inside the container
        $('#'+args.renderTo+' table').DataTable(args.setting);
    }

    /**
     * Set HTML Text to container
     * @param args {renderTo, data}
     */
    static htmlInfo(args = {renderTo, data}) {
        $('#'+args.renderTo).html(args.data);
    }

    /**
     * @param args
     */
    static rawData(args = {renderTo, data}) {
        $('#'+args.renderTo).val(args.data);
    }

}

export default TableHtml;