@extends('layouts.master')

@push('vendor-style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('page-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function parseNumber(str) {
                return parseFloat(str.replace(/[^\d]/g, "")) || 0;
            }

            function initSelect2() {
                $('.selectProduct').select2({
                    placeholder: "Pilih Produk",
                    allowClear: true,
                    width: '100%'
                });
            }

            initSelect2();

            function updateProductOptions() {
                let selectedValues = [];
                $('.selectProduct').each(function() {
                    let val = $(this).val();
                    if (val) selectedValues.push(val);
                });

                $('.selectProduct').each(function() {
                    let currentSelect = $(this);
                    let currentValue = currentSelect.val();

                    currentSelect.find('option').each(function() {
                        let optionValue = $(this).val();
                        if (optionValue !== "" && selectedValues.includes(optionValue) &&
                            optionValue !== currentValue) {
                            $(this).prop('disabled', true).css('color', '#ccc');
                        } else {
                            $(this).prop('disabled', false).css('color', '');
                        }
                    });
                });
            }

            $(document).on('change', '.selectProduct, .qty', function() {
                let row = $(this).closest('tr');

                if ($(this).hasClass('selectProduct')) {
                    updateProductOptions();
                }

                let option = row.find('.selectProduct option:selected');
                let price = parseFloat(option.data('price')) || 0;
                let qty = parseInt(row.find('.qty').val()) || 0;
                let stock = parseInt(option.data('stock')) || 0;

                if (qty > stock) {
                    Swal.fire('Stok Kurang', 'Sisa stok: ' + stock, 'warning');
                    row.find('.qty').val(stock);
                    qty = stock;
                }

                let subtotal = price * qty;
                row.find('.price').val(formatRupiah(price));
                row.find('.subtotal').val(formatRupiah(subtotal));
                calculateTotal();
            });

            $('.addRow').click(function() {
                let row = $('#productTable tbody tr:first').clone();
                row.find('input').val('');
                row.find('.qty').val(1);
                row.find('button').removeClass('btn-primary').addClass('btn-danger').text('x').removeClass(
                    'addRow').addClass('removeRow');

                row.find('.selectProduct').next('.select2-container').remove();
                row.find('.selectProduct').val('').prop('disabled', false);

                $('#productTable tbody').append(row);
                initSelect2();
                updateProductOptions();
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                updateProductOptions();
                calculateTotal();
            });

            function calculateTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    total += parseNumber($(this).val());
                });
                $('#total_price').val(formatRupiah(total));
            }

            $('#pay_amount').on('keyup', function() {
                let pay = parseNumber($(this).val());
                $(this).val(formatRupiah(pay));
                let total = parseNumber($('#total_price').val());
                let change = pay - total;
                $('#change_amount').val(formatRupiah(change > 0 ? change : 0));
            });

            $('#cashierForm').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnSubmit');
                btn.prop('disabled', true).text('Memproses...');

                let formData = $(this).serializeArray();
                formData.forEach(function(item) {
                    if (item.name === 'pay_amount' || item.name === 'total_price') {
                        item.value = parseNumber(item.value);
                    }
                });

                $.ajax({
                    url: "{{ route('sale.store') }}",
                    method: "POST",
                    data: $.param(formData),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: true
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Simpan Transaksi');
                        let err = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: err && err.message ? err.message :
                                'Terjadi kesalahan sistem.'
                        });
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Kasir Modern</h4>
                </div>
                <div class="card-body">
                    <form id="cashierForm">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered" id="productTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Produk</th>
                                        <th>Harga</th>
                                        <th style="width: 10%;">Qty</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="KdProduct[]" class="form-select selectProduct" required>
                                                <option value="" disabled selected>Pilih Produk</option>
                                                @foreach ($products as $p)
                                                    <option value="{{ $p->KdProduct }}" data-price="{{ $p->price }}"
                                                        data-stock="{{ $p->stock }}">
                                                        {{ $p->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control price" disabled></td>
                                        <td><input type="number" name="qty[]" class="form-control qty" value="1"
                                                min="1"></td>
                                        <td><input type="text" class="form-control subtotal" disabled></td>
                                        <td><button type="button" class="btn btn-primary addRow">+</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4 offset-md-8">
                                <div class="mb-2">
                                    <label>Total Harga</label>
                                    <input type="text" id="total_price" class="form-control" disabled>
                                </div>
                                <div class="mb-2">
                                    <label>Bayar</label>
                                    <input type="text" name="pay_amount" id="pay_amount" class="form-control">
                                </div>
                                <div class="mb-2">
                                    <label>Kembalian</label>
                                    <input type="text" id="change_amount" class="form-control" disabled
                                        style="background-color: #f8f9fa;">
                                </div>
                                <button type="submit" id="btnSubmit" class="btn btn-success w-100">Simpan
                                    Transaksi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
