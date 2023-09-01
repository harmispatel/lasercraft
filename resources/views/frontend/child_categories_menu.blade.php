@php
    $class_name = ($parent_key <= 1) ? "dropdown-menu" : "submenu dropdown-menu";
    $parent_key += 1;
@endphp

<ul class="{{ $class_name }}">
    @foreach($subcategories as $subcategory)
        <li>
            <a class="dropdown-item" href="#">{{ $subcategory[$name_key] }} {{ (count($subcategory->subcategories) > 0) ? '»' : ''; }} </a>
            @if(count($subcategory->subcategories) > 0)
                @include('frontend.child_categories_menu',['subcategories' => $subcategory->subcategories,'parent_key'=>$parent_key])
            @endif
        </li>
    @endforeach
</ul>
