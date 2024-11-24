<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <div class="input-group">
          <span class="input-group-btn">
            <a id="lfm2" data-input="thumbnail2" data-preview="holder2" class="btn btn-primary text-white">
              Choose
            </a>
          </span>
            <input id="thumbnail2" class="form-control" type="text" name="filepath">
        </div>
    </div>

</x-dynamic-component>
