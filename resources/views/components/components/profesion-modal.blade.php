<div id="profesionModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add type of worker</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <span class="form_result"></span>
          <form id="profesion">
            <div class="form-group">
              <label>Name: </label>
              <input type="text" name="nombre_p" id="nombre_p" class="form-control" />
            </div>
            <div class="form-group">
                <label>Description: </label>
                <textarea name="descripcion_p" id="descripcion_p" class="form-control"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success btn-sm" id="guardar_profesion">Save</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>