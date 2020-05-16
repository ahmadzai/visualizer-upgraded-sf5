import swal from 'sweetalert2';

class Alerts {

    static warning(message) {
        this.popup(message, 'warning', 'Warning!')
    }

    static error(message) {
        this.popup(message, 'error', 'Error!')
    }

    static success(message) {
        this.popup(message, 'success', 'Success!')
    }

    static info(message) {
        this.popup(message, 'info', 'Info!')
    }

    static popup(message, type, title) {
        swal(
            {
                type: type,
                title: title,
                text: message,
                showConfirmButton: false,
                timer: 2000
            }
        );
    }

    static clusterInfo() {
        swal({
            title: '<strong>Guidance!</strong>',
            type: 'info',
            html:
                'To see cluster level trends of any district please select a district first.' +
                'To select a district, you have to select a province first. ' +
                'When you select the province and district then click ' +
                '<span class="btn btn-xs bg-warning"><i class="fa fa-filter"></i> Filter</span> button.' +
                'The page will automatically populate',
            showCloseButton: false,
            showCancelButton: false,
            focusConfirm: true,
            confirmButtonText:
                '<i class="fa fa-thumbs-up"></i> Got it!',
            confirmButtonAriaLabel: 'Got it!',
        })
    }

    static filterInfo() {
        swal({
            title: '<strong>Attention!</strong>',
            type: 'info',
            html:
                'The current dashboard is already filtered. <br>' +
                'Change the Filter and then click Filter button',
            showCloseButton: false,
            showCancelButton: false,
            focusConfirm: true,
            confirmButtonText:
                '<i class="fa fa-thumbs-up"></i> Got it!',
            confirmButtonAriaLabel: 'Got it!',
        })
    }

    static ajaxError(error) {
        swal({
            title: '<strong>Network Error!</strong>',
            type: 'error',
            html:error,
            showCloseButton: false,
            showCancelButton: false,
            focusConfirm: true,
            confirmButtonText:
                '<i class="fa fa-thumbs-down"></i> Tray Later!',
            confirmButtonAriaLabel: 'Try again!',
        })
    }
}

export default Alerts;