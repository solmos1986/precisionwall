/////////load images/////////////
$(document).on("click", ".upload_image", function () {
    $("#uploadModal .modal-title").text(`Upload images ${$(this).data("image")}`);
    $("#uploadModal").modal("show");
    $("#uploadModal .modal-body").html("");
    $("#uploadModal .modal-body").html(`<div class="file-loading">
          <input id="images" name="images[]" type="file" accept="image/*" multiple>
        </div>`);
    fileinput_images($(this).data("id"), "images", $(this).data("image"), "goal");
});
////show delete///
$(document).on("click", ".delete", function () {
    var id = $(this).data("id");
    $("#deleteModal #delete_button").data("id", id);
    $("#deleteModal").modal("show");
});
//// delete////
$(document).on("click", "#delete_button", function () {
    $.ajax({
        type: "DELETE",
        url: `${base_url}/delete/${$(this).data('id')}/goal`,
        dataType: "json",
        success: function (data) {
            var html = "";
            if (data.success) {
                html = `<div class="alert alert-success">${data.success}</div>`;
                table.draw();
                $("#status_crud").html(html);
                $("#status_crud").addClass("visible").removeClass("invisible");
                $("#deleteModal").modal("hide");
            }
        },
    });
});
