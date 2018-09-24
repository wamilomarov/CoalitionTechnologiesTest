@extends("layout.app")

@section("container")
    <div class="row clearfix">
        <div class="col-md-12 table-responsive">

            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Product name</th>
                    <th scope="col">Quantity in stock</th>
                    <th scope="col">Price per item</th>
                    <th scope="col">Datetime submitted</th>
                    <th scope="col">Total value number</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                @php($total = 0)
                @foreach($products as $product)
                    @php($total += $product->price * $product->quantity)
                    <tr>
                        <td class="name">{{$product->name}}</td>
                        <td class="quantity">{{$product->quantity}}</td>
                        <td class="price">{{$product->price}}</td>
                        <td>{{$product->created_at}}</td>
                        <td class="total">{{$product->price * $product->quantity}}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit_modal"
                                    data-name="{{$product->name}}" data-quantity="{{$product->quantity}}"
                                    data-price="{{$product->price}}" data-id="{{$product->id}}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#remove_modal"
                                    data-id="{{$product->id}}" data-name="{{$product->name}}">
                                <i class="fa fa-remove"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">
                        Total value:
                    </td>
                    <td>
                        {{$total}}
                    </td>

                </tr>
                <tr>
                    <td>
                        <input class="form-control" type="text" name="name" value="{{old('name')}}">
                    </td>
                    <td>
                        <input class="form-control" type="number" step="1" name="quantity" value="{{old('quantity')}}">
                    </td>
                    <td>
                        <input class="form-control" type="number" step="0.1" name="price" value="{{old('price')}}">
                    </td>
                    <td colspan="3">
                        <button class="btn btn-success" id="add_row">Add row</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    @include("partials.modals.remove")
    @include("partials.modals.edit")
@endsection