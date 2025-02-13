<div id="materialModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Material</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <span class="form_result"></span>
          <form id="material">
            <div class="form-group">
              <label>Name: </label>
              <input type="text" name="nombre_m" id="nombre_m" class="form-control" />
            </div>
            <div class="form-group">
                <label>Unit of measurement (maximum 5 letters): </label>
                <input type="text" name="unit_m" id="unit_m" class="form-control" />
            </div>
            <div class="form-group">
                <label>Unit price (optional): </label>
                <input type="text" name="price_m" id="price_m" class="form-control" />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success btn-sm" id="guardar_material">Save</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>