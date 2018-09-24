<div class="modal" tabindex="-1" role="dialog" id="edit_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="">
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <input type="text" value="" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="number" step="1" value="" name="quantity" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="number" step="0.1" value="" name="price" class="form-control">
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save" >Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>