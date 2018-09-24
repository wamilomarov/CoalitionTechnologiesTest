$(document).ready(function() {
    $("#add_row").on("click", function() {

        let clicked_row = $(this).closest("tr");
        let name = clicked_row.find("input[name='name']");
        let quantity = clicked_row.find("input[name='quantity']");
        let price = clicked_row.find("input[name='price']");
        $.ajax({
            url: "/api/products",
            type: 'POST',
            data: {
                name: name.val(),
                quantity: quantity.val(),
                price: price.val()
            },
            success: function (result) {
                $(".invalid-feedback").remove(); //remove previous messages if exist
                if (result.status === 402)
                {
                    $.each(result.data.messages, function (index, value) { // append error messages if returned
                        $("input[name='" + index + "']")
                            .after("<span class='invalid-feedback' role='alert' style='display: block !important;'>" +
                                "<strong>" + value[0] + "</strong></span>");
                    });
                }
                else if (result.status === 200)
                {
                    let first_row = $("tbody").children("tr:first");
                    let product = result.data;
                    name.val('');
                    quantity.val(0);
                    price.val(0);

                    first_row.before(
                        "<tr>" +
                        "<td>" + product.name + "</td>" +
                        "<td>" + product.quantity + "</td>" +
                        "<td>" + product.price + "</td>" +
                        "<td>" + product.created_at + "</td>" +
                        "<td>" + product.price * product.quantity + "</td>" +
                        "<td>" +
                        "<button class=\"btn btn-sm btn-warning \"><i class=\"fa fa-edit\" data-toggle=\"modal\" data-target=\"#edit_modal\"\n" +
                        "                                    data-name=" + product.name + " data-quantity=" + product.quantity +
                        "                                    data-price=" + product.price + " data-id=" + product.id + "></i></button>" +
                        "<button class=\"btn btn-sm btn-danger \"><i class=\"fa fa-remove\"></i></button>" +
                        "</td>" +
                        "</tr>"
                    ) // append before the first one because the newest one should be the first
                }
            }
        });

    });

    $("#edit_modal").on("show.bs.modal", function (event) { // dynamic modal which gets values after clicking on the row
        let button = $(event.relatedTarget);
        let clicked_row = button.closest("tr");
        let id = button.data('id');
        let name = button.data('name');
        let quantity = button.data('quantity');
        let price = button.data('price');
        let modal = $(this);
        modal.find("input[name='name']").val(name);
        modal.find("input[name='price']").val(price);
        modal.find("input[name='quantity']").val(quantity);
        modal.find("input[name='id']").val(id);

        let submit = modal.find("#save");
        submit.on("click", function () {
            $.ajax({
                url: "/api/products/",
                type: 'PATCH',
                data: {
                    name: modal.find("input[name='name']").val(),
                    quantity: modal.find("input[name='quantity']").val(),
                    price: modal.find("input[name='price']").val(),
                    id: id
                },
                success: function (result) {
                    $(".invalid-feedback").remove();
                    if (result.status === 402)
                    {
                        $.each(result.data.messages, function (index, value) {
                            $("input[name='" + index + "']")
                                .after("<span class='invalid-feedback' role='alert' style='display: block !important;'>" +
                                    "<strong>" + value[0] + "</strong></span>");
                        });
                    }
                    else if (result.status === 200)
                    {
                        $("#edit_modal").modal('hide');
                        let product = result.data;
                        clicked_row.find(".name").text(product.name);
                        clicked_row.find(".price").text(product.price);
                        clicked_row.find(".quantity").text(product.quantity);
                        clicked_row.find(".total").text(product.quantity * product.price);
                        button.data('name', product.name);
                        button.data('price', product.price);
                        button.data('quantity', product.quantity);

                    }
                }
            });
        });
    });

    $("#remove_modal").on("show.bs.modal", function (event) {
        let button = $(event.relatedTarget);
        let clicked_row = button.closest("tr");
        let id = button.data('id');
        let name = button.data('name');
        let modal = $(this);
        modal.find("p").text("Are you sure to delete " + name + "?");

        let submit = modal.find("#delete");
        submit.on("click", function () {
            $.ajax({
                url: "/api/products/",
                type: 'DELETE',
                data: {
                    id: id
                },
                success: function (result) {
                    $(".invalid-feedback").remove();
                    if (result.status === 402)
                    {
                        $.each(result.data.messages, function (index, value) {
                            $("p")
                                .after("<span class='invalid-feedback' role='alert' style='display: block !important;'>" +
                                    "<strong>" + value[0] + "</strong></span>");
                        });
                    }
                    else if (result.status === 200)
                    {
                        $("#remove_modal").modal('hide');
                        clicked_row.remove();
                    }
                }
            });
        });
    });
});