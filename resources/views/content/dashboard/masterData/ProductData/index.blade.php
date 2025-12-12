@extends('layouts.master')

@push('vendor-style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('vendor-script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            var table = $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('products.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'KdProduct',
                        name: 'KdProduct'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'price',
                        render: function(data) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#productForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('products.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Data produk berhasil diproses.', 'success');
                        resetForm();
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal memproses data. Periksa inputan Anda.',
                            'error');
                    }
                });
            });
        });

        function resetForm() {
            $('#productForm')[0].reset();
            $('#product_id').val('');
            $('.btn-submit').text('Submit');
        }

        function editProduct(id) {
            $.get("/products-edit/" + id, function(data) {
                $('#product_id').val(data.KdProduct);
                $('#name').val(data.name);
                $('#stock').val(data.stock);
                $('#price').val(data.price);
                $('.btn-submit').text('Update');
            });
        }

        function deleteProduct(id) {
            Swal.fire({
                title: 'Hapus data ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/products-delete/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            $('#productTable').DataTable().ajax.reload();
                            Swal.fire('Terhapus!', 'Data telah dihapus.', 'success');
                        }
                    });
                }
            });
        }
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Produk</h5>
                </div>
                <div class="card-body">
                    <form id="productForm">
                        @csrf
                        <input type="hidden" name="product_id" id="product_id">

                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="Masukkan nama produk" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stok Barang</label>
                            <input type="number" name="stock" id="stock" class="form-control"
                                placeholder="Masukkan jumlah stok" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input type="number" name="price" id="price" class="form-control"
                                placeholder="Masukkan harga jual" required>
                        </div>

                        <div class="text-end gap-2 d-flex justify-content-end">
                            <button type="button" onclick="resetForm()" class="btn btn-light">Batal</button>
                            <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Stok Barang</h5>
                </div>
                <div class="card-body">
                    <table id="productTable"
                        class="table table-bordered dt-responsive nowrap table-striped align-middle w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
