<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\DivisionController::class, 'update'], [$division->id]), 'method' => 'PUT', 'id' => 'division_edit_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('lang_v1.edit_division')</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __('lang_v1.division_name') . ':*') !!}
          {!! Form::text('name', $division->name, ['class' => 'form-control', 'required', 'placeholder' => __('lang_v1.division_name')]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('description', __('brand.short_description') . ':') !!}
          {!! Form::text('description', $division->description, ['class' => 'form-control', 'placeholder' => __('brand.short_description')]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.update')</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div>
</div>
