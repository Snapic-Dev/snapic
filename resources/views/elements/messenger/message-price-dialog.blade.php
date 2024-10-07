<div class="modal fade" tabindex="-1" role="dialog" id="message-set-price-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title p-2">{{__('Set message price')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="p-2">{{__('Paid messages are locked for subscribers as well. Text is not locked, only media.')}}</p>
                <div class="input-group p-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
                    </div>
                    <input id="message-price" type="number" class="form-control" name="text" required  placeholder="{{__('Post price')}}" value="5" min="1" max="100">
                    <span class="invalid-feedback" role="alert">
                        <strong>{{__('The price must be between :min and :max.',['min' => getSetting('payments.min_ppv_message_price') ?? 1, 'max' => getSetting('payments.max_ppv_message_price') ?? 500])}}</strong>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-round btn-white" onclick="messenger.clearMessagePrice()">{{__('Close')}}</button>
                <button type="button" class="btn btn-round btn-primary" onclick="messenger.saveMessagePrice()">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
