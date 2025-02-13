<div id="multipleMailModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send {{ $title ?? '' }} to emails</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <span id="form_result"></span>
          <form id="mail">
              <input type="hidden" name="row_id" id="row_id">
            <div class="form-group">
              <label>TO: </label>
              <input type="email" id="to" class="form-control tagsinput" data-role="tagsinput">
            </div>
            <div class="form-group">
              <label>CC: </label>
              <input type="email" id="cc" class="form-control tagsinput" data-role="tagsinput">
            </div>
            <div class="form-group">
              <label>Title Mail: </label>
              <textarea rows="3" type="text" id="title_m" class="form-control"></textarea>
            </div>
            <div class="form-group" id="data">
              <label> {{ $title ?? '' }} : </label>
              <br>
            </div>
            <div class="form-group">
              <label>Mail Body: </label>
              <textarea id="body_m" class="form-control"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success btn-sm" id="send_multiple_mail">Send Mail</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>