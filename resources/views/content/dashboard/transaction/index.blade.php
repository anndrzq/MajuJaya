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
                if (!str) return 0;
                return parseFloat(str.replace(/[^0-9]/g, "")) || 0;
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
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
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
                let stock = parseInt(option.data('stock')) || 0;
                let qtyInput = row.find('.qty');
                let qty = parseInt(qtyInput.val()) || 0;

                if (qty > stock) {
                    Swal.fire('Stok Kurang', 'Sisa stok: ' + stock, 'warning');
                    qtyInput.val(stock);
                    qty = stock;
                }

                let subtotal = price * qty;
                row.find('.price_display').val(formatRupiah(price));
                row.find('.subtotal_display').val(formatRupiah(subtotal));
                row.find('.subtotal_raw').val(subtotal);
                calculateTotal();
            });

            $('.addRow').click(function() {
                let row = $('#productTable tbody tr:first').clone();
                row.find('input').val('');
                row.find('.qty').val(1);
                row.find('button').removeClass('btn-primary').addClass('btn-danger').text('x').removeClass(
                    'addRow').addClass('removeRow');
                row.find('.select2-container').remove();
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
                $('.subtotal_raw').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total_price_display').val(formatRupiah(total));
                $('#total_price_raw').val(total);
                updateChange();
            }

            function updateChange() {
                let pay = parseNumber($('#pay_amount').val());
                let total = parseFloat($('#total_price_raw').val()) || 0;
                let change = pay - total;
                $('#change_amount_display').val(formatRupiah(change > 0 ? change : 0));
            }

            $('#pay_amount').on('keyup', function() {
                let pay = parseNumber($(this).val());
                $(this).val(formatRupiah(pay));
                updateChange();
            });

            $('#cashierForm').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnSubmit');
                btn.prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: "{{ route('sale.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
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
                                'Terjadi kesalahan.'
                        });
                    }
                });
            });
        });
    </script>
@endpush

@section('title', 'Transaksi')

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
                                                        {{ $p->name }} (Stok: {{ $p->stock }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control price_display" readonly></td>
                                        <td><input type="number" name="qty[]" class="form-control qty" value="1"
                                                min="1"></td>
                                        <td>
                                            <input type="text" class="form-control subtotal_display" readonly>
                                            <input type="hidden" class="subtotal_raw">
                                        </td>
                                        <td><button type="button" class="btn btn-primary addRow">+</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4 offset-md-8">
                                <div class="mb-2">
                                    <label>Total Harga</label>
                                    <input type="text" id="total_price_display" class="form-control" readonly>
                                    <input type="hidden" name="total_price" id="total_price_raw">
                                </div>
                                <div class="mb-2">
                                    <label>Bayar</label>
                                    <input type="text" name="pay_amount" id="pay_amount" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label>Kembalian</label>
                                    <input type="text" id="change_amount_display" class="form-control" readonly
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
