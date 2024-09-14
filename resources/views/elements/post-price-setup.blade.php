<div class="modal fade" tabindex="-1" role="dialog" id="post-set-price-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title p-2">{{__('Set post price')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-2">
                    <p>{{__('Paid posts are locked for subscribers as well.')}}</p>
                </div>
                <div class="input-group p-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
                    </div>
                    <input id="post-price" type="number" class="form-control" name="text" required  placeholder="{{__('Post price')}}" value="{{$postPrice}}">
                    <span class="invalid-feedback" role="alert">
                        <strong class="post-price-error min-error d-none">{{__('The price must be between :min and :max.',['min' => getSetting('payments.min_ppv_post_price') ?? 1, 'max' => getSetting('payments.max_ppv_post_price') ?? 500])}}</strong>
                        <strong class="post-price-error ppv-error d-none">{{__('Posts having an expire date can not be price locked.')}}</strong>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="border btn-round btn btn-white"   onclick="PostCreate.clearPostPrice()">{{__('Cancel')}}</button>
                <button type="button" class="btn-round btn btn-primary" onclick="PostCreate.savePostPrice()">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>
