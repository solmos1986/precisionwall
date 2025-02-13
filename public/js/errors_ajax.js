function error_status(jqXHR) {
    switch (jqXHR.status) {
        case 419:
            Swal.fire({
                icon: 'error',
                title: 'Expired session',
                html: 'Please reload the page'
            });
            break;
        case 401:
            Swal.fire({
                icon: 'error',
                title: 'Expired session',
                html: 'Please reload the page'
            });
            break;
        default:
            Swal.fire({
                icon: 'error',
                title: 'An error occurred on the server',
                html: 'error',
            });
            break;
    }
}
function fail() {
    Swal.fire({
        icon: 'error',
        title: 'An error occurred',
        html: '',
    });
}