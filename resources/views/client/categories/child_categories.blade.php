@php
    $quote .= '-';
    $quote_array = strlen($quote);
    $fw = ($quote_array == 1) ? 600 : 400;
@endphp

@foreach($subcategories as $subcategory)
    <option value="{{ $subcategory->id }}" style="font-weight: {{ $fw }}" {{ ($par_cat_id == $subcategory->id) ? 'selected' : '' }}> &nbsp;{{ $quote }} {{ $subcategory[$name_key] }}</option>
    @if(count($subcategory->subcategories) > 0)
        @include('client.categories.child_categories',['subcategories' => $subcategory->subcategories,'par_cat_id'=>$par_cat_id])
    @endif
@endforeach
