$(document).ready(function () {
    var url1 = 'http://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/FullMoon2010.jpg/631px-FullMoon2010.jpg',
        url2 = 'http://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Earth_Eastern_Hemisphere.jpg/600px-Earth_Eastern_Hemisphere.jpg';
    // the file input
    var $el4 = $('.imagenes'), initPlugin = function () {
        $el4.fileinput({
            theme: "fas",
            allowedFileExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv','heic'],
            uploadUrl: ``,
            uploadAsync: true,
            showUpload: false,
            overwriteInitial: false,
            minFileCount: 1,
            maxFileCount: 4,
            browseOnZoneClick: true,
            initialPreviewAsData: true,
            showRemove: true,
            showClose: false,
            browseClass: "btn btn-sm btn-success",
            initialPreview: [],
            initialPreviewConfig: [],
    
        });

    };
    // initialize plugin
    initPlugin();
    $el4.fileinput('disable');

    // `disable` and `enable` methods
    $(".btn-disable").on('click', function () {
        var $btn = $(this);
        if (!$el4.data('fileinput')) {
            initPlugin();
        }
        if ($el4.attr('disabled')) {
            $el4.fileinput('enable');
            $btn.html('Disable').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            $el4.fileinput('disable');
            $btn.html('Enable').removeClass('btn-secondary').addClass('btn-primary');
        }
    });

    // `destroy` method
    $(".btn-destroy").on('click', function () {
        if ($el4.data('fileinput')) {
            $el4.fileinput('destroy');
        }
    });

    // recreate plugin after destroy
    $(".btn-recreate").on('click', function () {
        if ($el4.data('fileinput')) {
            return;
        }
        initPlugin();
    });

    // refresh plugin with new options 
    $(".btn-refresh").on('click', function () {
        if (!$el4.data('fileinput')) {
            // just normal init when plugin is not initialized
            $el4.fileinput({ previewClass: 'bg-info' });
        } else {
            // refresh already initialized plugin with new options
            $el4.fileinput('refresh', { previewClass: 'bg-info' });
        }
    });

    // clear/reset the file input
    $(".btn-clear").on('click', function () {
        $el4.fileinput('clear');
    });
});